<template>
    <div class="semester">
        <div class="panel panel-default">
            <div class="panel-heading clearfix move">
                <h4 class="panel-title pull-left">{{ name }}</h4>
                <div class="btn-group pull-right" v-if="courses.length === 0">
                    <button type="button" title="Delete Semester" class="delete-sem btn btn-default btn-xs"
                            aria-label="Delete" @!click="deleteSemester"><i class="fa fa-times"></i></button>
                    <button type="button" title="Set Summer" class="set-summer btn btn-default btn-xs"
                            aria-label="Summer" @!click="setSummer"><i class="fa fa-pencil"></i></button>
                </div>
            </div>
            <draggable class="list-group" v-model="courses" :options="{group: 'courses', animation: 150}"
                    @add="addCourse" @end="endDragging">
                <flowchart-course v-for="course in courses" :key="course.id" :course-props="course" />
            </draggable>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import Draggable from "vuedraggable";
    import FlowchartCourse from "./FlowchartCourse";

    export default {
        name: "FlowchartSemester",
        components: {
            Draggable,
            FlowchartCourse,
        },
        props: [
            "semesterProps",
        ],
        data() {
            return {
                id: this.semesterProps.id,
                name: this.semesterProps.name,
                courses: this.semesterProps.courses,
            };
        },
        methods: {
            propsToData: propsToData,
            // editSemesterName: editSemesterName,
            // saveSemesterName: saveSemesterName,
            deleteSemester: deleteSemester,
            setSummer: setSummer,
            addCourse: addCourse,
            endDragging: endDragging,
        },
        created() {
            eventDispatcher.$on("flowchartUpdated", this.propsToData);
        }
    }

    function propsToData() {
        this.id = this.semesterProps.id;
        this.name = this.semesterProps.name;
        this.courses = this.semesterProps.courses;
    }

    function deleteSemester(event) {
        let choice = confirm("Are you sure?");
        if (choice === true) {
            this.$emit("deleteSemester", event, this.id);
        }
    }

    function setSummer(event) {
        let planId = document.getElementById("id").value;
        axios.post(`/flowcharts/${planId}/semesters/${this.id}/set-summer`)
            .then((response) => {
                site.displayMessage(response.data, "success");
                eventDispatcher.$emit("updateFlowchart");
            })
            .catch((error) => {
                site.displayMessage(error, "Danger");
            });
    }

    function addCourse(event) {
        let planId = document.getElementById("id").value;

        let requirementId = this.courses[event.newIndex].id;

        let data = {
            requirementId: requirementId,
            semesterId: this.id,
            order: [],
        };

        for (let i = 0; i < this.courses.length; i++) {
            data.order.push({
                id: this.courses[i].id,
                order: i,
            });
        }

        axios.patch(`/flowcharts/${planId}/requirements/${requirementId}`, data)
            .then((response) => {
                // site.displayMessage(response.data, "success");
                eventDispatcher.$emit("flowchartModified");
            })
            .catch((error) => {
                site.displayMessage("AJAX Error", "danger");
            });
    }

    function endDragging(event) {
        if (event.from === event.to) {
            this.addCourse(event);
        }
    }
</script>
