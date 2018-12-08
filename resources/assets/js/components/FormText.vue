<template>
    <div class="form-group">
        <label class="control-label" :for="field">{{ label }}</label>
        <input type="text" class="form-control" :id="field" :aria-describedby="helpId" :disabled=disabled :value="value" @input="handleInput" />
        <span :id="helpId" class="help-block"></span>
    </div>
</template>

<script>
    export default {
        name: "FormText",
        props: {
            field: {required: true},
            label: {required: true},
            value: {},
            disabled: {default: false},
        },
        computed: {
            helpId() {return this.field + "help";},
        },
        methods: {
            handleInput: handleInput,
        },
    }

    function handleInput(event) {
        this.$emit('input', event.target.value);
    }
</script>
