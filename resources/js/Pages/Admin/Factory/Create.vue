<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create factory</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="factory.company_id"
                    :items="companys"
                    item-value="id"
                    item-title="name"
                    label="Select Company"
                    @update:search="fetchCompanys"
                />
                <!-- Name Field -->
                <v-text-field
                    v-model="factory.name"
                    :rules="[rules.required]"
                    label="Factory Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Factory Name <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <v-autocomplete
                    v-model="factory.floor_id"
                    :items="floors"
                    item-value="id"
                    item-title="name"
                    label="Select Floor"
                    @update:search="fetchFloors"
                />
                <v-autocomplete
                    v-model="factory.unit_id"
                    :items="units"
                    item-value="id"
                    item-title="name"
                    label="Select Unit"
                    @update:search="fetchUnit"
                />

                <v-autocomplete
                    v-model="factory.line_id"
                    :items="lines"
                    item-value="id"
                    item-title="name"
                    label="Select Line"
                    @update:search="fetchLines"
                />

                <!-- Name Field -->
                <v-text-field
                    v-model="factory.email"
                    :rules="[rules.required, rules.email]"
                    label="Email"
                    outlined
                    :error-messages="errors.email ? errors.email : ''"
                >
                    <template v-slot:label>
                        Email <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-text-field
                    v-model="factory.phone"
                    :rules="[rules.phone]"
                    label="Phone"
                    outlined
                    :error-messages="errors.phone ? errors.phone : ''"
                >
                    <template v-slot:label> Phone </template>
                </v-text-field>

                <v-text-field
                    v-model="factory.factory_code"
                    :rules="[rules.factory_code]"
                    label="Factory Code"
                    outlined
                    :error-messages="
                        errors.factory_code ? errors.factory_code : ''
                    "
                >
                    <template v-slot:label> Factory Code </template>
                </v-text-field>

                <v-textarea
                    v-model="factory.location"
                    label="Location"
                    :error-messages="errors.location ? errors.location : ''"
                />
                <v-select
                    v-model="factory.status"
                    :items="statusItems"
                    label="factory Status"
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
                floor_id: null,
                unit_id: null,
                line_id: null,
                name: "",
                email: "",
                phone: "",
                location: "",
                factory_code: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companys: [], // Array to store Company data
            floors: [], // Array to store Floor data
            units: [], // Array to store Unit data
            lines: [], // Array to store Line data
            selectedCompany: null, // Bound to selected Company in v-autocomplete

            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
                confirm_password: (value) =>
                    value === this.company.password || "Passwords must match.", // Confirms password matches
                phone: (value) =>
                    /^\d{11}$/.test(value) || "Phone number must be valid.",
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
            Object.entries(this.factory).forEach(([key, value]) => {
                formData.append(key, value);
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
                status: "Active", // New property for checkbox
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
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
        async fetchFloors(search) {
            try {
                const response = await this.$axios.get(`/get_floors`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                console.log(response.data);
                this.floors = response.data;
            } catch (error) {
                console.error("Error fetching floors:", error);
            }
        },
        async fetchUnit(search) {
            try {
                const response = await this.$axios.get(`/get_units`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                console.log(response.data);
                this.units = response.data;
            } catch (error) {
                console.error("Error fetching units:", error);
            }
        },
        async fetchLines(search) {
            try {
                const response = await this.$axios.get(`/get_lines`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                console.log(response.data);
                this.lines = response.data;
            } catch (error) {
                console.error("Error fetching lines:", error);
            }
        },
    },
};
</script>
