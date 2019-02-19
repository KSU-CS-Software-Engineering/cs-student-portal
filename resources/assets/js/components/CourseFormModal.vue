<template>
    <div class="modal fade" id="editCourse" role="dialog" aria-labelledby="createEventLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="createEventLabel">Course Details</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <form-text field="course_name" label="Course Title" v-model="course.name" :disabled=!custom />

                        <form-autofill field="electivelist_id" label="Elective:" :value-text="course.electivelist_name"
                            placeholder="Enter Elective List" :value="course.electivelist_id" :disabled=!custom
                            :autocomplete-url="electiveUrl" @selected="electiveSelected" />

                        <form-text field="credits" label="Credits" v-model="course.credits" :disabled=!custom />

                        <form-text field="notes" label="Notes" v-model="course.notes" />

                        <form-autofill :with-lock="true" field="course_id" label="Catalog Match:" :value-text="course.course_name"
                            placeholder="Enter Course" :locked=course.course_id_lock v-model="course.course_id"
                            :autocomplete-url="coursesUrl" @selected="courseSelected" @switchLock=switchLockCourse />

                        <form-autofill :with-lock="true" field="completedcourse_id" label="Completed Course Match:"
                            :value-text="course.completedcourse_name" placeholder="Enter Course"
                            :locked=course.completedcourse_id_lock v-model="course.completedcourse_id"
                            :autocomplete-url="completedUrl" @selected="completedSelected" @switchLock=switchLockCompleted />
                    </form>
                </div>
                <div class="modal-footer">
                    <span id="spin" class="fa fa-cog fa-spin fa-lg" :class="{'hide-spin': !saving}">&nbsp;</span>
                    <button type="button" class="btn btn-danger" id="deleteCourse" v-if="canDelete" @click="deleteCourse">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" @click="initialiseForm">
                        <i class="fa fa-times" aria-hidden="true"></i> Close
                    </button>
                    <button type="button" class="btn btn-primary" id="saveCourse" @click="saveCourse">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FormText from "./FormText";
    import FormAutofill from "./FormAutofill";

    export default {
        name: "CourseFormModal",
        components: {
            FormText,
            FormAutofill,
        },
        data() {
            return {
                emptyCourseTemplate: {
                    id: null,
                    name: null,
                    credits: null,
                    notes: null,
                    electivelist_id: null,
                    electivelist_name: null,
                    course_id: null,
                    course_id_lock: false,
                    course_name: null,
                    completedcourse_id: null,
                    completedcourse_id_lock: false,
                    completedcourse_name: null,
                    degreerequirement_id: null,
                },
                course: {},
                newCourse: true,
                saving: false,
                electiveUrl: "/electivelists/electivelistfeed",
                coursesUrl: "/courses/coursefeed",
            };
        },
        computed: {
            canDelete() {
                return this.custom && !this.newCourse;
            },
            custom() {
                return this.course.degreerequirement_id <= 0;
            },
            studentId() {
                return document.getElementById("student_id").value;
            },
            completedUrl() {
                return `/completedcourses/completedcoursefeed/${this.studentId}`;
            },
        },
        methods: {
            initialiseForm: initialiseForm,
            createCourse: createCourse,
            editCourse: editCourse,
            saveCourse: saveCourse,
            deleteCourse: deleteCourse,
            switchLockCourse: switchLockCourse,
            switchLockCompleted: switchLockCompleted,
            electiveSelected: electiveSelected,
            courseSelected: courseSelected,
            completedSelected: completedSelected,
        },
        created() {
            this.initialiseForm();
            eventDispatcher.$on("editCourse", this.editCourse);
            eventDispatcher.$on("createCourse", this.createCourse);
        },
    }

    function initialiseForm(course = this.emptyCourseTemplate) {
        site.clearFormErrors();
        this.course = JSON.parse(JSON.stringify(course));
        this.saving = false;
    }

    function createCourse() {
        this.initialiseForm();
        this.newCourse = true;
        $("#editCourse").modal("show");
    }

    function editCourse(course) {
        this.initialiseForm(course);
        this.newCourse = false;
        $("#editCourse").modal("show");
    }

    function saveCourse(event) {
        this.saving = true;
        let planId = document.getElementById("id").value;
        let data = {
            notes: this.course.notes,
            course_id: this.course.course_id,
            completedcourse_id: this.course.completedcourse_id,
            course_id_lock: this.course.course_id_lock,
            completedcourse_id_lock: this.course.completedcourse_id_lock,
        };
        if (this.custom) {
            data.course_name = this.course.name;
            data.credits = this.course.credits;
            data.electivelist_id = this.course.electivelist_id;
        }
        let method, url = `/flowcharts/${planId}/requirements`;
        if (this.newCourse) {
            method = "post";
        } else {
            method = "put";
            url = `${url}/${this.course.id}`
        }
        axios[method](url, data)
            .then((response) => {
                $('#editCourse').modal('hide');
                site.displayMessage(response.data, "success");
                eventDispatcher.$emit("reloadFlowchart");
                this.initialiseForm();
            })
            .catch((error) => {
                this.saving = false;
                site.handleError("save course", "#editCourse", error);
            });
    }

    function deleteCourse(event) {
        this.saving = true;
        let planId = document.getElementById("id").value;

        axios.delete(`/flowcharts/${planId}/requirements/${this.course.id}`)
            .then((response) => {
                $('#editCourse').modal('hide');
                this.saving = false;
                site.displayMessage(response.data, "success");
                this.initialiseForm();
                eventDispatcher.$emit("reloadFlowchart");
            })
            .catch((error) => {
                this.saving = false;
                site.handleError("delete course", "#editCourse", error);
            });
    }

    function switchLockCourse(event) {
        this.course.course_id_lock = !this.course.course_id_lock;
    }

    function switchLockCompleted(event) {
        this.course.completedcourse_id_lock = !this.course.completedcourse_id_lock;
    }

    function electiveSelected(selected) {
        this.course.electivelist_id = selected.data;
        this.course.electivelist_name = selected.value;
    }

    function courseSelected(selected) {
        this.course.course_id = selected.data;
        this.course.course_name = selected.value;
    }

    function completedSelected(selected) {
        this.course.completedcourse_id = selected.data;
        this.course.completedcourse_name = selected.value;
    }
</script>
