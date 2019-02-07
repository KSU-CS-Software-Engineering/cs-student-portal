<template>
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
                <schedule-day v-for="day in days" :key="day" :courses="courses[day]" :layoutMethods="layoutMethods"/>
            </div>
        </div>
    </div>
</template>

<script>
    import ScheduleDay from "./ScheduleDay";
    import ScheduleHourLine from "./ScheduleHourLine";

    export default {
        name: "Schedule",
        components: {
            ScheduleDay,
            ScheduleHourLine
        },
        data() {
            return {
                days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
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
                scheduleBegin: 7 * 60 + 30,
                scheduleEnd: 21 * 60 + 30,
            }
        },
        computed: {
            courses: function () {
                return this.dummyClasses;
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
                    calculateHeight: this.calculateHeight
                }
            }
        },
        methods: {
            calculateOffset: function (begin) {
                return (begin - this.scheduleBegin) / this.scheduleDuration;
            },
            calculateHeight: function (begin, end) {
                return (end - begin) / this.scheduleDuration;
            }
        }
    }
</script>

<style scoped>

</style>