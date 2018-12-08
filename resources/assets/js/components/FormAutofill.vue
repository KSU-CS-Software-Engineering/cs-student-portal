<template>
    <div class="form-group">
        <input type="hidden" :id="field" :value="value" @input="handleInput">
        <label class="control-label" :for="field">{{ label }}</label>
        <div class="input-group" aria-describedby="studentidhelp">
            <div :id="addonId" class="input-group-addon">Selected: ({{ value }}) {{ valueText }}</div>
            <input type="text" class="form-control" :id="inputId" :aria-describedby="helpId" :disabled=disabled :placeholder="placeholder">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" :id="clrBtnId" title="Clear Selection"><i class="fa fa-times"></i></button>
            </span>
        </div>
        <span :id="helpId" class="help-block"></span>
    </div>
</template>

<script>
    export default {
        name: "FormAutofill",
        props: {
            field: {required: true},
            label: {required: true},
            value: null,
            valueText: null,
            disabled: false,
            placeholder: null,
        },
        computed: {
            addonId() {return this.field + "text";},
            inputId() {return this.field + "auto";},
            helpId() {return this.field + "help";},
            clrBtnId() {return this.field + "clear";},
        },
        methods: {
            handleInput: handleInput,
        },
    }

    function handleInput(event) {
        this.$emit('input', event.target.value);
    }
</script>
