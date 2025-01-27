<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Edit Brand</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="brand.name"
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
                            v-model="brand.company_id"
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
                        <v-select
                            v-model="brand.status"
                            :items="statusItems"
                            label="Brand Status"
                            clearable
                            :error-messages="errors.status ? errors.status : ''"
                        ></v-select>
                    </v-col>
                </v-row>
                <!-- Description Field -->
                <v-textarea
                    v-model="brand.description"
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
                            Update Brand
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
            statusTypeItems: ["Mechine", "Parse"],
            brand: {
                name: "",
                company_id:null,
                type: false,
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
            this.fetchBrand();
        });
       
    },
    methods: {
        async fetchBrand() {
            // Fetch the brand data to populate the form
            const brandId = this.$route.params.uuid; // Assuming the brand ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/brand/${brandId}/edit`
                );
                // console.log(response.data);

                this.brand = response.data.brand; // Populate form with the existing brand data
                this.brand.status =
                    this.brand.status === "Active" ? "Active" : "Inactive";
                this.brand.type =
                    this.brand.type === "Mechine" ? "Mechine" : "Parse";
            } catch (error) {
                this.serverError = "Error fetching brand data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const brandId = this.$route.params.uuid; // Assuming brand ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/brand/${brandId}`,
                        this.brand
                    );
                    console.log(response)
                    if (response.data.success) {
                        toast.success("Brand update successfully!");
                        this.$router.push({ name: "BrandIndex" }); // Redirect to brand list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update brand.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating brand. Please try again.");
                        this.serverError = "Error updating brand.";
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
            this.fetchBrand(); // Reset the form with existing brand data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
