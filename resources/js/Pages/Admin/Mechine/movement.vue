<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Machine Movement</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <div>
                    <!-- Machine Selector -->
                    <v-autocomplete
                        :model-value="machine_movement.machine_id"
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
                        :model-value="machine_movement.line_id"
                        :items="lines"
                        item-value="id"
                        item-title="name"
                        label="Select Line"
                        outlined
                        clearable
                        density="comfortable"
                        @update:model-value="machine_movement.line_id = $event"
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
            machine_movement: {
                machine_id: null,
                line_id: null,
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            machine_statuses: [],
            currentDate: new Date(),

            rules: {
                required: (value) => !!value || "Required.",
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
            this.machine_movement.machine_id = machineId; // Update the selected machine
            this.machine_movement.line_id = null;
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
                // console.log(response.data);
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
            Object.entries(this.machine_movement).forEach(([key, value]) => {
                formData.append(key, value);
            });

            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post(
                        "/machine-movement",
                        formData
                    );
                    // console.log(response.data);

                    if (response.data.success) {
                        toast.success("machine movement create successfully!");
                        // localStorage.setItem("token", response.data.token);
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create machine movement.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to machine movement.");
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
                machine_id: null,
                line_id: null,
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
};
</script>
