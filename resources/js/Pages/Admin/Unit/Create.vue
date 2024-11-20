<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create unit</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="unit.floor_id"
                    :items="floors"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.floor_id ? errors.floor_id : ''"
                    @update:search="fetchFloors"
                >
                    <template v-slot:label>
                        Select Floor <span style="color: red">*</span>
                    </template>
                </v-autocomplete>
                <!-- Display factory name -->
                <div v-if="selectedFactoryName" style="margin-top: 10px">
                    <strong>Factory Name:</strong> {{ selectedFactoryName }}
                </div>

                <!-- Display user name -->
                <div v-if="selectedUserName" style="margin-top: 10px">
                    <strong>Company Name:</strong> {{ selectedUserName }}
                </div>

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

                <!-- Featured Checkbox -->
                <v-select
                    v-model="unit.status"
                    :items="statusItems"
                    label="Unit Status"
                    clearable
                ></v-select>

                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <!-- Submit Button -->
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
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "Inactive"],
            selectedFactoryName: null, // Displayed factory name
            selectedUserName: null, // Displayed user name
            floors: [],
            unit: {
                floor_id: null,
                name: "",
                description: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    methods: {
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.unit).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post("/units", formData);

                    if (response.data.success) {
                        toast.success("Unit create successfully!");
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create unit.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("An error occurred. Please try again.");
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
        async fetchFloors(search) {
            try {
                const response = await this.$axios.get(`/get_floors`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                console.log(response.data);
                this.floors = response.data;
            } catch (error) {
                console.error("Error fetching floors:", error);
            }
        },
        updateSelectedFloorDetails(floorId) {
            const selectedFloor = this.floors.find(
                (floor) => floor.id === floorId
            );
            if (selectedFloor) {
                this.selectedFactoryName =
                    selectedFloor.factories?.name || "No Factory Name";
                this.selectedUserName =
                    selectedFloor.factories?.user?.name || "No Company Name";
            } else {
                this.selectedFactoryName = null;
                this.selectedUserName = null;
            }
        },
        resetForm() {
            this.unit = {
                name: "",
                description: "",
                status: false, // Reset checkbox on form reset
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
    watch: {
        // Watch for changes to unit.floor_id
        "unit.floor_id": function (newFloorId) {
            console.log(newFloorId);
            this.updateSelectedFloorDetails(newFloorId);
        },
    },
};
</script>
