<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Machine Movement</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <div>
                    <!-- Machine Selector -->
                    <v-autocomplete
                        :model-value="selectedMachine"
                        :items="machines"
                        item-value="id"
                        item-title="name"
                        label="Select Machine"
                        outlined
                        clearable
                        density="comfortable"
                        @update:model-value="onMachineChange"
                    >
                        <template v-slot:label>
                            Select Machine<span style="color: red">*</span>
                        </template>
                    </v-autocomplete>

                    <!-- Line Selector -->
                    <v-autocomplete
                        :model-value="selectedLine"
                        :items="lines"
                        item-value="id"
                        item-title="name"
                        label="Select Line"
                        outlined
                        clearable
                        density="comfortable"
                        @update:model-value="selectedLine = $event"
                    >
                        <template v-slot:label>
                            Select Line<span style="color: red">*</span>
                        </template>
                    </v-autocomplete>
                </div>
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
                            Machine Movement
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
            selectedMachine: null,
            selectedLine: null,
            machines: [],
            lines: [],
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

    mounted() {
        this.fetchMachines();
    },
    methods: {
        async fetchMachines() {
            try {
                const response = await this.$axios.get("/get_mechines");
                this.machines = response.data;
            } catch (error) {
                console.error("Error fetching machines:", error);
            }
        },

        // Fetch lines based on the selected machine

        async onMachineChange(machineId) {
            this.selectedMachine = machineId; // Update the selected machine
            this.selectedLine = null;
            this.fetchLines(machineId); // Fetch lines for the selected machine
        },

        // Fetch lines based on the selected machine
        async fetchLines(machineId) {
            if (!machineId) {
                this.lines = [];
                return;
            }
            try {
                const response = await this.$axios.get(
                    "/get_lines_by_machine",
                    {
                        params: { machine_id: machineId },
                    }
                );
                console.log(response.data);

                this.lines = response.data;
            } catch (error) {
                console.error("Error fetching lines:", error);
            }
        },

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
    },
};
</script>
