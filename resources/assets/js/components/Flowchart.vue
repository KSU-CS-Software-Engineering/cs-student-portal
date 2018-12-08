<template>
    <div id="flowchart">
        <draggable class="flowchart" v-model="semesters" :options="{group: 'semesters', animation: 150, filter: '.no-drag', preventOnFilter: false}" @end="dropSemester">
            <flowchart-semester v-for="semester in semesters" :key="semester.id" :semester-props="semester" @deleteSemester="deleteSemester" />
        </draggable>
    </div>
</template>

<script>
    import axios from "axios";
    import Draggable from "vuedraggable";
    import FlowchartSemester from "./FlowchartSemester";

    export default {
        name: "Flowchart",
        components: {
            Draggable,
            FlowchartSemester,
        },
        data() {
            return {
                id: document.getElementById("id").value,
                semesters: [],
            }
        },
        methods: {
            loadSemesters: loadSemesters,
            loadCourses: loadCourses,
            dropSemester: dropSemester,
            deleteSemester: deleteSemester,
            addSemester: addSemester,
            refresh: refresh,
        },
        created() {
            this.loadSemesters();
            this.loadCourses();
            eventDispatcher.$on("addSemester", this.addSemester);
            eventDispatcher.$on("reloadFlowchart", this.refresh)
        }
    }

    function loadSemesters() {
        axios.get(`/flowcharts/${this.id}/semesters`)
            .then((response) => {
                this.semesters = response.data;
                document.documentElement.style.setProperty("--colNum", this.semesters.length);
            })
            .catch((error) => {
                site.handleError("get data", "", error);
            });
    }

    function loadCourses() {
        axios.get(`/flowcharts/${this.id}/courses`)
            .then((response) => {
                let courses = response.data;
                for (let i = 0; i < courses.length; i++) {
                    let course = courses[i];
                    let semester = this.semesters.find((element) => {
                        return element.id === course.semester_id;
                    });
                    semester.courses = semester.courses || [];
                    semester.courses.push(course);
                }
            })
            .catch((error) => {
                site.handleError("get data", "", error);
            });

    }

    function dropSemester() {
        let data = {
            ordering: [],
        };
        for (let i = 0; i < this.semesters.length; i++) {
            data.ordering.push({
                id: this.semesters[i].id,
                ordering: i,
            });
        }
        axios.patch(`/flowcharts/${this.id}/semesters`, data)
            .then((response) => {
                console.log("Flowchart.vue: dropSemester: success");
                console.log(response);
                //site.displayMessage(response.data, "success");
            })
            .catch((error) => {
                console.log("Flowchart.vue: dropSemester: error");
                console.log(error);
                site.displayMessage("AJAX Error", "danger");
            })
    }

    function deleteSemester(event, id) {
        axios.delete(`/flowcharts/${this.id}/semesters/${id}`)
            .then((response) => {
                for (let i = 0; i < this.semesters.length; i++) {
                    if (this.semesters[i].id === id) {
                        this.semesters.splice(i, 1);
                        break;
                    }
                }
                document.documentElement.style.setProperty("--colNum", this.semesters.length);
                console.log("Flowchart.vue: dropSemester: success");
                console.log(response);
                //site.displayMessage(response.data, "success");
            })
            .catch((error) => {
                console.log("Flowchart.vue: deleteSemester: error");
                console.log(error);
                site.displayMessage("AJAX Error", "danger");
            });
    }

    function addSemester() {
        let data = {};
        axios.post(`/flowcharts/${this.id}/semesters`, data)
            .then((response) => {
                this.semesters.push(response.data);
                document.documentElement.style.setProperty("--colNum", this.semesters.length);
                //site.displayMessage("Item Saved", "success");
            })
            .catch((error) => {
                site.displayMessage("AJAX Error", "danger");
            })
    }

    function refresh() {
        this.loadSemesters();
        this.loadCourses();
    }
</script>
