import Vue from "vue";
import Flowchart from "../components/Flowchart";
import CourseFormModal from "../components/CourseFormModal";
import axios from "axios";
import site from "../util/site";

export function init() {

    window.axios = axios;
    window.site = site;

    window.eventDispatcher = new Vue();

    let app = new Vue({
        el: "#flowchart",
        template: "<flowchart />",
        components: {
            Flowchart,
        },
        methods: {
            addSemester() {
                console.log(this.$children[0]);
                this.$children[0].addSemester();
            },
            addCourse() {
                this.$children[0].addCourse();
            },
            refresh() {
                this.$children[0].refresh();
            },
        }
    });

    let courseModal = new Vue({
        el: "#course-modal",
        template: "<course-form-modal />",
        components: {
            CourseFormModal,
        },
    });

    document.getElementById("reset").addEventListener("click", () => eventDispatcher.$emit("reloadFlowchart"));
    document.getElementById("add-sem").addEventListener("click", () => eventDispatcher.$emit("addSemester"));
    document.getElementById("add-course").addEventListener("click", () => eventDispatcher.$emit("createCourse"));

    site.ajaxautocomplete("electivelist_id", "/electivelists/electivelistfeed");

    // site.ajaxautocompletelock("course_id", "/courses/coursefeed");

    let student_id = document.getElementById("student_id").value;
    // site.ajaxautocompletelock("completedcourse_id", `/completedcourses/completedcoursefeed/${student_id}`);
}
