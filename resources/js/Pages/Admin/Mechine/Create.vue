<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create machine</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="machine.machine_code"
                            :rules="[rules.required]"
                            label="Machine Code"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.machine_code ? errors.machine_code : ''
                            "
                        >
                            <template v-slot:label>
                                Machine Code<span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                            v-model="machine.name"
                            :rules="[rules.required]"
                            label="Machine Name"
                            outlined
                            density="comfortable"
                            :error-messages="errors.name ? errors.name : ''"
                        >
                            <template v-slot:label>
                                Machine Name <span style="color: red">*</span>
                            </template>
                        </v-text-field></v-col
                    >
                </v-row>

                <v-row>
                    <!-- <v-col cols="6">
                        <v-autocomplete
                            v-model="machine.company_id"
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
                    </v-col> -->
                    <v-col cols="12">
                        <v-autocomplete
                            v-model="machine.factory_id"
                            :items="factories"
                            item-value="id"
                            :item-title="formatFactory"
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
                        <!-- <v-autocomplete
                            v-model="machine.brand_id"
                            :items="brands"
                            item-value="id"
                            item-title="name"
                            label="Select Machine Brand"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.brand_id ? errors.brand_id : ''
                            "
                            @update:search="fetchBrands"
                            @update:model-value="onBrandChange"
                        >
                            <template v-slot:label>
                                Select Machine Brand
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete> -->

                        <v-autocomplete
                            v-model="machine.model_id"
                            :items="models"
                            item-value="id"
                            item-title="name"
                            label="Select Machine Model"
                            outlined
                            clearable
                            density="comfortable"
                            :loading="loadingModels"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.model_id ? errors.model_id : ''
                            "
                            @update:search="fetchModels"
                            @update:model-value="onModelChange"
                        >
                            <template v-slot:label>
                                Select Machine Model
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="machine.brand_id"
                            :items="brands"
                            item-value="id"
                            item-title="name"
                            label="Select Machine Brand"
                            outlined
                            clearable
                            density="comfortable"
                            :loading="loadingBrands"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.brand_id ? errors.brand_id : ''
                            "
                        >
                            <template v-slot:label>
                                Select Machine Brand
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>

                        <!-- Model Selection -->
                        <!-- <v-autocomplete
                            v-model="machine.model_id"
                            :items="models"
                            item-value="id"
                            item-title="name"
                            label="Select Machine Model"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.model_id ? errors.model_id : ''
                            "
                            @update:search="fetchModels"
                            :disabled="!machine.brand_id"
                        >
                            <template v-slot:label>
                                Select Machine Model
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete> -->
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="machine.machine_type_id"
                            :items="types"
                            item-value="id"
                            item-title="name"
                            label="Select Machine Type"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.machine_type_id
                                    ? errors.machine_type_id
                                    : ''
                            "
                            @update:search="fetchTypes"
                            @update:model-value="updatePreventiveServiceDays"
                        >
                            <template v-slot:label>
                                Select Machine Type
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>

                    <v-col cols="4">
                        <v-text-field
                            v-model="machine.partial_maintenance_day"
                            :rules="[rules.required]"
                            label="Machine Preventive Service Days"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.partial_maintenance_day
                                    ? errors.partial_maintenance_day
                                    : ''
                            "
                        >
                            <template v-slot:label>
                                Machine Preventive Service Days
                                <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="machine.full_maintenance_day"
                            :rules="[rules.required]"
                            label="Machine Preventive Service Days"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.full_maintenance_day
                                    ? errors.full_maintenance_day
                                    : ''
                            "
                        >
                            <template v-slot:label>
                                Full Maintenance Day
                                <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="12">
                        <v-autocomplete
                            v-model="machine.machine_source_id"
                            :items="sources"
                            item-value="id"
                            item-title="name"
                            label="Select Machine Source"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.machine_source_id
                                    ? errors.machine_source_id
                                    : ''
                            "
                            @update:model-value="checkRateApplicable"
                            @update:search="fetchSources"
                        >
                            <template v-slot:label>
                                Select Machine Source
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>

                <v-row
                    v-if="
                        isRateApplicable == true || isRateApplicable == 'true'
                    "
                >
                    <!-- <v-autocomplete
                            v-model="machine.rent_id"
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

                        </v-autocomplete> -->
                    <v-col cols="3">
                        <v-date-input
                            v-model="machine.rent_date"
                            label="Rent In Date"
                            density="comfortable"
                            :error-messages="
                                errors.rent_date ? errors.rent_date : ''
                            "
                            :model-value="machine.rent_date"
                        />
                    </v-col>
                    <v-col cols="3">
                        <v-text-field
                            v-model="machine.rent_name"
                            label="Rent Name"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.rent_name ? errors.rent_name : ''
                            "
                        >
                        </v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-text-field
                            v-model="machine.rent_price"
                            label="Price"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.rent_price ? errors.rent_price : ''
                            "
                        >
                        </v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-select
                            v-model="machine.rent_amount_type"
                            :items="rentAmountItems"
                            label="Rent Amount Type"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea
                            v-model="machine.rent_note"
                            row-height="20"
                            rows="2"
                            label="Rent Note"
                            density="comfortable"
                            :error-messages="
                                errors.rent_note ? errors.rent_note : ''
                            "
                        />
                    </v-col>
                </v-row>

                <!-- Name Field -->

                <!-- <v-text-field
                    v-model="machine.purchase_date"
                    label="Purchase Date"
                    type="date"
                    outlined
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="
                        errors.purchase_date ? errors.purchase_date : ''
                    "
                >
                    <template v-slot:label>
                        Purchase Date <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <v-text-field
                    v-model="machine.rent_date"
                    label="Rent Date"
                    type="date"
                    outlined
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.rent_date ? errors.rent_date : ''"
                >
                    <template v-slot:label>
                        Rent Date <span style="color: red">*</span>
                    </template>
                </v-text-field> -->
                <v-row>
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="machine.supplier_id"
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
                            <!-- <template v-slot:label>
                        Select Supplier
                        <span style="color: red">*</span>
                    </template> -->
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="4">
                        <v-date-input
                            v-model="machine.purchase_date"
                            label="Purchase Date"
                            density="comfortable"
                            :error-messages="
                                errors.purchase_date ? errors.purchase_date : ''
                            "
                        />
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="machine.purchase_price"
                            label="Purchase Price"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.purchase_price
                                    ? errors.purchase_price
                                    : ''
                            "
                        >
                            <template v-slot:label> Purchase Price </template>
                        </v-text-field>
                    </v-col>
                </v-row>

                <!-- <v-text-field
                    v-model="machine.factory_code"
                    :rules="[rules.factory_code]"
                    label="Factory Code"
                    outlined
                    density="comfortable"
                    :error-messages="
                        errors.factory_code ? errors.factory_code : ''
                    "
                >
                    <template v-slot:label> Factory Code </template>
                </v-text-field> -->

                <v-textarea
                    v-model="machine.note"
                    label="Note"
                    density="comfortable"
                    :error-messages="errors.note ? errors.note : ''"
                />

                <v-autocomplete
                    v-model="machine.machine_status_id"
                    :items="machine_statuses"
                    item-value="id"
                    item-title="name"
                    label="Select Machine Status"
                    density="comfortable"
                    clearable
                    :error-messages="
                        errors.machine_status_id ? errors.machine_status_id : ''
                    "
                    @update:search="fetchMachineStatus"
                >
                    <template v-slot:label>
                        Select Machine Status
                        <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

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
                            Create Machine
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
            rentAmountItems: ["Monthly", "Yearly", "Fixed"],

            machine: {
                rent_date: new Date(),
                purchase_date: null,
                purchase_price: 0,
                name: "",
                date: null,
                company_id: null,
                factory_id: null,
                brand_id: null,
                model_id: null,
                machine_type_id: null,
                partial_maintenance_day: "",
                full_maintenance_day: "",
                machine_source_id: null,
                supplier_id: null,
                rent_id: null,
                machine_code: "",
                note: "",
                machine_status_id: null, // New property for checkbox
                rent_note: "",
                rent_amount_type: null,
                rent_price: "",
                rent_name: "",
            },
            isRateApplicable: false,
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companys: [], // Array to store Company data
            factories: [], // Array to store factories data
            brands: [], // Array to store brands data
            models: [], // Array to store models data
            selectedModel: null, // Selected model ID
            selectedBrand: null, // Selected brand ID
            types: [], // Array to store types data
            sources: [], // Array to store sources data
            suppliers: [], // Array to store suppliers data
            rents: [], // Array to store rents data
            machine_statuses: [],
            selectedCompany: null, // Bound to selected Company in v-autocomplete
            currentDate: new Date(),

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
        this.fetchMachineStatus().then(() => {
            this.setDefaultStatus();
        });
        // this.fetchBrands();
        this.generateMachineCode();
        // this.fetchModels();
    },
    methods: {
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.machine).forEach(([key, value]) => {
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
                        "/machine-assing",
                        formData
                    );
                    console.log(response.data);

                    if (response.data.success) {
                        toast.success("machine assing create successfully!");
                        // localStorage.setItem("token", response.data.token);
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create machine assing.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to create machine assing.");
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

        checkRateApplicable(id) {
            const selectedSource = this.sources.find(
                (source) => source.id === id
            );
            this.isRateApplicable = selectedSource?.rate_applicable || false;
            // console.log(this.isRateApplicable);

            // if (!this.isRateApplicable) {
            //     this.machine.rent_id = null;
            //     this.machine.rent_date = null;
            // }
        },
        resetForm() {
            this.machine = {
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
                const response = await this.$axios.get(`/get_companies`, {
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

        // Fetch brands based on search term
        // async fetchBrands(search) {
        //     try {
        //         const response = await this.$axios.get("/get_brands", {
        //             params: { search },
        //         });
        //         this.brands = response.data;
        //     } catch (error) {
        //         console.error("Error fetching brands:", error);
        //     }
        // },

        // Handle brand change, fetch models based on selected brand
        // async onBrandChange() {
        //     this.machine.model_id = null;
        //     if (!this.machine.brand_id) {
        //         this.models = []; // Clear models if no brand is selected
        //         return;
        //     }

        //     try {
        //         const response = await this.$axios.get("/get_models", {
        //             params: { brand_id: this.machine.brand_id },
        //         });
        //         this.models = response.data;
        //     } catch (error) {
        //         console.error("Error fetching models:", error);
        //     }
        // },

        // Fetch models based on search term (for model autocomplete)
        // async fetchModels(search) {
        //     try {
        //         const response = await this.$axios.get("/get_models", {
        //             params: { search, brand_id: this.machine.brand_id },
        //         });
        //         this.models = response.data;
        //     } catch (error) {
        //         console.error("Error fetching models:", error);
        //     }
        // },
        async fetchModels(search) {
            try {
                this.loadingModels = true;
                const response = await this.$axios.get("/get_models", {
                    params: { search, limit: 5 },
                });
                this.models = response.data;
                console.log(response.data);
            } catch (error) {
                console.error("Error fetching models:", error);
            } finally {
                this.loadingModels = false;
            }
        },
        async fetchBrands(search) {
            try {
                if (!this.machine.model_id) {
                    this.brands = [];
                    return;
                }

                this.loadingBrands = true;
                const response = await this.$axios.get("/get_brands", {
                    params: {
                        search,
                        model_id: this.machine.model_id,
                        limit: 5,
                    },
                });
                this.brands = response.data;
            } catch (error) {
                console.error("Error fetching brands:", error);
            } finally {
                this.loadingBrands = false;
            }
        },
        onModelChange() {
            this.machine.brand_id = null; // Reset brand selection when model changes
            this.fetchBrands("");
        },
        async generateMachineCode() {
            try {
                const response = await this.$axios.get(
                    "/generate-machine-code"
                );
                console.log(response.data);

                this.machine.machine_code = response.data.machine_code;
            } catch (error) {
                console.error("Error fetching machine code:", error);
            }
        },
        formatFactory(factory) {
            if (factory) {
                return `${factory.name} -- ${factory.user?.name || "No User"}`;
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
                (type) => type.id === this.machine.machine_type_id
            );
            // console.log("Selected Type:", selectedType); // Debugging log

            this.machine.partial_maintenance_day = selectedType
                ? selectedType.partial_maintenance_day
                : "";
            this.machine.full_maintenance_day = selectedType
                ? selectedType.full_maintenance_day
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

        async fetchMachineStatus(search) {
            try {
                const response = await this.$axios.get(
                    `/get_machine_statuses`,
                    {
                        params: {
                            search: search,
                            limit: this.limit,
                        },
                    }
                );
                // console.log(response.data);
                this.machine_statuses = response.data;
            } catch (error) {
                console.error("Error fetching machine status:", error);
            }
        },
        setDefaultStatus() {
            const idleStatus = this.machine_statuses.find(
                (status) => status.name == "Idle"
            );
            // console.log(idleStatus);

            if (idleStatus) {
                this.machine.machine_status_id = idleStatus.id;
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
