<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create brand</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="brand.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    density="comfortable"
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <!-- <v-select
                    v-model="brand.type"
                    :rules="[rules.required]"
                    :items="statusTypeItems"
                    label="Brand Type"
                    density="comfortable"
                    clearable
                >
                    <template v-slot:label>
                        Brand Type <span style="color: red">*</span>
                    </template>
                </v-select> -->
                <v-select
                    v-model="brand.status"
                    :items="statusItems"
                    label="Brand Status"
                    @change="updateStatus"
                    density="comfortable"
                    clearable
                ></v-select>

                <!-- Description Field -->
                <v-textarea
                    v-model="brand.description"
                    density="comfortable"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Featured Checkbox -->
                <!-- <v-checkbox
                    v-model="brand.status"
                    label="Status"
                    :error-messages="errors.status ? errors.status : ''"
                >
                </v-checkbox> -->

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
                            Create brand
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
            statusItems: ["Active", "Inactive"],
            statusTypeItems: ["Mechine", "Parse"],
            brand: {
                name: "",
                type: "Mechine",
                description: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    methods: {
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.brand).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post("/brand", formData);

                    if (response.data.success) {
                        toast.success("Brand create successfully!");
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create brand.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("An error occurred. Please try again.");

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
            this.brand = {
                name: "",
                description: "",
                type: "",
                status: "", // Reset checkbox on form reset
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
};
</script>
