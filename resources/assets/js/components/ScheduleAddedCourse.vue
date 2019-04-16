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
            <tr v-for="section in course.sections" @click="addSection(sectionIsAdded(section) ? null : section)">
                <td>
                    <i class="fa section-btn" :class="faIconClass(section)"></i>
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
            layoutMethods: {},
            addedSection: {}
        },
        data() {
            return {
                isExpanded: false
            }
        },
        computed: {
            courseIsAdded: function () {
                return this.addedSection && true;
            },
        },
        methods: {
            sectionIsAdded: function (section) {
                return this.addedSection === section;
            },
            faIconClass: function (section) {
                return this.sectionIsAdded(section) ? 'fa-minus'
                    : this.courseIsAdded ? 'fa-exchange' : 'fa-plus';
            },
            addSection: function (section) {
                this.$emit('putSection', this.course.id, section)
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

    tr:not(:first-child) {
        cursor: pointer;
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

    .added-course-root:not(:first-child) {
        margin-top: 0.1rem;
    }

    .added-course-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #512888;
        color: white;
        padding: 0.8em 2em;
        cursor: pointer;
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
