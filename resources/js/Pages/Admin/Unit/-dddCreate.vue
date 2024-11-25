<template>
    <v-card outlined class="mx-auto my-5" max-width="600px">
        <v-card-title>Create Unit</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Floor Selection -->
                <v-autocomplete
                    v-model="unit.selected_floor"
                    :items="floors"
                    item-value="id"
                    :item-title="formatFloor"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.floor_id ? errors.floor_id : ''"
                    @update:search="fetchFloors"
                    @input="updateFloorId"
                >
                    <template v-slot:label>
                        Select Floor <span style="color: red">*</span>
                    </template>
                    <template v-slot:no-data> No floors found. </template>
                </v-autocomplete>

                <!-- Name Field -->
                <v-text-field
                    v-model="unit.name"
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
                    v-model="unit.description"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Status Field -->
                <v-select
                    v-model="unit.status"
                    :items="statusItems"
                    label="Unit Status"
                    clearable
                ></v-select>

                <!-- Action Buttons -->
                <v-row class="mt-4">
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
                            Create Unit
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
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            valid: false,
            loading: false,
            statusItems: ["Active", "Inactive"],
            floors: [], // Floor options for autocomplete
            unit: {
                floor_id: null, // The ID for backend use
                selected_floor: null, // The entire floor object for display
                name: "",
                description: "",
                status: "Active", // Default status
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "This field is required",
            },
        };
    },
    methods: {
        // Format floor for display in autocomplete
        formatFloor(floor) {
            if (floor) {
                const factoryName = floor.factories?.name || "No Factory Name";
                const userName = floor.factories?.user?.name || "No User";
                return `${
                    floor.name || "No Floor Name"
                } -- ${factoryName} -- ${userName}`;
            }
            return "No Floor Data";
        },

        // Fetch floors based on search term
        async fetchFloors(search) {
            try {
                const response = await this.$axios.get(
                    `/get_floors?search=${search}`
                );
                this.floors = response.data;
            } catch (error) {
                console.error("Error fetching floors:", error);
            }
        },

        // Update floor_id based on selected floor
        updateFloorId(selectedFloor) {
            this.unit.floor_id = selectedFloor ? selectedFloor.id : null;
        },

        // Reset form fields
        resetForm() {
            this.unit = {
                floor_id: null,
                selected_floor: null,
                name: "",
                description: "",
                status: "Active",
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref
            }
        },

        // Submit the form
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true;

            const formData = new FormData();
            Object.entries(this.unit).forEach(([key, value]) => {
                formData.append(key, value);
            });

            try {
                const response = await this.$axios.post("/api/units", formData);
                console.log(response.data);

                if (response.data.success) {
                    toast.success("Unit created successfully!");
                    this.resetForm();
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    toast.error("Failed to create unit.");
                    this.errors = error.response.data.errors || {};
                } else {
                    toast.error("An error occurred. Please try again.");
                    this.serverError = "An error occurred. Please try again.";
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
/* Add custom styles if necessary */
</style>
