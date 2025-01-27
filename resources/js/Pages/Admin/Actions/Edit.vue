<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Edit Action</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <v-row>
                    <!-- Select Company -->
                    <v-col col="12" md="6">
                        <v-autocomplete
                            v-model="action.company_id"
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

                    <!-- Select Status -->
                    <v-col col="12" md="6">
                        <v-select
                            v-model="action.status"
                            :items="statusItems"
                            label="Action Status"
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

                <!-- Problem Name -->
                <v-textarea
                    v-model="action.name"
                    label="Action Name"
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
                           class="primary-color"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update Action
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
            action: {
                name: "",
                company_id: "",
                status: "",
            },
            companies: [],
            errors: {},
            serverError: null,
            limit: 5,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchCompanies().then(() => {
            this.fetchAction();
        });
    },
    methods: {
        // Fetch Action Details
        async fetchAction() {
            const uuid = this.$route.params.uuid; // UUID from route params
            try {
                const response = await this.$axios.get(`/action/${uuid}/edit`);
                console.log(response);
                
                this.action = response.data.item; // Populate form data
            } catch (error) {
                console.error("Error fetching action data:", error);
                this.serverError = "Failed to fetch action data.";
            }
        },

        // Submit Form
        async update() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;
            const uuid = this.$route.params.uuid;

            try {
                const response = await this.$axios.put(`/action/${uuid}`, this.action);
              
                if (response.data.success) {
                    toast.success("Action updated successfully!");
                    this.$router.push({ name: "ActionIndexPage" }); // Redirect to action index page
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    toast.error("Validation errors occurred.");
                } else {
                    console.error("Error updating action:", error);
                    this.serverError = "Failed to update action.";
                }
            } finally {
                this.loading = false;
            }
        },

        // Fetch Companies
        async fetchCompanies(search) {
            try {
                const response = await this.$axios.get("/get_companies", {
                    params: {
                        search: search || "",
                        limit: this.limit,
                    },
                });
                this.companies = response.data;
            } catch (error) {
                console.error("Error fetching companies:", error);
                toast.error("Failed to fetch companies.");
            }
        },

        // Format Company Display
        formatCompany(company) {
            return company && company.name ? company.name : "No Company Name";
        },

        // Reset Form
        resetForm() {
            this.fetchAction();
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>

<style scoped>
/* Optional styles */
</style>
