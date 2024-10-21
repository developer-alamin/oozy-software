<!-- ConfirmDialog.vue -->
<template>
    <v-dialog v-model="localVisible" max-width="400">
        <v-card>
            <v-card-title>
                <span class="headline">Restore Confirmation</span>
            </v-card-title>
            <v-card-text>
                <p>Are you sure you want to restore this brand?</p>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="cancel" color="grey">Cancel</v-btn>
                <v-btn @click="confirm" color="green">Restore</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: Boolean,
            required: true,
        },
        onConfirm: {
            type: Function,
            required: true,
        },
        onCancel: {
            type: Function,
            required: true,
        },
    },
    computed: {
        localVisible: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            },
        },
    },
    methods: {
        confirm() {
            this.onConfirm();
            this.localVisible = false; // Close the dialog
        },
        cancel() {
            this.onCancel();
            this.localVisible = false; // Close the dialog
        },
    },
};
</script>

<style scoped>
/* Optional: Add styles for the confirm dialog */
</style>
