<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create factory</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-row>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="factory.company_id"
                            :items="companies"
                            item-value="id"
                            item-title="name"
                            outlined
                            clearable
                            chips
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.company_id ? errors.company_id : ''
                            "
                            @update:search="fetchCompanies"
                        >
                            <template v-slot:label>
                                Select Company <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                            v-model="factory.name"
                            :rules="[rules.required]"
                            label="Factory Name"
                            outlined
                            density="comfortable"
                            :error-messages="errors.name ? errors.name : ''"
                        >
                            <template v-slot:label>
                                Factory Name <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                </v-row>
                <!-- Name Field -->
                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="factory.email"
                            label="Email"
                            outlined
                            density="comfortable"
                            :error-messages="errors.email ? errors.email : ''"
                        >
                            <!-- <template v-slot:label>
                                Email <span style="color: red">*</span>
                            </template> -->
                        </v-text-field></v-col
                    >
                    <v-col cols="6">
                        <v-text-field
                            v-model="factory.phone"
                            label="Phone"
                            outlined
                            density="comfortable"
                            :error-messages="errors.phone ? errors.phone : ''"
                        >
                            <template v-slot:label> Phone </template>
                        </v-text-field>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="4">
                        <v-text-field
                            v-model="factory.factory_owner"
                            :rules="[rules.factory_owner]"
                            label="Factory Woner"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.factory_owner ? errors.factory_owner : ''
                            "
                        >
                            <template v-slot:label>
                                Factory Woner Name</template
                            >
                        </v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="factory.factory_size"
                            :rules="[rules.factory_size]"
                            label="Factory Code"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.factory_size ? errors.factory_size : ''
                            "
                        >
                            <template v-slot:label> Factory Size </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="factory.factory_capacity"
                            :rules="[rules.factory_capacity]"
                            label="Factory Code"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.factory_capacity
                                    ? errors.factory_capacity
                                    : ''
                            "
                        >
                            <template v-slot:label> Factory Capacity </template>
                        </v-text-field>
                    </v-col>
                    <!-- <v-col cols="4">
                        <v-select
                            v-model="factory.status"
                            :items="statusItems"
                            label="Factory Status"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col> -->
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="factory.factory_code"
                            :rules="[rules.factory_code]"
                            label="Factory Code"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.factory_code ? errors.factory_code : ''
                            "
                        >
                            <template v-slot:label> Factory Code </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="6">
                        <v-select
                            v-model="factory.status"
                            :items="statusItems"
                            label="Factory Status"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col>
                </v-row>

                <v-textarea
                    v-model="factory.location"
                    label="Location"
                    density="comfortable"
                    :error-messages="errors.location ? errors.location : ''"
                />
                <v-textarea
                    v-model="factory.note"
                    label="Note"
                    density="comfortable"
                    :error-messages="errors.note ? errors.note : ''"
                />

                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <!-- Submit Button -->
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
                            Create factory
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

            factory: {
                company_id: null,
                name: "",
                email: "",
                phone: "",
                location: "",
                note: "",
                factory_code: "",
                factory_owner: "",
                factory_size: "",
                factory_capacity: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companies: [], // Array to store Company data
            selectedCompany: null, // Bound to selected Company in v-autocomplete

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
            // const formData = new FormData();
            // Object.entries(this.factory).forEach(([key, value]) => {
            //     formData.append(key, value);
            // });
            const formData = new FormData();
            Object.entries(this.factory).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach((val) => formData.append(`${key}[]`, val));
                } else {
                    formData.append(key, value);
                }
            });
            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post(
                        "/factory",
                        formData
                    );
                    console.log(response.data);

                    if (response.data.success) {
                        toast.success("Factory create successfully!");
                        // localStorage.setItem("token", response.data.token);
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create factory.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to create factory.");
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
                company_id: "",
                name: "",
                email: "",
                phone: "",
                factory_code: "",
                location: "",
                note: "",
                status: "Active", // New property for checkbox
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },

        async fetchCompanies(search) {
            try {
                const response = await this.$axios.get(`/get_companies`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.companies = response.data;
            } catch (error) {
                console.error("Error fetching companies:", error);
            }
        },
    },
};
</script>
