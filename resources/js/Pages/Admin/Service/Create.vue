<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create Service</v-card-title>
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
                            v-model="mechine_assing.mechine_id"
                            :items="mechines"
                            item-value="id"
                            item-title="name"
                            label="Select Mechine"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.mechine_id ? errors.mechine_id : ''
                            "
                            @update:search="fetchMechins"
                        >
                            <template v-slot:label>
                                Select Mechine <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>
                <v-row>
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
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.operator_id"
                            :items="operators"
                            item-value="id"
                            item-title="name"
                            label="Select Operator"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.operator_id ? errors.operator_id : ''
                            "
                            @update:search="fetchOperator"
                        >
                            <template v-slot:label>
                                Select Operator
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.technician_id"
                            :items="technicians"
                            item-value="id"
                            item-title="name"
                            label="Select Technician"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.technician_id ? errors.technician_id : ''
                            "
                            @update:search="fetchTechnicians"
                        >
                            <template v-slot:label>
                                Select Technician
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.mechine_type_id"
                            :items="parses"
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
                            @update:search="fetchParses"
                        >
                            <template v-slot:label>
                                Select Parse
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
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

                <!-- Name Field -->

                <!-- <v-text-field
                    v-model="mechine_assing.purchase_date"
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
                    v-model="mechine_assing.rent_date"
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
                            <!-- <template v-slot:label>
                        Select Supplier
                        <span style="color: red">*</span>
                    </template> -->
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

                <!-- <v-text-field
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
                </v-text-field> -->

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
                mechine_id: null,
                operator_id: null,
                technician_id: null,
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
            mechines: [], // Array to store mechines data
            operators: [], // Array to store operators data
            technicians: [], // Array to store technicians data
            parses: [], // Array to store parses data
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
    methods: {
        async submit() {
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
        async fetchMechins(search) {
            try {
                const response = await this.$axios.get(`/get_mechines`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.mechines = response.data;
            } catch (error) {
                console.error("Error fetching mechines:", error);
            }
        },
        async fetchOperator(search) {
            try {
                const response = await this.$axios.get(`/get_operators`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.operators = response.data;
            } catch (error) {
                console.error("Error fetching operators:", error);
            }
        },
        async fetchTechnicians(search) {
            try {
                const response = await this.$axios.get(`/get_technicians`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.technicians = response.data;
            } catch (error) {
                console.error("Error fetching technicians:", error);
            }
        },
        async fetchParses(search) {
            try {
                const response = await this.$axios.get(`/get_parses`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.parses = response.data;
            } catch (error) {
                console.error("Error fetching parses:", error);
            }
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
