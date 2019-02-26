<template>
    <div class="schedule-course" :style="style">
        <p class="course-name">{{ course.name }}</p>
        <p class="course-time">{{ layoutMethods.formatTime(course.begin) }} - {{ layoutMethods.formatTime(course.end) }}</p>
        <p class="course-location">{{ course.location }}</p>
    </div>
</template>

<script>
    export default {
        name: "ScheduleCourse",
        props: {
            course: {},
            offset: {
                type: Number,
                required: true
            },
            height: {
                type: Number,
                required: true
            },
            layoutMethods: {}
        },
        computed: {
            style: function () {
                return `top: ${this.offset * 100}%; height: ${this.height * 100}%;`;
            }
        }

    }


</script>

<style scoped>
    p {
        margin-top: 0;
        margin-bottom: 0;
    }

    .course-name {
        font-weight: bold;
    }

    .course-time {
        font-size: 0.9em;
    }

    .course-location {
        font-size: 0.9em;
    }
</style>