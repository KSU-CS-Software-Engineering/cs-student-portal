<template>
    <div class="schedule-day">
        <schedule-course v-for="section in sections" :key="section.id" :course="section"
                         :offset="layoutMethods.calculateOffset(layoutMethods.parseTimes(section)[0])"
                         :height="layoutMethods.calculateHeight.apply(null, layoutMethods.parseTimes(section))"
                            :layoutMethods="layoutMethods"/>
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
            sections: {
                type: Array
            },
            layoutMethods: {}
        }
    }
</script>

<style scoped>

</style>