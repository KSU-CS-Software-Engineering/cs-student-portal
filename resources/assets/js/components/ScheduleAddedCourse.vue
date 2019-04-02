<template>
    <div class="added-course-root">
        <div @click="toggleExpand" class="added-course-title">
            <h5><b>{{ course.slug }} - {{ course.title }}</b></h5>
            <i class="fa expand-btn" :class="{ 'fa-angle-up' : isExpanded, 'fa-angle-down' : !isExpanded }"></i>
        </div>
        <table v-show="isExpanded" class="added-course-section">
            <tr>
                <th>Section</th>
                <th>Type</th>
                <th>Days</th>
                <th>Hours</th>
                <th>Instructor</th>
            </tr>
            <tr v-for="section in course.sections">
                <td>
                    <i v-if="!courseIsAdded" @click="addSection(section)" class="fa fa-plus section-btn"></i>
                    <i v-else-if="sectionIsAdded(section)" @click="addSection(null)" class="fa fa-minus section-btn"></i>
                    <i v-else @click="addSection(section)" class="fa fa-exchange section-btn"></i>
                    {{ section.section }}
                </td>
                <td>{{ section.type }}</td>
                <td>{{ section.days }}</td>
                <td>{{ section.hours }}</td>
                <td>{{ section.instructor }}</td>
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
                return this.course.sections.some(section => section.added);
            }
        },
        methods: {
            sectionIsAdded: function (section) {
                return section.added;
            },
            addSection: function (section) {
                this.$emit('putSection', section)
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
        margin-left: 15px;
        padding-left: 0.2rem;
        background-color: rgba(151, 160, 179, 0.05);
    }

    tr:first-child {
        background-color: transparent;
    }

    tr:nth-child(even) {
        background-color: rgba(151, 160, 179, 0.36);
    }

    td, th {
        flex: 1 1 150px;
    }

    td:first-child {
        position: relative;
        padding-top: 0.2rem;
    }

    td:last-child {
        padding-bottom: 0.2rem;
    }

    .added-course-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #512888;
        color: white;
        padding: 0.8em 2em;
    }

    .added-course-title > h5 {
        margin: 0;
    }

    .section-btn {
        position: absolute;
        left: -15px;
        top: 5px;
        width: 10px;
    }

    .expand-btn {
        padding: 3px;
    }
</style>