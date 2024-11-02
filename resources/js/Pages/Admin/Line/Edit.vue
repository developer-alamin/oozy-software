<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Line</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field v-model="line.name" label="Name"> </v-text-field>
                <!-- Description Field -->
                <v-textarea v-model="line.description" label="Description" />
                <!-- Action Buttons -->

                <v-row class="mt-4">
                    <v-col cols="12" class="text-right">
                        <v-btn
                            type="button"
                            color="secondary"
                            class="mr-3"
                            @click="resetForm"
                        >
                            Reset Form
                        </v-btn>
                        <v-btn
                            type="submit"
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update Line
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <!-- Server Error Message -->
        <v-alert v-if="serverError" type="error" class="my-4">
            {{ serverError }}
        </v-alert>
    </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false,
            line: {
                name: "",
                description: "",
            },
            errors: {},
            serverError: null,
        };
    },
    created() {
        this.fetchLine();
    },
    methods: {
        async fetchLine() {
            // Fetch the brand data to populate the form
            const lineId = this.$route.params.uuid; // Assuming the brand ID is passed in the route params
            try {
                const response = await this.$axios.get(`/line/${lineId}/edit`);
                this.line = response.data.line;
                console.log(response.data);
                // Populate form with the existing brand data
            } catch (error) {
                this.serverError = "Error fetching brand data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const lineId = this.$route.params.uuid; // Assuming brand ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/line/${lineId}`,
                        this.line
                    );
                    if (response.data.success) {
                        this.$router.push({ name: "LineIndex" }); // Redirect to brand list page
                        toast.success("Line Updated successfully!");
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating Line.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchLine(); // Reset the form with existing brand data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
