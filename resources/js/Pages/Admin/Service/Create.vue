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
                    <!-- <v-col cols="6">
                        <v-select
                            v-model="mechine_assing.status"
                            :items="statusItems"
                            label="Mechine Status"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col> -->
                </v-row>

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
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Pending", "Approved", "Cancle"],
            statusTypeItems: ["Preventive", "Breakdown"],
            statusTechnicianItems: ["Pending", "Running", "Success", "Failed"],

            mechine_assing: {
                service_time: this.getCurrentTime(),
                service_date: this.getCurrentDate(),
                company_id: null,
                mechine_id: null,
                description: "",
                status: "Pending", // New property for checkbox
                service_type_status: "Preventive",
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companys: [], // Array to store Company data
            mechines: [], // Array to store mechines data
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
                        "/services",
                        formData
                    );

                    if (response.data.success) {
                        toast.success("mechine assing create successfully!");
                        console.log(response.data.service.id);
                        this.$router.push({
                            name: "ServiceHistoryCreate",
                            params: { id: response.data.service.id }, // Pass the ID here
                        });

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

        getCurrentDate() {
            const currentDate = new Date();
            return currentDate.toISOString().split("T")[0]; // Format YYYY-MM-DD
        },
        getCurrentTime() {
            const currentTime = new Date();
            return currentTime.toTimeString().split(" ")[0].slice(0, 5); // Format HH:MM
        },
    },
    mounted() {
        this.mechine_assing.service_date = this.getCurrentDate();
        this.mechine_assing.service_time = this.getCurrentTime();
    },
};
</script>
