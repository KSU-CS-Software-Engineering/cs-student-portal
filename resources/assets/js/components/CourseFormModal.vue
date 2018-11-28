<template>
    <div class="modal fade" id="editCourse" role="dialog" aria-labelledby="createEventLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="createEventLabel">Course Details</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <form-text field="course_name" label="Course Title" v-model="course.name" :disabled=!custom />

                        <form-autofill field="electivelist_id" label="Elective:" :value-text="course.electivelist_name" placeholder="Enter Elective List" v-model="course.electivelist_id" :disabled=!custom />

                        <form-text field="credits" label="Credits" v-model="course.credits" :disabled=!custom />

                        <form-text field="notes" label="Notes" v-model="course.notes" />

                        <form-autofill-lock field="course_id" label="Catalog Match:" :value-text="course.course_name" placeholder="Enter Course" :locked=course.course_id_lock v-model="course.course_id" @setLock=setLockCourse />

                        <form-autofill-lock field="completedcourse_id" label="Completed Course Match:" :value-text="course.completedcourse_name" placeholder="Enter Course" :locked=course.completedcourse_id_lock v-model="course.completedcourse_id" @setLock=setLockCourse />

                        <input type="hidden" id="planrequirement_id" v-model="course.planrequirement_id" />
                    </form>
                </div>
                <div class="modal-footer">
                    <span id="spin" class="fa fa-cog fa-spin fa-lg hide-spin">&nbsp;</span>
                    <button type="button" class="btn btn-danger" id="deleteCourse" v-if="canDelete" @click="deleteCourse"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="saveCourse" @click="saveCourse"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FormText from "./FormText";
    import FormAutofill from "./FormAutofill";
    import FormAutofillLock from "./FormAutofillLock";

    export default {
        name: "CourseFormModal",
        components: {
            FormText,
            FormAutofill,
            FormAutofillLock,
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
            }
        },
        computed: {
            canDelete() {
                return !this.newCourse && this.course.degreerequirement_id < 0;
            },
            custom() {
                return this.course.degreerequirement_id <= 0;
            }
        },
        methods: {
            createCourse: createCourse,
            editCourse: editCourse,
            saveCourse: saveCourse,
            deleteCourse: deleteCourse,
            setLockCourse: (locked) => this.course_id_lock = locked,
            setLockCompleted: (locked) => this.completedcourse_id_lock = locked,
        },
        created() {
            this.course = JSON.parse(JSON.stringify(this.emptyCourseTemplate));
            eventDispatcher.$on("editCourse", this.editCourse);
            eventDispatcher.$on("createCourse", this.createCourse);
        }
    }

    function createCourse() {
        this.course = JSON.parse(JSON.stringify(this.emptyCourseTemplate));
        this.newCourse = true;
        $("#editCourse").modal("show");
    }

    function editCourse(course) {/*
        site.clearFormErrors();
        var courseIndex = $(event.currentTarget).data('id');
        var semIndex = $(event.currentTarget).data('sem');
        var course = window.vm.semesters[semIndex].courses[courseIndex];
        $('#course_name').val(course.name);
        $('#credits').val(course.credits);
        $('#notes').val(course.notes);
        $('#planrequirement_id').val(course.id);
        $('#electlivelist_id').val(course.electivelist_id);
        $('#electivelist_idauto').val('');
        $('#electivelist_idtext').html("Selected: (" + course.electivelist_id + ") " + site.truncateText(course.electivelist_name, 30));
        $('#course_id').val(course.course_id);
        $('#course_idauto').val('');
        $('#course_idtext').html("Selected: (" + course.course_id + ") " + site.truncateText(course.course_name, 30));
        site.ajaxautocompleteset('course_id', course.course_id_lock);
        $('#completedcourse_id').val(course.completedcourse_id);
        $('#completedcourse_idauto').val('');
        $('#completedcourse_idtext').html("Selected: (" + course.completedcourse_id + ") " + site.truncateText(course.completedcourse_name, 30));
        site.ajaxautocompleteset('completedcourse_id', course.completedcourse_id_lock);
        if (course.degreerequirement_id <= 0) {
            $('#course_name').prop('disabled', false);
            $('#credits').prop('disabled', false);
            $('#electivelist_idauto').prop('disabled', false);
            $('#deleteCourse').show();
        } else {
            if (course.electivelist_id <= 0) {
                $('#course_name').prop('disabled', true);
            } else {
                $('#course_name').prop('disabled', false);
            }
            $('#credits').prop('disabled', true);
            $('#electivelist_idauto').prop('disabled', true);
            $('#deleteCourse').hide();
        }

        $('#editCourse').modal('show');*/

        site.clearFormErrors();
        this.course = course;
        this.newCourse = false;
        $("#editCourse").modal("show");
    }

    function saveCourse(event) {
        let elSpin = document.getElementById("spin");
        elSpin.classList.remove("hide-spin");
        let id = document.getElementById("id").value;
        let PlanRequirementId = document.getElementById("planrequirement_id");
        let data = {
            notes: notes,
            course_id_lock: document.getElementById("course_idlock").value,
            completedcourse_id_lock: document.getElementById("completedcourse_idlock").value,
            course_id: courseId,
            completedcourse_id: completedCourseId,
        };
        if (!this.isDegreeReq) {
            data.course_name = $('#course_name').val();
            data.credits = $('#credits').val();

            if ($('#electivelist_id').val() > 0) {
                data.electivelist_id = $('#electivelist_id').val();
            } else {
                data.electivelist_id = '';
            }
        }
        this.$emmit('courseSaved', data);
        let method;
        if (this.newCourse) {
            method = "post";
        } else {
            method = "patch";
        }
        // axios[method](`/flowcharts/${id}/data`, data)
        //     .then((response) => {
        //         $('#editCourse').modal('hide');
        //         elSpin.classList.add('hide-spin');
        //         site.displayMessage(response.data, "success");
        //         site.clearFormErrors();
        //     })
        //     .catch((error) => {
        //         elSpin.classList.add('hide-spin');
        //         site.handleError("save course", "#editCourse", error);
        //     });
    }

    function deleteCourse(event) {
        $('#spin').removeClass('hide-spin');
        var id = $('#id').val();
        var planrequirement_id = $('#planrequirement_id').val();
        var data = {
            planrequirement_id: planrequirement_id,
        }
        window.axios.post('/flowcharts/data/' + id + '/delete', data)
            .then(function (response) {
                $('#editCourse').modal('hide');
                $('#spin').addClass('hide-spin');
                site.displayMessage(response.data, "success");
                site.clearFormErrors();
                loadData();
            })
            .catch(function (error) {
                $('#spin').addClass('hide-spin');
                site.handleError("delete course", "#editCourse", error);
            });
    }
</script>
