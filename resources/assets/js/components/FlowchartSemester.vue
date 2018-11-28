<template>
    <div class="semester" :key="id">
        <div class="panel panel-default">
            <div class="panel-heading clearfix move" v-if="!nameIsEdited">
                <h4 class="panel-title pull-left">{{ name }}</h4>
                <div class="btn-group pull-right">
                    <button type="button" title="Delete Semester" class="delete-sem btn btn-default btn-xs" aria-label="Delete" v-if="courses.length === 0" @!click="deleteSemester"><i class="fa fa-times"></i></button>
                    <button type="button" title="Edit Semester" class="edit-sem btn btn-default btn-xs" aria-label="Edit" @!click="editSemesterName"><i class="fa fa-pencil"></i></button>
                </div>
            </div>

            <div class="panel-heading clearfix" v-if="nameIsEdited">
                <div class="input-group no-drag">
                    <input type="text" class="form-control input-sm" title="Semester name" v-model="name" :id="'sem-text-' + id" @keyup.enter="saveSemesterName">
                    <div class="input-group-btn">
                        <button type="button" title="Save Semester" class="save-sem btn btn-success btn-sm" aria-label="Save" @!click="saveSemesterName"><i class="fa fa-check"></i></button>
                    </div>
                </div>
            </div>
            <draggable class="list-group" v-model="courses" :options="{group: 'courses', animation: 150}" @add="addCourse" @remove="removeCourse">
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
                nameIsEdited: false,
                id: this.semesterProps.id,
                name: this.semesterProps.name,
                courses: this.semesterProps.courses,
            };
        },
        methods: {
            editSemesterName: editSemesterName,
            saveSemesterName: saveSemesterName,
            deleteSemester: deleteSemester,
            addCourse: addCourse,
            removeCourse: removeCourse,
        },
    }

    function editSemesterName() {
        this.nameIsEdited = true;
    }

    function saveSemesterName() {
        let id = document.getElementById("id").value;
        let data = {
            name: this.name,
        };
        axios.patch("/flowcharts/" + id + "/semesters/" + this.id, data)
            .then((response) => {
                this.nameIsEdited = false;
                //site.displayMessage(response.data, "success");
            })
            .catch((error) => {
                site.displayMessage("AJAX Error", "danger");
            })
    }

    function deleteSemester(event) {
        let choice = confirm("Are you sure?");
        if (choice === true) {
            this.$emit("deleteSemester", event, this.id);
        }
    }

    function addCourse() {

    }

    function removeCourse() {

    }
</script>
