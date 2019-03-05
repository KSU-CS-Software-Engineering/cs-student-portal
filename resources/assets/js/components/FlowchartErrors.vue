<template>
    <div id="error" class="row bg-red rounded flowchart-header">
        <p v-if="noErrors"><strong>No Errors</strong></p>
        <template v-else v-for="errorGroup of errors">
            <template v-if="errorGroup.errors && errorGroup.errors.length">
                <h4>{{ errorGroup.title }}</h4>
                <ul>
                    <li v-for="error of errorGroup.errors" v-if="error">{{ error.message ? error.message : error /* Temporary until errors refactoring */ }}</li>
                </ul>
            </template>
        </template>
    </div>
</template>

<script>
    import axios from "axios";
    import { eventDispatcher } from "../util/vueEventDispatcher";

    export default {
        name: "FlowchartErrors",
        data() {
            return {
                errors: [],
                planId: document.getElementById("id").value,
            };
        },
        computed: {
            url() { return `/flowcharts/${this.planId}/errors`; },
            noErrors() {
                for (let errorGroup of this.errors) {
                    if (errorGroup.errors && errorGroup.errors.length) {
                        return false;
                    }
                }
                return true;
            }
        },
        methods: {
            fetchErrors: fetchErrors,
        },
        mounted() {
            eventDispatcher.$on("flowchartModified", this.fetchErrors);
            eventDispatcher.$on("updateFlowchart", this.fetchErrors);
            this.fetchErrors();
        },
    }

    function fetchErrors() {
        if (this.url === null) {
            return;
        }
        axios.get(this.url)
            .then((response) => {
                this.errors = response.data;
            })
            .catch((error) => {
                console.error(error);
            });
    }
</script>
