<template>
    <div id="schedule-root">
        <div id="schedule">
            <div class="schedule-header">
                <div class="schedule-day-names">
                    <div v-for="day in days" class="schedule-day-name">
                        <h4>{{ day }}</h4>
                    </div>
                </div>
            </div>
            <div class="schedule-body">
                <div class="schedule-hourLines">
                    <schedule-hour-line v-for="hourLine in scheduleHourLines" :key="hourLine.time" :time="hourLine.time"
                                        :offset="hourLine.offset"/>
                </div>
                <div class="schedule-courses">
                    <schedule-day v-for="(sections, index) in selectedSectionsByDays" :key="`section-${index}`" :sections="sections"
                                  :layoutMethods="layoutMethods"/>
                </div>
            </div>
        </div>
        <div id="class-finder">
            <h3>Class Finder</h3>
            <form class="class-finder-form">
                <label>
                    Select semester you want to plan.
                </label>
                <select id="select-semester" v-model="studentSelectedSemester">
                    <option v-for="semester in semesterList" :value="semester.id">  {{semester.name}} </option>
                </select>
            </form>

            <label v-if="studentSelectedSemester != null && studentSelectedSemester !== dateSemester.id"> Warning: semester chosen class times have not yet been published! </label>
            <button @click="selectSemester" >Select</button>

            <form class="class-finder-form">

                <label>
                    <input type="checkbox" name="showOwnClasses" v-model="showOwnClasses">
                    Show Your Own Classes
                </label>
                <br>
                <select id="available-classes" v-model="selectedCourse">
                    <option v-for="course in allCourses" v-if="!selectedCourses.includes(course)" :value="course">{{ course.slug }} - {{ course.title}}</option>
                </select>
            </form>
            <button @click="addSelectedCourse" :disabled="selectedCourse == null">Add</button>
            <hr>
            <div class="class-finder-selected">
                <schedule-added-course v-for="course in selectedCourses" :key="course.id" :course="course"
                                       :addedSectionTypes="selectedSections[course.id]"
                                       :layoutMethods="layoutMethods" @putSection="putSection"></schedule-added-course>
            </div>
        </div>
    </div>
</template>

<script>
    import ScheduleDay from "./ScheduleDay";
    import ScheduleHourLine from "./ScheduleHourLine";
    import ScheduleAddedCourse from "./ScheduleAddedCourse";
    import axios from "axios";

    export default {
        name: "Schedule",
        components: {
            ScheduleDay,
            ScheduleHourLine,
            ScheduleAddedCourse
        },
        data() {
            return {
                days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                allCourses: [],
                selectedCourse: null,
                selectedCourses: [],
                scheduleBegin: 7 * 60 + 30,
                scheduleEnd: 21 * 60 + 30,
                selectedSections: {},
                selectedSectionsByDays: [[], [], [], [], [], [], []],
                showOwnClasses: true,
                semesterId: null,
                planId: null,
                semesterList: null,
                studentSelectedSemester: null,
                dateSemester: null,


            }
        },
        computed: {
            scheduleDuration: function () {
                return this.scheduleEnd - this.scheduleBegin;
            },
            scheduleHourLines: function () {
                let ret = [];
                let begin = this.scheduleBegin + (60 - this.scheduleBegin % 60);
                let end = this.scheduleEnd - (this.scheduleEnd % 60);
                for (let i = begin; i <= end; i += 60) {
                    ret.push({
                        time: i / 60,
                        offset: this.calculateOffset(i)
                    })
                }
                return ret;
            },
            layoutMethods: function () {
                return {
                    calculateOffset: this.calculateOffset,
                    calculateHeight: this.calculateHeight,
                    formatTime: this.formatTime,
                    parseTimes: this.parseTimes,
                }
            },
            availableClasses: function () {
                return this.allCourses;
            }
        },
        methods: {
            calculateOffset: function (begin) {
                return (begin - this.scheduleBegin) / this.scheduleDuration;
            },
            calculateHeight: function (begin, end) {
                return (end - begin) / this.scheduleDuration;
            },
            parseTimes: function (section) {
                if (!section.hours.match(/\s*\d\d?\:\d\d\s*(a\.m\.|p\.m\.)?\s*\-\s*\d\d?\:\d\d\s*(a\.m\.|p\.m\.)?\s*/g)) {
                    console.error(`Unknown time format: ${section.hours}`);
                    return null;
                }
                let times = [];
                let periods = [];
                let timeRegex = /\d\d?/g;
                let periodRegex = /(a\.m\.|p\.m\.)/g;
                section.hours.match(timeRegex).forEach(time => times.push(parseInt(time)));
                section.hours.match(periodRegex).forEach(period => {
                    if (period.match('a\.m\.')) {
                        periods.push(0);
                    } else {
                        periods.push(12 * 60);
                    }
                });
                return [
                    (times[0] === 12 ? 0 : times[0]) * 60 + periods[0] + times[1],
                    (times[2] === 12 ? 0 : times[2]) * 60 + periods[periods.length - 1] + times[3]
                ]
            },
            addSelectedCourse: function () {
                if (!this.selectedCourses.includes(this.selectedCourse)) {
                    this.selectedCourses.push(this.selectedCourse);
                }
            },

            selectSemester: function(){

                this.semesterId = this.studentSelectedSemester;
                this.getAllCourses();

            },

            formatTime: function (time) {
                return `${Math.floor(time / 60)}:${('0' + time % 60).slice(-2)}`;
            },

            getAllCourses: getAllCourses,
            getCurrentSemester: getCurrentSemester,
            getSemesters: getSemesters,

            putSection: function (courseId, type, section) {
                this.selectedSections[courseId][type] = section;
                this.updateSelectedSectionsByDays();
            },
            updateSelectedSectionsByDays: function () {
                const dayMapping = {M: 0, T: 1, W: 2, U: 3, F: 4}; //Saturday and Sunday?
                let ret = [[], [], [], [], [], [], []];
                for (let key in this.selectedSections) {
                    if (!this.selectedSections.hasOwnProperty(key)) continue;
                    let sectionTypes = this.selectedSections[key];
                    if (sectionTypes) {
                        for (let sectionKey in sectionTypes) {
                            if (!sectionTypes.hasOwnProperty(sectionKey)) continue;
                            let section = sectionTypes[sectionKey];
                            if (section) {
                                for (let c of section.days) {
                                    if (c === ' ') continue;
                                    ret[dayMapping[c]].push(section);
                                }
                            }
                        }
                    }
                }
                this.selectedSectionsByDays = ret;
            }
        },

        created() {
            this.planId = document.getElementById("planId").value;
            this.getCurrentSemester();
            this.getSemesters();
        },
    }


    //gets semester course times
    function getAllCourses() {
        axios.get(`/scheduler/${this.semesterId}/sections`)
            .then((response) => {
                this.allCourses = response.data
                    .map(course => course.course)
                    .filter(course => course != null && course.sections.length > 0);
                this.allCourses.forEach(course => {
                    let courseSectionTypes = {};
                    this.selectedSections[course.id] = courseSectionTypes;
                    course.sections.forEach(section => {
                        courseSectionTypes[section.type] = null;
                    });
                });
            })
            .catch((error) => {
                site.handleError("get data", "", error);
            });
    }

    //gets students semesters in their plan
    function getSemesters(){
        axios.get(`/scheduler/${this.planId}/get-semester`)
            .then((response)=>{
                this.semesterList = response.data;
            });
    }

    function getCurrentSemester() {
        axios.get(`/scheduler/${this.planId}/current-semester`)
            .then((response)=>{
                this.dateSemester = response.data;
                //this.getAllCourses();
            });
    }

</script>

<style scoped>
    hr {
        width: 100%;
        box-sizing: border-box;
    }
    .class-finder-form {
        display: flex;
        flex-direction: column;
        width: 100%;
        box-sizing: border-box;
        margin-bottom: 1em;
    }
    .class-finder-form > select {
        width: 100%;
        box-sizing: border-box;
    }

    .class-finder-selected {
        width: 100%;
        box-sizing: border-box;
    }
</style>
