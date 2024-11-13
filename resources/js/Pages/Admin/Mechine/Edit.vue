<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Edit Mechine</v-card-title>
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
                <v-row>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.company_id"
                            :items="companys"
                            item-value="id"
                            item-title="name"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.company_id ? errors.company_id : ''
                            "
                            @update:search="fetchCompanys"
                        >
                            <template v-slot:label>
                                Select Company <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
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
                            :error-messages="
                                errors.factory_id ? errors.factory_id : ''
                            "
                            @update:search="fetchFactories"
                        >
                            <template v-slot:label>
                                Select Factory <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
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
                            :error-messages="
                                errors.brand_id ? errors.brand_id : ''
                            "
                            @update:search="fetchBrands"
                        >
                            <template v-slot:label>
                                Select Mechine Brand
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.model_id"
                            :items="models"
                            item-value="id"
                            item-title="name"
                            label="Select Mechine Model"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.model_id ? errors.model_id : ''
                            "
                            @update:search="fetchModels"
                        >
                            <template v-slot:label>
                                Select Mechine Model
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.mechine_type_id"
                            :items="types"
                            item-value="id"
                            item-title="name"
                            label="Select Mechine Type"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.mechine_type_id
                                    ? errors.mechine_type_id
                                    : ''
                            "
                            @update:search="fetchTypes"
                            @update:model-value="updatePreventiveServiceDays"
                        >
                            <template v-slot:label>
                                Select Mechine Type
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
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
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="6">
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
                    </v-col>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.mechine_source_id"
                            :items="sources"
                            item-value="id"
                            item-title="name"
                            label="Select Mechine Source"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.mechine_source_id
                                    ? errors.mechine_source_id
                                    : ''
                            "
                            @update:search="fetchSources"
                        >
                            <template v-slot:label>
                                Select Mechine Source
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.rent_id"
                            :items="rents"
                            item-value="id"
                            item-title="name"
                            label="Select Rent"
                            density="comfortable"
                            clearable
                            :error-messages="
                                errors.rent_id ? errors.rent_id : ''
                            "
                            @update:search="fetchRents"
                        >
                            <!-- <template v-slot:label>
                        Select Rent
                        <span style="color: red">*</span>
                    </template> -->
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
                        <v-date-input
                            v-model="mechine_assing.rent_date"
                            label="Rent Date"
                            density="comfortable"
                            :error-messages="
                                errors.rent_date ? errors.rent_date : ''
                            "
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="mechine_assing.supplier_id"
                            :items="suppliers"
                            item-value="id"
                            item-title="name"
                            label="Select Supplier"
                            density="comfortable"
                            clearable
                            :error-messages="
                                errors.supplier_id ? errors.supplier_id : ''
                            "
                            @update:search="fetchSuppliers"
                        >
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="4">
                        <v-date-input
                            v-model="mechine_assing.purchase_date"
                            label="Purchase Date"
                            density="comfortable"
                            :error-messages="
                                errors.purchase_date ? errors.purchase_date : ''
                            "
                        />
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="mechine_assing.purchace_price"
                            label="Purchase Price"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.purchace_price
                                    ? errors.purchace_price
                                    : ''
                            "
                        >
                            <template v-slot:label> Purchase Price </template>
                        </v-text-field>
                    </v-col>
                </v-row>
                <v-textarea
                    v-model="mechine_assing.note"
                    label="Note"
                    density="comfortable"
                    :error-messages="errors.note ? errors.note : ''"
                />
                <v-select
                    v-model="mechine_assing.status"
                    :items="statusItems"
                    label="Mechine Status"
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
                            Edit Mechine
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
// import VDateInput from "../../Components/VDateInput.vue";

export default {
    // components: {
    //     VDateInput,
    // },
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: [
                "Preventive",
                "Production",
                "Breakdown",
                "Under Maintenance",
                "Loan",
                "Idol",
                "AsFactory",
                "Scraped",
            ],

            mechine_assing: {
                rent_date: null,
                purchase_date: null,
                purchace_price: 0,
                name: "",
                date: null,
                company_id: null,
                factory_id: null,
                brand_id: null,
                model_id: null,
                mechine_type_id: null,
                preventive_service_days: "",
                mechine_source_id: null,
                supplier_id: null,
                rent_id: null,
                mechine_code: "",
                phone: "",
                note: "",
                factory_code: "",
                status: "Preventive", // New property for checkbox
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
    computed: {
        // Function to limit the allowed dates within the min and max date range
        allowedDates() {
            return (date) => {
                return date >= new Date();
            };
        },
    },
    created() {
        this.fetchCompanys().then(() => {
            this.fetchMechine();
        });

        this.fetchFactories().then(() => {
            this.fetchMechine();
        });

        this.fetchBrands().then(() => {
            this.fetchMechine();
        });
        this.fetchModels().then(() => {
            this.fetchMechine();
        });
        this.fetchTypes().then(() => {
            this.fetchMechine();
        });
        this.fetchSources().then(() => {
            this.fetchMechine();
        });
        this.fetchSuppliers().then(() => {
            this.fetchMechine();
        });
        this.fetchRents().then(() => {
            this.fetchMechine();
        });
    },
    methods: {
        async fetchMechine() {
            const mecineAssingId = this.$route.params.uuid;
            try {
                const response = await this.$axios.get(
                    `/mechine/assing/${mecineAssingId}/edit`
                );
                this.mechine_assing = response.data.mechineAssing;
                this.mechine_assing.status = this.statusItems.includes(
                    this.mechine_assing.status
                )
                    ? this.mechine_assing.status
                    : "";

                // Set the selected company based on the company_id
                const selectedCompany = this.companys.find(
                    (c) => c.id === this.mechine_assing.company_id
                );
                if (selectedCompany) {
                    this.mechine_assing.company_id = selectedCompany.id; // Set the company_id for v-autocomplete
                }
                // factories
                const selectedFactory = this.factories.find(
                    (c) => c.id === this.mechine_assing.factory_id
                );
                if (selectedFactory) {
                    this.mechine_assing.factory_id = selectedFactory.id; // Set the company_id for v-autocomplete
                }
                // Brands
                const selectedBrand = this.factories.find(
                    (c) => c.id === this.mechine_assing.brand_id
                );
                if (selectedBrand) {
                    this.mechine_assing.brand_id = selectedBrand.id; // Set the company_id for v-autocomplete
                }

                // Models
                const selectedModel = this.factories.find(
                    (c) => c.id === this.mechine_assing.model_id
                );
                if (selectedModel) {
                    this.mechine_assing.model_id = selectedModel.id; // Set the company_id for v-autocomplete
                }

                // Type
                const selectedType = this.factories.find(
                    (c) => c.id === this.mechine_assing.mechine_type_id
                );
                if (selectedType) {
                    this.mechine_assing.mechine_type_id = selectedType.id; // Set the company_id for v-autocomplete
                }

                // Source
                const selectedSource = this.factories.find(
                    (c) => c.id === this.mechine_assing.mechine_source_id
                );
                if (selectedSource) {
                    this.mechine_assing.mechine_source_id = selectedSource.id; // Set the company_id for v-autocomplete
                }
                // Supplier
                const selectedSupplier = this.factories.find(
                    (c) => c.id === this.mechine_assing.supplier_id
                );
                if (selectedSupplier) {
                    this.mechine_assing.supplier_id = selectedSupplier.id; // Set the company_id for v-autocomplete
                }
                // Rent
                const selectedRent = this.factories.find(
                    (c) => c.id === this.mechine_assing.rent_id
                );
                if (selectedRent) {
                    this.mechine_assing.rent_id = selectedRent.id; // Set the company_id for v-autocomplete
                }
            } catch (error) {
                this.serverError = "Error fetching technician data.";
            }
        },

        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const mechineId = this.$route.params.uuid; // Assuming type ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.post(
                        `mechine/transfer/${mechineId}`,
                        this.mechine_assing
                    );
                    // console.log(response.data);
                    if (response.data.success) {
                        toast.success("mechine transfer successfully!");
                        this.$router.push({ name: "MechineIndex" }); // Redirect to type list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update mechine transfer .");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error(
                            "Error updating mechine transfer . Please try again."
                        );
                        this.serverError = "Error mechine transfer .";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },

        async submitOnd() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.mechine_assing).forEach(([key, value]) => {
                formData.append(key, value);
            });
            // const formData = new FormData();
            // Object.entries(this.factory).forEach(([key, value]) => {
            //     if (Array.isArray(value)) {
            //         value.forEach((val) => formData.append(`${key}[]`, val));
            //     } else {
            //         formData.append(key, value);
            //     }
            // });
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
                status: "Preventive", // New property for checkbox
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
                (type) => type.id === this.mechine_assing.mechine_type_id
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
