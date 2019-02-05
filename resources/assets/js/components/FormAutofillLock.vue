<template>
    <span class="input-group-btn">
        <button class="btn btn-default" type="button" :id="lckBtnId" title="Lock Selection" @click="switchLock">
            <i class="fa" :class="{active: locked, 'fa-lock': locked, 'fa-unlock-alt': !locked}"></i>
        </button>
        <input type="hidden" :id="lckInputId" :value="locked">
    </span>
</template>

<script>
    export default {
        name: "FormAutofillLock",
        props: {
            field: {required: true},
            locked: {required: true},
        },
        computed: {
            lckBtnId() {return this.field + "lockBtn"},
            lckInputId() {return this.field + "lock"},
        },
        methods: {
            switchLock: switchLock,
        },
    }

    function switchLock(event) {
        this.$emit('switchLock');
    }
</script>
