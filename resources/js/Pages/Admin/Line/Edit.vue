<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Line</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <v-autocomplete
                    v-model="line.unit_id"
                    :items="units"
                    item-value="id"
                    item-title="name"
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
                <div v-if="selectedFloorName" style="margin-top: 10px">
                    <strong>Floor Name:</strong> {{ selectedFloorName }}
                </div>
                <!-- Display factory name -->
                <div v-if="selectedFactoryName" style="margin-top: 10px">
                    <strong>Factory Name:</strong> {{ selectedFactoryName }}
                </div>

                <!-- Display user name -->
                <div v-if="selectedUserName" style="margin-top: 10px">
                    <strong>Company Name:</strong> {{ selectedUserName }}
                </div>

                <!-- Name Field -->
                <v-text-field v-model="line.name" label="Line Number">
                    <template v-slot:label>
                        Line Number <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <!-- Description Field -->
                <v-textarea v-model="line.description" label="Description" />
                <!-- Action Buttons -->
                <v-select
                    v-model="line.status"
                    :items="statusItems"
                    label="Line Status"
                    clearable
                    :error-messages="errors.status ? errors.status : ''"
                ></v-select>
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
                            Update Line
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
            selectedFloorName: null,
            line: {
                unit_id: null,
                name: "",
                description: "",
                status: false,
            },
            units: [],
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchUnits().then(() => {
            this.fetchLine();
        });
        this.fetchLine();
    },
    methods: {
        async fetchLine() {
            // Fetch the brand data to populate the form
            const lineId = this.$route.params.uuid; // Assuming the brand ID is passed in the route params
            try {
                const response = await this.$axios.get(`/line/${lineId}/edit`);
                console.log(response.data == true);

                if (response.data.success) {
                    this.line = response.data.line;

                    const selectedFactory = this.units.find(
                        (c) => c.id === this.line.unit_id
                    );
                    if (selectedFactory) {
                        this.line.unit_id = selectedFactory.id;
                    }
                    // console.log(response.data);
                    // if (response.data.success) {
                    //     this.factory = response.data.factory;
                    // }
                    this.line.status =
                        this.line.status === "Active" ? "Active" : "Inactive";
                }

                // Populate form with the existing line data
            } catch (error) {
                this.serverError = "Error fetching Line data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const lineId = this.$route.params.uuid; // Assuming brand ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/line/${lineId}`,
                        this.line
                    );

                    if (response.data.success) {
                        this.$router.push({ name: "LineIndex" }); // Redirect to brand list page
                        toast.success("Line Updated successfully!");
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating Line.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchLine(); // Reset the form with existing brand data
            this.errors = {};
            this.$refs.form.resetValidation();
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
        updateSelectedUnitDetails(unitId) {
            const selectedUnit = this.units.find((unit) => unit.id === unitId);
            if (selectedUnit) {
                this.selectedFloorName =
                    selectedUnit.floors?.name || "No Floor Name";
                this.selectedFactoryName =
                    selectedUnit.floors?.factories?.name || "No Factory Name";
                this.selectedUserName =
                    selectedUnit.floors?.factories?.user?.name ||
                    "No Company Name";
            } else {
                this.selectedFloorName = null;
                this.selectedFactoryName = null;
                this.selectedUserName = null;
            }
        },
    },
    watch: {
        // Watch for changes to unit.floor_id
        "line.unit_id": function (newUnitId) {
            this.updateSelectedUnitDetails(newUnitId);
        },
    },
};
</script>
