<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Edit Effect</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <v-row>
                    <!-- Select Cause -->
                    <v-col cols="12">
                        <v-autocomplete
                            v-model="effect.cause_id"
                            :items="causes"
                            item-value="id"
                            :item-title="formatCause"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="errors.cause_id ? errors.cause_id : ''"
                            @update:search="fetchCauses"
                        >
                            <template v-slot:label>
                                Select Cause <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>

                    <!-- Select Status -->
                    <v-col cols="12">
                        <v-select
                            v-model="effect.status"
                            :items="statusItems"
                            label="Effect Status"
                            outlined
                            clearable
                            :rules="[rules.required]"
                        >
                            <template v-slot:label>
                                Status <span style="color: red">*</span>
                            </template>
                        </v-select>
                    </v-col>
                </v-row>

                <!-- Effect Name -->
                <v-textarea
                    v-model="effect.name"
                    label="Effect Name"
                    :rules="[rules.required]"
                    outlined
                    density="comfortable"
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-textarea>

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
                            Update Effect
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
            statusItems: ["Active", "Inactive"],
            effect: {
                cause_id: null,   // Added cause_id field
                name: "",
                status: "Active",  // Default status to "Active"
            },
            causes: [],         // List of causes to be fetched from the backend
            errors: {},
            serverError: null,
            limit: 5,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchCauses().then(() => {
            this.fetchEffect();  // Fetch effect data when the page loads
        });
    },
    methods: {
        // Fetch Effect Details for editing
        async fetchEffect() {
            const uuid = this.$route.params.uuid; // Fetch the UUID from route params
            try {
                const response = await this.$axios.get(`/effect/${uuid}/edit`);
                this.effect = response.data.item; // Populate form with effect data
            } catch (error) {
                console.error("Error fetching effect data:", error);
                this.serverError = "Failed to fetch effect data.";
            }
        },

        // Submit the updated form data
        async update() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;
            const uuid = this.$route.params.uuid;

            try {
                const response = await this.$axios.put(`/effect/${uuid}`, this.effect); // Put request to update effect
                
                if (response.data.success) {
                    toast.success("Effect updated successfully!");
                    this.$router.push({ name: "EffectIndex" }); // Redirect to the effect list after success
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    toast.error("Validation errors occurred.");
                } else {
                    console.error("Error updating effect:", error);
                    this.serverError = "Failed to update effect.";
                }
            } finally {
                this.loading = false;
            }
        },

        // Fetch Causes for the autocomplete field
        async fetchCauses(search = "") {
            try {
                const response = await this.$axios.get("/get_causes", {
                    params: { search: search, limit: this.limit },
                });
                this.causes = response.data;
            } catch (error) {
                console.error("Error fetching causes:", error);
                toast.error("Failed to fetch causes.");
            }
        },

        // Format Cause Display
        formatCause(cause) {
            if (cause && cause.problem_note && cause.problem_note.company) {
                const problemNoteName = cause.problem_note.name || "No Problem Note";
                const companyName = cause.problem_note.company.name || "No Company Name";
                return `${cause.name} -- ${problemNoteName} -- ${companyName}`;
            }
            return "No Cause Data";
        },

        // Reset Form Data
        resetForm() {
            this.fetchEffect();  // Refetch the effect data to reset the form
            this.errors = {};  // Clear any previous errors
            this.$refs.form.resetValidation();  // Reset the form validation
        },
    },
};
</script>

<style scoped>
/* Optional styles */
</style>
