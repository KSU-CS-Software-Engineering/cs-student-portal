<template>
    <div class="schedule-day">
        <schedule-course v-for="course in courses" :key="course.id" :course="course"
                         :offset="layoutMethods.calculateOffset(course.begin)"
                         :height="layoutMethods.calculateHeight(course.begin, course.end)"/>
    </div>
</template>

<script>
    import ScheduleCourse from "./ScheduleCourse";

    export default {
        name: "ScheduleDay",
        components: {
            ScheduleCourse
        },
        props: {
            courses: {
                type: Array
            },
            layoutMethods: {}
        }
    }
</script>

<style scoped>

</style>