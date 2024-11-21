<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Machine Status</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="machine_status.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="machine_status.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
                <!-- Status Field (Checkbox) -->
                <v-select
                    v-model="machine_status.status"
                    :items="statusItems"
                    label="Status"
                    clearable
                ></v-select>

                <!-- Action Buttons -->

                <v-row class="mt-4">
                    <v-col cols="12" class="text-right">
                        <v-btn
                            type="button"
                            color="secondary"
                            class="mr-3"
                            @click="resetForm"
                        >
                            Reset Form
                        </v-btn>
                        <v-btn
                            type="submit"
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Machine Status Update
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <!-- Server Error Message -->
        <v-alert v-if="serverError" type="error" class="my-4">
            {{ serverError }}
        </v-alert>
    </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false,
            statusItems: ["Active", "Inactive"],
            machine_status: {
                name: "",
                description: "",
                status: "", // Default to false (inactive)
            },
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchMachineStatus();
    },
    methods: {
        async fetchMachineStatus() {
            // Fetch the unit data to populate the form
            const machineStatusId = this.$route.params.uuid; // Assuming the unit ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/parse-unit/${machineStatusId}/edit`
                );
                this.machine_status = response.data.unit; // Populate form with the existing unit data
                // console.log(this.unit);

                this.machine_status.status =
                    this.machine_status.status === "Active"
                        ? "Active"
                        : "Inactive";
            } catch (error) {
                console.log(error);
                this.serverError =
                    "Error fetching machine status data." + error;
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const machineStatusId = this.$route.params.uuid; // Assuming unit ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/parse-unit/${machineStatusId}`,
                        this.machine_status
                    );

                    if (response.data.success) {
                        toast.success("Machine Status updated successfully!");
                        this.$router.push({ name: "MachineStatusIndex" }); // Redirect to unit list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update machine status.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating  machine status.");
                        this.serverError = "Error updating machine status.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchMachineStatus(); // Reset the form with existing unit data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
