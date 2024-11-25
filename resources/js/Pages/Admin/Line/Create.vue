<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create Line</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="line.unit_id"
                    :items="units"
                    item-value="id"
                    :item-title="formatUnit"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.unit_id ? errors.unit_id : ''"
                    @update:search="fetchUnits"
                >
                    <template v-slot:label>
                        Select Unit <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <!-- Display factory name -->
                <!-- <div v-if="selectedFloorName" style="margin-top: 10px">
                    <strong>Floor Name:</strong> {{ selectedFloorName }}
                </div> -->
                <!-- Display factory name -->
                <!-- <div v-if="selectedFactoryName" style="margin-top: 10px">
                    <strong>Factory Name:</strong> {{ selectedFactoryName }}
                </div> -->

                <!-- Display user name -->
                <!-- <div v-if="selectedUserName" style="margin-top: 10px">
                    <strong>Company Name:</strong> {{ selectedUserName }}
                </div> -->
                <!-- Name Field -->
                <v-text-field
                    v-model="line.name"
                    :rules="[rules.required]"
                    label="Name Number"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Line <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea v-model="line.description" label="Description" />
                <v-select
                    v-model="line.status"
                    :items="statusItems"
                    label="Line Status"
                    @change="updateStatus"
                    clearable
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
                            Create Line
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</template>
<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "Inactive"],
            selectedFactoryName: null, // Displayed factory name
            selectedUserName: null, // Displayed user name
            selectedFloorName: null,
            line: {
                name: "",
                description: "",
                status: "Active",
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            units: [],
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
            Object.entries(this.line).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post("/line", formData);

                    if (response.data.success) {
                        this.resetForm();
                        toast.success("Line Created successfully!");
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
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

        async fetchUnits(search) {
            try {
                const response = await this.$axios.get(`/get_units`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.units = response.data;
            } catch (error) {
                console.error("Error fetching units:", error);
            }
        },
        // Format factory name with user name
        formatUnit(line) {
            if (line) {
                return `${line.name} -- ${line.floors?.name || "No Floor"} -- ${
                    line.floors?.factories?.name || "No Factory"
                } -- ${line.floors?.factories?.user?.name || "No Company"}`;
            }
        },
        // updateSelectedUnitDetails(unitId) {
        //     const selectedUnit = this.units.find((unit) => unit.id === unitId);
        //     if (selectedUnit) {
        //         this.selectedFloorName =
        //             selectedUnit.floors?.name || "No Floor Name";
        //         this.selectedFactoryName =
        //             selectedUnit.floors?.factories?.name || "No Factory Name";
        //         this.selectedUserName =
        //             selectedUnit.floors?.factories?.user?.name ||
        //             "No Company Name";
        //     } else {
        //         this.selectedFloorName = null;
        //         this.selectedFactoryName = null;
        //         this.selectedUserName = null;
        //     }
        // },

        resetForm() {
            this.line = {
                name: "",
                number: "",
                status: "Active",
                description: "",
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
    // watch: {
    //     // Watch for changes to unit.floor_id
    //     "line.unit_id": function (newUnitId) {
    //         this.updateSelectedUnitDetails(newUnitId);
    //     },
    // },
};
</script>
