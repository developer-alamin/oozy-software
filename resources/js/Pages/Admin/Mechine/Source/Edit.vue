<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit source</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="source.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="source.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <v-select
                    v-model="source.status"
                    :items="statusItems"
                    label="Source Status"
                    clearable
                    :error-messages="errors.status ? errors.status : ''"
                ></v-select>

                <v-checkbox
                    v-model="source.rate_applicable"
                    label="Rate Applicable"
                    :error-messages="
                        errors.rate_applicable ? errors.rate_applicable : ''
                    "
                />

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
                            Update source
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <!-- Server Error Message -->
        <v-alert v-if="serverError" source="error" class="my-4">
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
            source: {
                name: "",
                description: "",
                status: false, // Default to false (inactive)
                rate_applicable: false,
            },
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchsource();
    },
    methods: {
        async fetchsource() {
            // Fetch the source data to populate the form
            const sourcesId = this.$route.params.uuid; // Assuming the source ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/mechine/source/${sourcesId}/edit`
                );
                this.source = response.data.source; // Populate form with the existing source data
                this.source.status =
                    this.source.status === "Active" ? "Active" : "Inactive";
                this.source.rate_applicable =
                    this.source.rate_applicable == "true" ||
                    this.source.rate_applicable == true;
            } catch (error) {
                this.serverError = "Error fetching source data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const sourceId = this.$route.params.uuid; // Assuming source ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `mechine/source/${sourceId}`,
                        this.source
                    );
                    console.log(response.data);
                    if (response.data.success) {
                        toast.success("source update successfully!");
                        this.$router.push({ name: "MechineSourceIndex" }); // Redirect to source list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update source.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating source. Please try again.");
                        this.serverError = "Error updating source.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchsource(); // Reset the form with existing source data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
