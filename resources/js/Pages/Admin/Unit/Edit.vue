<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Unit</v-card-title>
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
                    <strong>User Name:</strong> {{ selectedUserName }}
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
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
                <!-- Status Field (Checkbox) -->
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
                            Update Unit
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
            selectedFactoryName: null, // Displayed factory name
            selectedUserName: null, // Displayed user name
            floors: [],
            unit: {
                floor_id: null,
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
        this.fetchFloors().then(() => {
            this.fetchUnit();
        });
        this.fetchUnit();
    },
    methods: {
        async fetchUnit() {
            // Fetch the unit data to populate the form
            const unitId = this.$route.params.uuid; // Assuming the unit ID is passed in the route params
            try {
                const response = await this.$axios.get(`/units/${unitId}/edit`);
                this.unit = response.data.unit; // Populate form with the existing unit data
                // console.log(this.unit);

                // console.log(response.data);
                const selectedUnit = this.floors.find(
                    (c) => c.id === this.unit.floor_id
                );
                if (selectedUnit) {
                    this.unit.floor_id = selectedUnit.id;
                }

                this.unit.status =
                    this.unit.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                console.log(error);
                this.serverError = "Error fetching unit data." + error;
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const unitId = this.$route.params.uuid; // Assuming unit ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/units/${unitId}`,
                        this.unit
                    );

                    if (response.data.success) {
                        toast.success("Unit updated successfully!");
                        this.$router.push({ name: "UnitIndex" }); // Redirect to unit list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update unit.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating unit.");
                        this.serverError = "Error updating unit.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        async fetchFloors(search) {
            try {
                const response = await this.$axios.get(`/get_floors`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
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
                    selectedFloor.factories?.user?.name || "No User Name";
            } else {
                this.selectedFactoryName = null;
                this.selectedUserName = null;
            }
        },
        resetForm() {
            this.fetchUnit(); // Reset the form with existing unit data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
    watch: {
        // Watch for changes to unit.floor_id
        "unit.floor_id": function (newFloorId) {
            this.updateSelectedFloorDetails(newFloorId);
        },
    },
};
</script>
