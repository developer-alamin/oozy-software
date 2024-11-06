<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create operator</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="operator.company_id"
                    :items="companys"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.company_id ? errors.company_id : ''"
                    @update:search="fetchCompanys"
                >
                    <template v-slot:label>
                        Select Company <span style="color: red">*</span>
                    </template>
                </v-autocomplete>
                <!-- Name Field -->
                <v-text-field
                    v-model="operator.name"
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
                    v-model="operator.type"
                    :items="typeItems"
                    :rules="[rules.required]"
                    label="Operator Type"
                    clearable
                >
                    <template v-slot:label>
                        Operator Type <span style="color: red">*</span>
                    </template>
                </v-select>
                <!-- Name Field -->
                <v-text-field
                    v-model="operator.email"
                    :rules="[rules.email]"
                    label="Email"
                    outlined
                    :error-messages="errors.email ? errors.email : ''"
                >
                    <template v-slot:label> Email </template>
                </v-text-field>

                <v-text-field
                    v-model="operator.phone"
                    label="Phone"
                    outlined
                    :error-messages="errors.phone ? errors.phone : ''"
                >
                    <template v-slot:label> Phone </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="operator.address"
                    label="Address"
                    :error-messages="errors.description ? errors.address : ''"
                />
                <!-- Description Field -->
                <v-textarea
                    v-model="operator.description"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <v-select
                    v-model="operator.status"
                    :items="statusItems"
                    label="Operator Status"
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
                            Create operator
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
            typeItems: ["General", "Special", "Manager"],
            operator: {
                company_id: "",
                name: "",
                type: "General",
                email: "",
                phone: "",
                photo: "",
                address: "",
                description: "",
                status: "Active", // New property for checkbox
            },
            companys: [],
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
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
            Object.entries(this.operator).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post(
                        "/operator",
                        formData
                    );

                    if (response.data.success) {
                        toast.success("Operator create successfully!");
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create operator.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to create operator.");
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
        async fetchCompanys(search) {
            try {
                const response = await this.$axios.get(`/get_companys`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                console.log(response.data);
                this.companys = response.data;
            } catch (error) {
                console.error("Error fetching companys:", error);
            }
        },

        resetForm() {
            this.operator = {
                name: "",
                email: "",
                phone: "",
                photo: "",
                address: "",
                description: "",
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
