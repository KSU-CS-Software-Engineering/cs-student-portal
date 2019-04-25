import Vue from "vue";
import Flowchart from "../components/Flowchart";
import CourseFormModal from "../components/CourseFormModal";
import FlowchartErrors from "../components/FlowchartErrors";
import { eventDispatcher } from "../util/vueEventDispatcher";

export function init() {

    const app = new Vue({
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

    const courseModal = new Vue({
        el: "#course-modal",
        template: "<course-form-modal />",
        components: {
            CourseFormModal,
        },
    });

    const errors = new Vue({
        el: "#flowchart-errors",
        template: "<flowchart-errors />",
        components: {
            FlowchartErrors,
        },
    });

    document.getElementById("reset")
        .addEventListener("click", () => eventDispatcher.$emit("updateFlowchart"));

    document.getElementById("add-sem")
        .addEventListener("click", () => eventDispatcher.$emit("addSemester"));

    document.getElementById("add-course")
        .addEventListener("click", () => eventDispatcher.$emit("createCourse"));
}
