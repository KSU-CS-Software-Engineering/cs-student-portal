<template>
    <div class="added-course-root">
        <div @click="toggleExpand" class="added-course-title">
            <h5>{{ course.name }}</h5>
            <i class="fa expand-btn" :class="{ 'fa-angle-up' : isExpanded, 'fa-angle-down' : !isExpanded }"></i>
        </div>
        <table v-show="isExpanded" class="added-course-time">
            <tr>
                <th>Section</th>
                <th>Type</th>
                <th>Days</th>
                <th>Hours</th>
                <th>Instructor</th>
            </tr>
            <tr v-for="time in course.times">
                <td>
                    <i v-if="!courseIsAdded" @click="addTime(time)" class="fa fa-plus time-btn"></i>
                    <i v-else-if="timeIsAdded(time)" @click="removeTime" class="fa fa-minus time-btn"></i>
                    <i v-else @click="addTime(time)" class="fa fa-exchange time-btn"></i>
                    A
                </td>
                <td>LEC</td>
                <td>{{ time.days }}</td>
                <td>{{ layoutMethods.formatTime(time.begin) }} - {{ layoutMethods.formatTime(time.end) }}</td>
                <td>{{ time.teacher }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        name: "ScheduleAddedCourse",
        props: {
            course: {},
            layoutMethods: {}
        },
        data() {
            return {
                isExpanded: false
            }
        },
        computed: {
            courseIsAdded: function () {
                return this.course.times.some(time => time.added);
            }
        },
        methods: {
            timeIsAdded: function (time) {
                return time.added;
            },
            addTime: function (time) {
                this.removeTime();
                time.added = true;
            },
            removeTime: function () {
                this.course.times.forEach(time => time.added = false);
            },
            toggleExpand: function () {
                this.isExpanded = !this.isExpanded;
            }
        }
    }
</script>

<style scoped>
    p {
        margin-top: 0;
        margin-bottom: 0;
    }

    table {
        table-layout: auto;
        width: 100%;
        box-sizing: border-box;
        border-collapse: collapse;
    }

    tr {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        border: 2px solid white;
        padding-left: 15px;
    }

    td, th {
        flex: 1 1 150px;
    }

    td {
        background-color: rgba(151, 160, 179, 0.05);
    }

    td:first-child {
        position: relative;
    }

    .added-course-title {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #512888;
        color: white;
        padding: 0.8em 2em;
    }

    .added-course-title > h5 {
        margin: 0;
    }

    .time-btn {
        position: absolute;
        left: -15px;
        top: 5px;
        width: 10px;
    }

    .expand-btn {
        padding: 3px;
    }
</style>