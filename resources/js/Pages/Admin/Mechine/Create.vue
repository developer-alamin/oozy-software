<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create mechine</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-text-field
                    v-model="mechine_assing.name"
                    :rules="[rules.required]"
                    label="Mechine Name"
                    outlined
                    density="comfortable"
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Mechine Name <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <v-autocomplete
                    v-model="mechine_assing.company_id"
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

                <v-autocomplete
                    v-model="mechine_assing.factory_id"
                    :items="factories"
                    item-value="id"
                    item-title="name"
                    label="Select Factory"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.factory_id ? errors.factory_id : ''"
                    @update:search="fetchFactories"
                >
                    <template v-slot:label>
                        Select Factory <span style="color: red">*</span>
                    </template>
                </v-autocomplete>
                <v-autocomplete
                    v-model="mechine_assing.brand_id"
                    :items="brands"
                    item-value="id"
                    item-title="name"
                    label="Select Mechine Brand"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.brand_id ? errors.brand_id : ''"
                    @update:search="fetchBrands"
                >
                    <template v-slot:label>
                        Select Mechine Brand <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <v-autocomplete
                    v-model="mechine_assing.model_id"
                    :items="models"
                    item-value="id"
                    item-title="name"
                    label="Select Mechine Model"
                    density="comfortable"
                    clearable
                    :rules="[rules.required]"
                    :error-messages="errors.model_id ? errors.model_id : ''"
                    @update:search="fetchModels"
                >
                    <template v-slot:label>
                        Select Mechine Model <span style="color: red">*</span>
                    </template>
                </v-autocomplete>
                <v-autocomplete
                    v-model="mechine_assing.type_id"
                    :items="types"
                    item-value="id"
                    item-title="name"
                    label="Select Mechine Type"
                    density="comfortable"
                    clearable
                    :rules="[rules.required]"
                    :error-messages="errors.type_id ? errors.type_id : ''"
                    @update:search="fetchTypes"
                    @update:model-value="updatePreventiveServiceDays"
                >
                    <template v-slot:label>
                        Select Mechine Type <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <v-text-field
                    v-model="mechine_assing.preventive_service_days"
                    :rules="[rules.required]"
                    label="Mechine Preventive Service Days"
                    outlined
                    density="comfortable"
                    :error-messages="
                        errors.preventive_service_days
                            ? errors.preventive_service_days
                            : ''
                    "
                >
                    <template v-slot:label>
                        Mechine Preventive Service Days
                        <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-autocomplete
                    v-model="mechine_assing.source_id"
                    :items="sources"
                    item-value="id"
                    item-title="name"
                    label="Select Mechine Source"
                    density="comfortable"
                    clearable
                    :rules="[rules.required]"
                    :error-messages="errors.source_id ? errors.source_id : ''"
                    @update:search="fetchSources"
                >
                    <template v-slot:label>
                        Select Mechine Source <span style="color: red">*</span>
                    </template>
                </v-autocomplete>
                <v-autocomplete
                    v-model="mechine_assing.supplie_id"
                    :items="suppliers"
                    item-value="id"
                    item-title="name"
                    label="Select Supplier"
                    density="comfortable"
                    clearable
                    :error-messages="errors.supplie_id ? errors.supplie_id : ''"
                    @update:search="fetchSuppliers"
                >
                    <!-- <template v-slot:label>
                        Select Supplier
                        <span style="color: red">*</span>
                    </template> -->
                </v-autocomplete>
                <v-autocomplete
                    v-model="mechine_assing.rent_id"
                    :items="rents"
                    item-value="id"
                    item-title="name"
                    label="Select Rent"
                    density="comfortable"
                    clearable
                    :error-messages="errors.rent_id ? errors.rent_id : ''"
                    @update:search="fetchRents"
                >
                    <!-- <template v-slot:label>
                        Select Rent
                        <span style="color: red">*</span>
                    </template> -->
                </v-autocomplete>

                <!-- Name Field -->
                <v-text-field
                    v-model="mechine_assing.mechine_code"
                    label="Mechine Code"
                    outlined
                    density="comfortable"
                    :error-messages="
                        errors.mechine_code ? errors.mechine_code : ''
                    "
                >
                </v-text-field>

                <v-text-field
                    v-model="mechine_assing.phone"
                    label="Phone"
                    outlined
                    density="comfortable"
                    :error-messages="errors.phone ? errors.phone : ''"
                >
                    <template v-slot:label> Phone </template>
                </v-text-field>

                <v-text-field
                    v-model="mechine_assing.factory_code"
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

                <v-textarea
                    v-model="mechine_assing.note"
                    label="Note"
                    density="comfortable"
                    :error-messages="errors.note ? errors.note : ''"
                />
                <v-select
                    v-model="mechine_assing.status"
                    :items="statusItems"
                    label="Factory Status"
                    clearable
                    density="comfortable"
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
                            Create Mechine
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

            mechine_assing: {
                name: "",
                company_id: null,
                factory_id: null,
                brand_id: null,
                model_id: null,
                type_id: null,
                preventive_service_days: "",
                source_id: null,
                supplie_id: null,
                rent_id: null,
                mechine_code: "",
                phone: "",
                note: "",
                factory_code: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companys: [], // Array to store Company data
            factories: [], // Array to store factories data
            brands: [], // Array to store brands data
            models: [], // Array to store models data
            types: [], // Array to store types data
            sources: [], // Array to store sources data
            suppliers: [], // Array to store suppliers data
            rents: [], // Array to store rents data
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
                        "/mechine-assing",
                        formData
                    );
                    console.log(response.data);

                    if (response.data.success) {
                        toast.success("mechine assing create successfully!");
                        // localStorage.setItem("token", response.data.token);
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create mechine assing.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to create mechine assing.");
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
            this.mechine_assing = {
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
                // console.log(response.data);
                this.companys = response.data;
            } catch (error) {
                console.error("Error fetching companys:", error);
            }
        },
        async fetchFactories(search) {
            try {
                const response = await this.$axios.get(`/get_factories`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.factories = response.data;
            } catch (error) {
                console.error("Error fetching factories:", error);
            }
        },
        async fetchBrands(search) {
            try {
                const response = await this.$axios.get(`/get_brands`, {
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
        async fetchModels(search) {
            try {
                const response = await this.$axios.get(`/get_models`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.models = response.data;
            } catch (error) {
                console.error("Error fetching models:", error);
            }
        },
        async fetchTypes(search) {
            try {
                const response = await this.$axios.get(`/get_types`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.types = response.data;
            } catch (error) {
                console.error("Error fetching types:", error);
            }
        },
        updatePreventiveServiceDays() {
            const selectedType = this.types.find(
                (type) => type.id === this.mechine_assing.type_id
            );
            console.log("Selected Type:", selectedType); // Debugging log

            this.mechine_assing.preventive_service_days = selectedType
                ? selectedType.day
                : "";
        },
        async fetchSources(search) {
            try {
                const response = await this.$axios.get(`/get_sources`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.sources = response.data;
            } catch (error) {
                console.error("Error fetching sources:", error);
            }
        },
        async fetchSuppliers(search) {
            try {
                const response = await this.$axios.get(`/get_suppliers`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.suppliers = response.data;
            } catch (error) {
                console.error("Error fetching suppliers:", error);
            }
        },
        async fetchRents(search) {
            try {
                const response = await this.$axios.get(`/get_rents`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.rents = response.data;
            } catch (error) {
                console.error("Error fetching rents:", error);
            }
        },
    },
};
</script>
