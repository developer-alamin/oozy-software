<!-- ConfirmDialog.vue -->
<template>
    <v-dialog v-model="localVisible" max-width="400">
        <v-card>
            <v-card-title>
                <span class="headline">Delete Confirmation</span>
            </v-card-title>
            <v-card-text>
                <p>{{ dialogName }}</p>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="cancel" color="grey">Cancel</v-btn>
                <v-btn @click="confirm" color="red">Delete</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        dialogName:{
            type:String
        },

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
    setup(props){
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
        }
    },
};
</script>

<style scoped>
/* Optional: Add styles for the confirm dialog */
</style>
