<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create model</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="model.brand_id"
                    :items="brands"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    chips
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.brand_id ? errors.brand_id : ''"
                    @update:search="fetchBrands"
                >
                    <template v-slot:label>
                        Select Brand <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <!-- Name Field -->
                <v-text-field
                    v-model="model.name"
                    :rules="[rules.required]"
                    label="Name"
                    density="comfortable"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- <v-select
                    v-model="model.type"
                    :rules="[rules.required]"
                    :items="statusModelItems"
                    label="Model Type"
                    density="comfortable"
                    clearable
                >
                    <template v-slot:label>
                        Model Type <span style="color: red">*</span>
                    </template>
                </v-select> -->
                <!-- Featured Checkbox -->
                <v-select
                    v-model="model.status"
                    density="comfortable"
                    :items="statusItems"
                    label="Model Status"
                    clearable
                ></v-select>

                <!-- Description Field -->
                <v-textarea
                    v-model="model.description"
                    density="comfortable"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

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
                            Create model
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
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "Inactive"],
            statusModelItems: ["Mechine", "Parse"],
            model: {
                brand_id: "",
                name: "",
                model_number: "",
                type: "Mechine",
                description: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            brands: [],
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
            Object.entries(this.model).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post(
                        "/models",
                        formData
                    );

                    if (response.data.success) {
                        toast.success("Model create successfully!");
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create model.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("An error occurred. Please try again !");
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
        async fetchBrands(search) {
            try {
                const response = await this.$axios.get(`/get_brand_alls`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.brands = response.data;
            } catch (error) {
                console.error("Error fetching brands:", error);
            }
        },
        resetForm() {
            this.model = {
                name: "",
                model_number: "",
                type: "",
                description: "",
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
