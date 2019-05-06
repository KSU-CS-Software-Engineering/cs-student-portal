<template>
    <div class="schedule-day">
        <schedule-course v-for="section in sections" :key="section.id" :course="section"
                         :offset="layoutMethods.calculateOffset(sectionTimes[section.id][0])"
                         :height="layoutMethods.calculateHeight.apply(null, sectionTimes[section.id])"
                         :width="1/sectionWidth[section.id].sections.length"
                         :v-position="sectionWidth[section.id].sections.indexOf(section)"
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
        },
        computed: {
            sectionTimes: function () {
                return this.sections.reduce((acc, section) => {
                    acc[section.id] = this.layoutMethods.parseTimes(section);
                    return acc;
                }, {});
            },
            /**
             * as with how many sections have to be displayed horizontally. Meant as width: (100/retValue)%
             */
            sectionWidth: function () {
                let intervals = [/* sections: arr, begin, end */];
                let section2BiggestInterval = {};
                let collidesWith = function (begin, end) {
                    return intervals.filter(interval => !(begin > interval.end || end < interval.begin))
                };
                for (let section of this.sections) {
                    let times = this.sectionTimes[section.id];
                    let collidingIntervals = collidesWith(times[0], times[1]);

                    let interval = {sections: [section], begin: times[0], end: times[1]};
                    intervals.push(interval);
                    section2BiggestInterval[section.id] = interval;
                    collidingIntervals.forEach(interval => {
                        let newInterval = {
                            sections: interval.sections.concat(section).sort((a, b) => a.id - b.id),
                            begin: Math.min(interval.begin, times[0]),
                            end: Math.max(interval.end, times[1])
                        };
                        intervals.push(newInterval);
                        newInterval.sections.forEach(s => {
                            if(newInterval.sections.length > section2BiggestInterval[s.id].sections.length) {
                                section2BiggestInterval[s.id] = newInterval;
                            }
                        });
                    });
                }
                return section2BiggestInterval;
            }
        }
    }
</script>

<style scoped>

</style>