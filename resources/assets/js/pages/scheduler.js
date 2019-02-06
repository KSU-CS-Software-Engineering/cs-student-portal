import Vue from "vue";
import Schedule from "../components/Schedule"

export function init() {
    let app = new Vue({
        el: "#schedule",
        components: {
            Schedule
        },
        template: `<schedule />`,
    })

}