<template>
    <div class="schedule-hourLine" :style="style">
        <hr>
        {{ time }}
    </div>
</template>

<script>
    export default {
        name: "ScheduleHourLine",
        props: {
            time: {
                type: Number,
                required: true
            },
            offset: {
                type: Number,
                required: true
            }
        },
        computed: {
            style: function () {
                return 'top: ' + this.offset * 100 + '%';
            }
        }
    }
</script>

<style scoped>
    hr {
        height: 1px;
        width: 100%;
        margin-top: 0;
        margin-bottom: 2px;
        z-index: -1;
    }
</style>