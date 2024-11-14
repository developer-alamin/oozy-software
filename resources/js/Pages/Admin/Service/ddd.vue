<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create Service</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
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
                            v-model="mechine_assing.service_date"
                            label="Service Date"
                            density="comfortable"
                            :error-messages="
                                errors.service_date ? errors.service_date : ''
                            "
                        />
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                            v-model="mechine_assing.service_time"
                            label="Service Time"
                            type="time"
                            density="comfortable"
                            :error-messages="
                                errors.service_time ? errors.service_time : ''
                            "
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-select
                            v-model="mechine_assing.service_type_status"
                            :items="statusTypeItems"
                            label="Service Type"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-select
                            v-model="mechine_assing.status"
                            :items="statusItems"
                            label="Mechine Status"
                            clearable
                            density="comfortable"
                        ></v-select>
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
                        <v-text-field
                            v-model="mechine_assing.operator_cell_time"
                            label="Operator Cell Time"
                            type="time"
                            density="comfortable"
                            :error-messages="
                                errors.operator_cell_time
                                    ? errors.operator_cell_time
                                    : ''
                            "
                        />
                    </v-col>
                </v-row>
                <v-textarea
                    v-model="mechine_assing.operator_mechine_problem_note"
                    label=" Operator Mechine Problems Note"
                    density="comfortable"
                    :error-messages="
                        errors.operator_mechine_problem_note
                            ? errors.operator_mechine_problem_note
                            : ''
                    "
                />
                <v-row>
                    <v-col cols="3">
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
                    <v-col cols="3">
                        <v-text-field
                            v-model="mechine_assing.technician_arrive_time"
                            label="Technician Arrive Time"
                            type="time"
                            density="comfortable"
                            :error-messages="
                                errors.technician_arrive_time
                                    ? errors.technician_arrive_time
                                    : ''
                            "
                        />
                    </v-col>
                    <v-col cols="3">
                        <v-text-field
                            v-model="mechine_assing.technician_working_time"
                            label="Technician Working Time"
                            type="time"
                            density="comfortable"
                            :error-messages="
                                errors.technician_working_time
                                    ? errors.technician_working_time
                                    : ''
                            "
                        />
                    </v-col>
                    <v-col cols="3">
                        <v-select
                            v-model="mechine_assing.technician_status"
                            :items="statusTechnicianItems"
                            label="Technician Status"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col>
                </v-row>
                <v-textarea
                    v-model="mechine_assing.technician_note"
                    label="Technician Note"
                    density="comfortable"
                    :error-messages="
                        errors.technician_note ? errors.technician_note : ''
                    "
                />
                <!--
                <v-row>
                    <v-col cols="6">
                        <v-autocomplete
                            v-model="mechine_assing.parse_id"
                            :items="parses"
                            item-value="id"
                            item-title="name"
                            label="Select Mechine Type"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.parse_id ? errors.parse_id : ''
                            "
                            @update:search="fetchParses"
                        >
                            <template v-slot:label>
                                Select Parse
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col>
                        <v-text-field
                            v-model="mechine_assing.quantity"
                            :rules="[rules.required]"
                            label="Quantity"
                            density="comfortable"
                            outlined
                            :error-messages="
                                errors.quantity ? errors.quantity : ''
                            "
                        >
                            <template v-slot:label>
                                Quantity <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                </v-row> -->

                <!-- Parses Section for Dynamic Add/Remove -->
                <div
                    v-for="(parse, index) in mechine_assing.parses"
                    :key="index"
                >
                    <v-row class="mt-3">
                        <v-col cols="8">
                            <v-autocomplete
                                v-model="parse.parse_id"
                                item-value="id"
                                :items="parseOptions"
                                item-title="name"
                                label="Select Parse"
                                outlined
                                clearable
                                density="comfortable"
                                :rules="[rules.required]"
                                :error-messages="
                                    errors[`parses.${index}.parse_id`] || ''
                                "
                                @update:search="fetchParses"
                            >
                                <template v-slot:label>
                                    Select Parse
                                    <span style="color: red">*</span>
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field
                                v-model="parse.quantity"
                                :rules="[rules.required]"
                                label="Quantity"
                                density="comfortable"
                                outlined
                                :error-messages="
                                    errors[`parses.${index}.quantity`] || ''
                                "
                            ></v-text-field>
                        </v-col>
                        <v-col class="text-right">
                            <v-btn color="primary" @click="addParse"
                                >Add Parse</v-btn
                            >
                        </v-col>
                        <v-col cols="12" class="d-flex justify-end">
                            <v-btn
                                color="error"
                                text
                                @click="removeParse(index)"
                                v-if="mechine_assing.parses.length > 1"
                            >
                                Remove
                            </v-btn>
                        </v-col>
                    </v-row>
                </div>

                <v-textarea
                    v-model="mechine_assing.description"
                    label="Description"
                    density="comfortable"
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
                            Create Service
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
    props: ["id"],
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Pending", "Approved", "Cancle"],
            statusTypeItems: ["Preventive", "Breakdown"],
            statusTechnicianItems: ["Pending", "Running", "Success", "Failed"],

            mechine_assing: {
                service_id: this.id,
                service_time: "",
                operator_cell_time: "",
                technician_working_time: "",
                technician_arrive_time: "",
                service_date: null,
                name: "",
                date: null,
                company_id: null,
                mechine_id: null,
                operator_id: null,
                technician_id: null,
                operator_mechine_problem_note: "",
                technician_note: "",
                description: "",
                status: "Pending", // New property for checkbox
                service_type_status: "Preventive",
                technician_status: "Pending",
                parses: [{ parse_id: null, quantity: 1 }],
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companys: [], // Array to store Company data
            mechines: [], // Array to store mechines data
            parseOptions: [],
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

    methods: {
        // async submit() {
        //     // Reset errors and loading state before submission
        //     this.errors = {};
        //     this.serverError = null;
        //     this.loading = true; // Start loading when submit is clicked

        //     const formData = new FormData();
        //     Object.entries(this.mechine_assing).forEach(([key, value]) => {
        //         formData.append(key, value);
        //     });
        //     // const formData = new FormData();
        //     // Object.entries(this.factory).forEach(([key, value]) => {
        //     //     if (Array.isArray(value)) {
        //     //         value.forEach((val) => formData.append(`${key}[]`, val));
        //     //     } else {
        //     //         formData.append(key, value);
        //     //     }
        //     // });
        //     // Simulate a 3-second loading time (e.g., for an API call)
        //     setTimeout(async () => {
        //         try {
        //             // Assuming the actual API call here
        //             const response = await this.$axios.post(
        //                 "/mechine-assing",
        //                 formData
        //             );
        //             console.log(response.data);

        //             if (response.data.success) {
        //                 toast.success("mechine assing create successfully!");
        //                 // localStorage.setItem("token", response.data.token);
        //                 this.resetForm();
        //             }
        //         } catch (error) {
        //             if (error.response && error.response.status === 422) {
        //                 toast.error("Failed to create mechine assing.");
        //                 // Handle validation errors from the server
        //                 this.errors = error.response.data.errors || {};
        //             } else {
        //                 toast.error("Failed to create mechine assing.");
        //                 // Handle other server errors
        //                 this.serverError =
        //                     "An error occurred. Please try again.";
        //             }
        //         } finally {
        //             // Stop loading after the request (or simulated time) is done
        //             this.loading = false;
        //         }
        //     }, 1000); // Simulates a 3-second loading duration
        // },

        // async submit() {
        //     this.errors = {};
        //     this.serverError = null;
        //     this.loading = true;
        //     const formData = new FormData();
        //     Object.entries(this.mechine_assing).forEach(([key, value]) => {
        //         if (key === "parses") {
        //             value.forEach((parse, index) => {
        //                 formData.append(
        //                     `parses[${index}][parse_id]`,
        //                     parse.parse_id
        //                 );
        //                 formData.append(
        //                     `parses[${index}][quantity]`,
        //                     parse.quantity
        //                 );
        //             });
        //         } else {
        //             formData.append(key, value);
        //         }
        //     });
        //     setTimeout(async () => {
        //         try {
        //             const response = await this.$axios.post(
        //                 "/mechine-assing",
        //                 formData
        //             );
        //             if (response.data.success) {
        //                 toast.success("Mechine assigned successfully!");
        //                 this.resetForm();
        //             }
        //         } catch (error) {
        //             if (error.response && error.response.status === 422) {
        //                 toast.error("Failed to create mechine assignment.");
        //                 this.errors = error.response.data.errors || {};
        //             } else {
        //                 this.serverError =
        //                     "An error occurred. Please try again.";
        //             }
        //         } finally {
        //             this.loading = false;
        //         }
        //     }, 1000);
        // },

        async submit() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;
            const formData = new FormData();
            Object.entries(this.mechine_assing).forEach(([key, value]) => {
                if (key === "parses") {
                    value.forEach((parse, index) => {
                        formData.append(
                            `parses[${index}][parse_id]`,
                            parse.parse_id
                        );
                        formData.append(
                            `parses[${index}][quantity]`,
                            parse.quantity
                        );
                    });
                } else {
                    formData.append(key, value);
                }
            });
            setTimeout(async () => {
                try {
                    const response = await this.$axios.post(
                        "/mechine-assing",
                        formData
                    );
                    if (response.data.success) {
                        toast.success("Mechine assigned successfully!");
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create mechine assignment.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError =
                            "An error occurred. Please try again.";
                    }
                } finally {
                    this.loading = false;
                }
            }, 1000);
        },

        addParse() {
            this.mechine_assing.parses.push({ parse_id: null, quantity: 1 });
        },
        removeParse(index) {
            this.mechine_assing.parses.splice(index, 1);
        },
        resetForm() {
            this.mechine_assing = {
                company_id: "",
                name: "",
                email: "",
                phone: "",
                parses: [{ parse_id: null, quantity: 1 }],
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
                this.parseOptions = response.data;
            } catch (error) {
                console.error("Error fetching parses:", error);
            }
        },

        async fetchGroups(search) {
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
    },
    created() {
        // Fetch data or perform actions using `this.id`
        console.log("Service history ID:", this.mechine_assing.service_id);
        console.log("Service history ID:", this.id);
    },
};
</script>
