<template>
    <v-card outlined class="mx-auto my-5" >
        <v-card-title>Edit Unit</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="unit.name"
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
                        <v-autocomplete
                            v-model="unit.company_id"
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
                    <v-col col="12" md="6">
                        <!-- Status Field (Checkbox) -->
                        <v-select
                            v-model="unit.status"
                            :items="statusItems"
                            label="Unit Status"
                            clearable
                        ></v-select>
                    </v-col>
                </v-row>
                <!-- Description Field -->
                <v-textarea
                    v-model="unit.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
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
                            class="primary-color"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Parse Update Unit
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
            unit: {
                name: "",
                company_id:null,
                description: "",
                status: "", // Default to false (inactive)
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
            this.fetchUnit();
        });
    },
    methods: {
        async fetchUnit() {
            // Fetch the unit data to populate the form
            const unitId = this.$route.params.uuid; // Assuming the unit ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/parse-unit/${unitId}/edit`
                );
                this.unit = response.data.unit; // Populate form with the existing unit data
                // console.log(this.unit);

                this.unit.status =
                    this.unit.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                console.log(error);
                this.serverError = "Error fetching parse unit data." + error;
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const unitId = this.$route.params.uuid; // Assuming unit ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/parse-unit/${unitId}`,
                        this.unit
                    );

                    if (response.data.success) {
                        toast.success("Parse Unit updated successfully!");
                        this.$router.push({ name: "ParseUnitIndex" }); // Redirect to unit list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update unit.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating parse unit.");
                        this.serverError = "Error updating unit.";
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
            this.fetchUnit(); // Reset the form with existing unit data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
