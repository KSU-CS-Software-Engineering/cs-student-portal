<template>
    <div class="form-group">
        <input type="hidden" :id="field" :value="value">
        <label class="control-label" :for="field">{{ label }}</label>
        <div class="input-group" aria-describedby="studentidhelp">
            <div :id="addonId" class="input-group-addon" v-if="value !== null">Selected: ({{ value }}) {{ valueText }})</div>
            <input type="text" class="form-control" :id="inputId" :aria-describedby="helpId" :disabled=disabled
                :placeholder="placeholder" v-model="inputData">
            <form-autofill-lock v-if="withLock" :field="field" :locked="locked" @switchLock="switchLock" />
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" :id="clrBtnId" title="Clear Selection" @click="clearSelection">
                    <i class="fa fa-times"></i>
                </button>
            </span>
        </div>
        <span :id="helpId" class="help-block"></span>
    </div>
</template>

<script>
    import FormAutofillLock from "./FormAutofillLock";

    export default {
        name: "FormAutofill",
        components: {
            FormAutofillLock
        },
        props: {
            field: {required: true},
            label: {required: true},
            value: null,
            valueText: null,
            disabled: false,
            placeholder: null,
            electivelist_id: null,
            autocompleteUrl: null,
            withLock: false,
            locked: false,
        },
        data() {
            return {
                inputData: null,
            };
        },
        computed: {
            addonId() {return this.field + "text";},
            inputId() {return this.field + "auto";},
            helpId() {return this.field + "help";},
            clrBtnId() {return this.field + "clear";},
        },
        methods: {
            autocompleteInit: autocompleteInit,
            clearSelection: clearSelection,
            switchLock: switchLock,
        },
        mounted() {
            this.autocompleteInit();
            console.log(this.autocompleteUrl);
            console.log(this.electivelist_id);
        }
    }

    function autocompleteInit() {
      //let self = this;
        $(`#${this.inputId}`).autocomplete({
            serviceUrl: this.autocompleteUrl + "?electivelist_id=" + this.electivelist_id,
            ajaxSettings: {
                dataType: "json",
            },
            minChars: 3,
            autoSelectFirst: true,
            onSelect: (suggestion) => {
                this.$emit("selected", suggestion);
                this.inputData = null;
            },
            transformResult: function (response) {
                console.log(response);
                return {
                    suggestions: $.map(response.data, function (dataItem) {
                        return {value: dataItem.value, data: dataItem.data};
                    })
                };
            }
        });
    }

    function clearSelection() {
        let emptyData = {
            value: null,
            data: null,
        };
        this.$emit("selected", emptyData);
    }

    function switchLock(event) {
        this.$emit('switchLock');
    }
</script>
