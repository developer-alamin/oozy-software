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
                <v-row>
                    <v-col col="12" md="6">
                        <v-col col="6">
                        <v-autocomplete
                        v-model="source.company_id"
                        :items="companies"
                        item-value="id"
                        :item-title="formatCompany"
                        outlined
                        clearable
                        density="comfortable"
                        :rules="[rules.required]"
                        :error-messages="errors.company_id ? errors.company_id : ''"
                        @update:search="fetchCompanies"
                        >
                        <template v-slot:label>
                            Select Company <span style="color: red">*</span>
                        </template>
                        </v-autocomplete>
                    </v-col>
                    </v-col>
                    <v-col col="12" md="6">
                        <v-select
                            v-model="source.status"
                            :items="statusItems"
                            label="Source Status"
                            clearable
                            :error-messages="errors.status ? errors.status : ''"
                        ></v-select>
                    </v-col>
                </v-row>

                <!-- Description Field -->
                <v-textarea
                    v-model="source.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
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
                company_id:null,
                description: "",
                status: false, // Default to false (inactive)
                rate_applicable: false,
            },
            limit: 5,
            companies:[],
            selectedCompany: null,
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchCompanies().then(() => {
            this.fetchsource();
        });
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
        formatCompany(company) {
        if (company) {
            console.log(company)
            if (typeof company === "number") {
                // Use strict equality (===) and ensure proper assignment in the find function
                company = this.companies.find((item) => item.id === company);
            }
            // Safely return the company name or a fallback if the name is missing
            return company && company.name ? company.name : "No Company Name";
            }
            // Fallback if no company data is provided
            return "No Company Data";
        },
        async fetchCompanies(search) {
            try {
                // Make a GET request to the '/get_companies' endpoint with query parameters
                const response = await this.$axios.get('/get_companies', {
                    params: {
                    search: this.search || '', // Use `this.search` or fallback to an empty string
                    limit: this.limit || 5,   // Use `this.limit` or fallback to default value (5)
                    },
                });
                // Update the companies array with the fetched data
                this.companies = response.data;
                } catch (error) {
                // Log any errors that occur during the request
                console.error('Error fetching companies:', error);
                // Optionally, handle the error (e.g., show an error message to the user)
                this.$toast.error('Failed to fetch companies. Please try again later.');
                }

        },
        resetForm() {
            this.fetchsource(); // Reset the form with existing source data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
