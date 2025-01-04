<template>
    <v-card outlined class="mx-auto my-5" max-width="500">
        <v-card-title>Create Company</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="company.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-select
                    v-model="company.status"
                    :items="statusItems"
                    label="Company Status"
                    clearable
                ></v-select>

                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <!-- Submit Button -->

                    <!-- Reset Button -->
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
                           Next
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>

    <!-- Server Error Message -->
    <v-alert v-if="serverError" type="error" class="my-4">
        {{ serverError }}
    </v-alert>

</template>
<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "In-active"],
            company: {
                role: "admin",
                name: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
            },
            visible: false,
            confirm_visible: false,
        };
    },
    methods: {
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.company).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post(
                        "company/next",
                        formData
                    );
                    if (response.data.success) {
                       const item = response.data.item;
                        
                        this.$router.push({ name: "CompanyNext", state: { item } });
                       // toast.success("Company create successfully!");
                        // localStorage.setItem("token", response.data.token);
                        //this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create company.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to create company.");
                        // Handle other server errors
                        this.serverError =
                            "An error occurred. Please try again.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000); // Simulates a 3-second loading duration
        },
        resetForm() {
            this.company = {
                role: "admin",
                name: "",
                status: "Active", // New property for checkbox
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
};
</script>
