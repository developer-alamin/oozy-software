<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Create Group</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
               <v-row>
                <v-col col="12" md="12">
                    <!-- Name Field -->
                    <v-text-field
                        v-model="group.name"
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
                <v-col col="12" md="6">
                    <v-autocomplete
                        v-model="group.company_id"
                        :items="companies"
                        item-value="id"
                        item-title="name"
                        outlined
                        clearable
                        density="comfortable"
                        :rules="[rules.required]"
                        :error-messages="errors.company_id || ''"
                        @update:search="fetchCompanies"
                        no-filter
                        >
                        <template v-slot:label>
                            Select Company <span style="color: red">*</span>
                        </template>
                    </v-autocomplete>
                </v-col>
                    <v-col col="12" md="6">
                        <v-select
                            v-model="group.status"
                            :items="statusItems"
                            label="Group Status"
                            @change="updateStatus"
                            clearable
                        ></v-select>
                    </v-col>
               </v-row>
                <!-- Description Field -->
                <v-textarea
                    v-model="group.description"
                    label="Description"
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
                            @click="resetForm"
                            class="mr-3"
                        >
                            Reset Form
                        </v-btn>
                        <v-btn
                            type="submit"
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Create Group
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <v-alert v-if="serverError" type="error" class="my-4">
            {{ serverError }}
        </v-alert>
    </v-card>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            valid: false,
            loading: false,
            loadingTechnicians: false,
            statusItems: ["Active", "Inactive"],
            group: {
                company_id:null,
                name: "",
                description: "",
                status: "Active",
            }, 
            limit: 5,
            companies:[],
            selectedCompany: null,
            errors: {},
            search: "",
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    computed: {
        
    },
    methods: {
        async submit() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;

            const formData = new FormData();
            Object.entries(this.group).forEach(([key, value]) => {
                formData.append(key, value);
            });
            setTimeout(async () => {
                try {
                    const response = await this.$axios.post("/group", formData);
                    if (response.data.success) {
                        this.resetForm();
                        toast.success("Group Created successfully!");
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        console.log(error);
                        this.serverError =
                            "An error occurred. Please try again.";
                    }
                } finally {
                    this.loading = false;
                }
            }, 1000); // Delay time in milliseconds
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
            this.group.name = "";
            this.group.description = "";
            this.errors = {};
            if (this.$refs.form) {
                this.$refs.form.reset();
            }
        },
    },
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
