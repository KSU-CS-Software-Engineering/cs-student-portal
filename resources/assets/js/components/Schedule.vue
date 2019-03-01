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
                    <schedule-day v-for="day in days" :key="day" :courses="courses[day]"
                                  :layoutMethods="layoutMethods"/>
                </div>
            </div>
        </div>
        <div id="class-finder">
            <h3>Class Finder</h3>
            <form class="class-finder-form">
                <label>
                    <input type="checkbox" name="showOwnClasses" v-model="showOwnClasses">
                    Show Your Own Classes
                </label>
                <br>
                <select id="available-classes" v-model="selectedCourse">
                    <option v-for="availableClass in availableClasses" :value="availableClass">{{ availableClass.name }}</option>
                </select>
            </form>
            <button @click="addSelectedCourse" :disabled="selectedCourse == null">Add</button>
            <hr>
            <div class="class-finder-selected">
                <schedule-added-course v-for="course in selectedCourses" :key="course.name" :course="course" :layoutMethods="layoutMethods"></schedule-added-course>
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
                dummyClasses: {
                    Monday: [
                        {
                            id: 0,
                            name: "Test1",
                            begin: 9 * 60 + 15,
                            end: 10 * 60 + 45,
                            location: "Somewhere",
                            teacher: "Someone"
                        }
                    ],
                    Tuesday: [
                        {
                            id: 1,
                            name: "Test2",
                            begin: 12 * 60 + 30,
                            end: 13 * 60 + 20,
                            location: "Somewhere",
                            teacher: "Someone"
                        },
                        {
                            id: 2,
                            name: "Test3",
                            begin: 16 * 60 + 30,
                            end: 17 * 60 + 20,
                            location: "Somewhere",
                            teacher: "Someone"
                        }
                    ],
                    Thursday: [
                        {
                            id: 3,
                            name: "Test2",
                            begin: 12 * 60 + 30,
                            end: 13 * 60 + 20,
                            location: "Somewhere",
                            teacher: "Someone"
                        },
                        {
                            id: 4,
                            name: "Test3",
                            begin: 16 * 60 + 30,
                            end: 17 * 60 + 20,
                            location: "Somewhere",
                            teacher: "Someone"
                        }
                    ]
                },
                allClasses: [
                    // {
                    //     name: course_name,
                    //     times: [
                    //         {
                    //
                    //             days: ,
                    //             begin: ,
                    //             end: ,
                    //             location: ,
                    //             teacher: ,
                    //
                    //         }
                    //     ]
                    //
                    //
                    // },
                    {
                        name: "CIS 643",
                        times: [
                            {
                                days: "M W F",
                                begin: 16 * 60 + 5,
                                end: 17 * 60 + 20,
                                location: "Somewhere",
                                teacher: "Someone",
                                added: false
                            },
                            {
                                days: " T T ",
                                begin: 16 * 60 + 5,
                                end: 17 * 60 + 20,
                                location: "Somewhere",
                                teacher: "Someone",
                                added: false
                            }
                        ]
                    },
                    {
                        name: "CIS 530",
                        times: [
                            {
                                days: "M W F",
                                begin: 16 * 60 + 5,
                                end: 17 * 60 + 20,
                                location: "Somewhere",
                                teacher: "Someone",
                                added: false
                            }
                        ]
                    },
                    {
                        name: "CIS 580",
                        times: [
                            {
                                days: "M W F",
                                begin: 16 * 60 + 5,
                                end: 17 * 60 + 20,
                                location: "Somewhere",
                                teacher: "Someone",
                                added: false
                            }
                        ]
                    }
                ],


                selectedCourse: null,
                selectedCourses: [],
                scheduleBegin: 7 * 60 + 30,
                scheduleEnd: 21 * 60 + 30,
                showOwnClasses: true,
            }
        },
        computed: {
            courses: function () {
                return this.showOwnClasses ? this.dummyClasses : [];
            },
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
                    formatTime: this.formatTime
                }
            },
            availableClasses: function () {
                return this.allClasses;
            }
        },
        methods: {
            calculateOffset: function (begin) {
                return (begin - this.scheduleBegin) / this.scheduleDuration;
            },
            calculateHeight: function (begin, end) {
                return (end - begin) / this.scheduleDuration;
            },
            addSelectedCourse: function () {
                if (!this.selectedCourses.includes(this.selectedCourse)) {
                    this.selectedCourses.push(this.selectedCourse);
                }
            },
            formatTime: function (time) {
                return `${Math.floor(time / 60)}:${('0' + time % 60).slice(-2)}`;
            },

            getAllCourses: getAllCourses,
        },

        created() {
            this.getAllCourses();
        }
    }

    //get course time info into allClasses array
    function getAllCourses(){
        axios.get(`/scheduler/${this.semesterId}/sections`)
            .then((response) => {
                let coursesTimes = response.data;
                console.log(coursesTimes);
                for(let i =0; i < coursesTimes.length; i++){
                    let courseTimes = coursesTimes[i];


                }



            })
            .catch((error) => {
                site.handleError("get data", "", error);
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