<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Edit Mechine Type</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-col cols="12" md="12">
                    <v-text-field
                        v-model="type.name"
                        :rules="[rules.required]"
                        label="Name"
                        outlined
                        :error-messages="errors.name ? errors.name : ''"
                    >
                        <template v-slot:label>
                            Name <span style="color: red">*</span>
                        </template>
                    </v-text-field>
                </v-col>

                <v-row>
                    <v-col col="6">
                        <v-autocomplete
                        v-model="type.company_id"
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
                    <v-col col="6">
                        <v-select
                            v-model="type.status"
                            :items="statusItems"
                            label="Type Status"
                            clearable
                        ></v-select>
                    </v-col>
                </v-row>


                <v-row>
                    <v-col cols="12" md="6">
                        <v-tooltip location="top" activator="parent">
                            <template v-slot:activator="{ props }">
                                <v-text-field
                                    v-model="type.partial_maintenance_day"
                                    label="Partial Maintenance Days"
                                ></v-text-field>
                            </template>
                            <span
                                >Enter the partial maintenance days here
                                ??</span
                            >
                        </v-tooltip>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-tooltip location="top" activator="parent">
                            <template v-slot:activator="{ props }">
                                <v-text-field
                                    v-model="type.full_maintenance_day"
                                    label="Full Maintenance Days"
                                ></v-text-field>
                            </template>
                            <span>Enter the full maintenance days here ??</span>
                        </v-tooltip>
                    </v-col>
                </v-row>

                <!-- Description Field -->
                <v-textarea
                    v-model="type.description"
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
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update type
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
            type: {
                name: "",
                company_id:null,
                partial_maintenance_day: "",
                full_maintenance_day: "",
                description: "",
                status: false, // Default to false (inactive)
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
            this.fetchtype();
        });
        this.fetchtype();
    },
    methods: {
        async fetchtype() {
            // Fetch the type data to populate the form
            const typesId = this.$route.params.uuid; // Assuming the type ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/mechine/type/${typesId}/edit`
                );
                this.type = response.data.mechinetype; // Populate form with the existing type data
                this.type.status =
                    this.type.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                this.serverError = "Error fetching type data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const typeId = this.$route.params.uuid; // Assuming type ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `mechine/type/${typeId}`,
                        this.type
                    );
                    console.log(response.data);
                    if (response.data.success) {
                        toast.success("type update successfully!");
                        this.$router.push({ name: "MechineTypeIndex" }); // Redirect to type list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update type.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating type. Please try again.");
                        this.serverError = "Error updating type.";
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
            this.fetchtype(); // Reset the form with existing type data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
