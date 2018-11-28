<template>
    <div class="course list-group-item move" :class="{'custom-course': custom, 'complete-course': complete}" :key="course.id">
        <div class="pull-right">
            <div class="btn-group">
                <button type="button" class="prereqs btn btn-default btn-xs" aria-label="Prerequisites" title="Show Prerequisites" v-if="course.course_id > 0" @!click="showPrereqs"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button>
                <button type="button" class="edit-course btn btn-default btn-xs" aria-label="Edit" title="Edit Course" @!click="editCourse"><i class="fa fa-pencil"></i></button>
            </div>
            <p class="text-right prereq-icon"><i :class="prereqClass" aria-hidden="true"></i></p>
        </div>

        <div class="course-content pull-left">
            <template v-if="course.name.length !== 0">
                <p>
                    <a v-if="course.course_id > 0" :href="courseHref" target="_blank"><i class="fa fa-star" aria-hidden="true"></i></a>
                    <strong>{{ course.name }} ({{ course.credits }})</strong>
                </p>
                <p v-if="course.electivelist_name.length !== 0"><i class="fa fa-code-fork text-primary" aria-hidden="true"></i> {{ course.electivelist_abbr }}</p>
            </template>
            <p v-else><i class="fa fa-code-fork text-primary" aria-hidden="true"></i> <b>{{ course.electivelist_abbr }} ({{ course.credits }})</b></p>
            <p v-if="course.completedcourse_name.length !== 0"><i><i class="fa fa-check text-success" aria-hidden="true"></i> {{ course.completedcourse_name }}</i></p>
            <p v-if="course.notes.length !== 0"><i class="fa fa-comment-o" aria-hidden="true"></i> {{ course.notes }}</p>
        </div>

    </div>
</template>

<script>

    export default {
        name: "FlowchartCourse",
        props: [
            "courseProps",
        ],
        data() {
            return {
                course: this.courseProps,
                selectedCousreId: null,
                selectedPrereqs: null,
                selectedFollowers: null,
            }
        },
        computed: {
            courseHref() {
                return `/course/id/${this.course.course_id}`;
            },
            prereqClass: prereqClass,
            custom() {
                return this.course.degreerequirement_id <= 0;
            },
            complete() {
                return this.course.completedcourse_id > 0;
            },
        },
        methods: {
            showPrereqs: showPrereqs,
            editCourse: editCourse,
        },
        created() {
            eventDispatcher.$on("showPrereqs", (selectedCourseId, selectedPrereqs, selectedFollowers) => {
                this.selectedCourseId = selectedCourseId;
                this.selectedPrereqs = selectedPrereqs;
                this.selectedFollowers = selectedFollowers;
            });
        },
    }

    function showPrereqs(event) {
        if (this.selectedCousreId === this.course.course_id) {
            this.selectedCousreId = null;
            this.selectedPrereqs = null;
            this.selectedFollowers = null;
            eventDispatcher.$emit("showPrereqs", this.selectedCousreId, this.selectedPrereqs, this.selectedFollowers);
        } else {
            axios.get(`/courses/prereqfeed/${this.course.course_id}`)
                .then((response) => {
                    this.selectedCousreId = this.course.course_id;
                    this.selectedPrereqs = response.data.prerequisites;
                    this.selectedFollowers = response.data.followers;
                    eventDispatcher.$emit("showPrereqs", this.selectedCousreId, this.selectedPrereqs, this.selectedFollowers);
                })
                .catch(function (error) {
                    site.handleError("retrieve prerequisites", "", error);
                });
        }
    }

    function editCourse() {
        eventDispatcher.$emit("editCourse", this.course);
    }

    function prereqClass() {
        let cur = false;
        let pre = false;
        let post = false;
        if (this.selectedCousreId === this.course.course_id) {
            cur = true;
        }
        if (this.selectedPrereqs && this.selectedPrereqs.includes(this.course.course_id)) {
            pre = true;
        }
        if (this.selectedFollowers && this.selectedFollowers.includes(this.course.course_id)) {
            post = true;
        }
        return {
            'fa': cur || pre || post,
            'fa-3x': cur || pre || post,
            'fa-circle': cur,
            'fa-arrow-circle-right': pre,
            'fa-arrow-circle-left': post,
        };
    }
</script>
