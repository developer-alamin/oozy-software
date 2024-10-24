<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Technician</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="technician.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <v-text-field
                    v-model="technician.email"
                    label="Email"
                    outlined
                    :error-messages="errors.email ? errors.email : ''"
                >
                    <template v-slot:label> Email </template>
                </v-text-field>

                <v-text-field
                    v-model="technician.phone"
                    label="Phone"
                    outlined
                    :error-messages="errors.phone ? errors.phone : ''"
                >
                    <template v-slot:label> Phone </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="technician.address"
                    label="Address"
                    :error-messages="errors.description ? errors.address : ''"
                />

                <!-- Description Field -->
                <v-textarea
                    v-model="technician.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Status Field (Checkbox) -->
                <!-- <v-checkbox
                    v-model="technician.status"
                    label="Status"
                    :error-messages="errors.status ? errors.status : ''"
                /> -->
                <v-select
                    v-model="technician.status"
                    :items="statusItems"
                    label="Technician Status"
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
                            Update Technician
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
            technician: {
                group_id: "",
                name: "",
                email: "",
                phone: "",
                photo: "",
                address: "",
                description: "",
                status: false, // Default to false (inactive)
            },
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchTechnician();
    },
    methods: {
        async fetchTechnician() {
            // Fetch the technician data to populate the form
            const technicianId = this.$route.params.id; // Assuming the technician ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/technician/${technicianId}/edit`
                );
                // console.log(response.data);

                this.technician = response.data.technician; // Populate form with the existing technician data
                this.technician.status =
                    this.technician.status === "Active"
                        ? "Active"
                        : "In-Active";
            } catch (error) {
                this.serverError = "Error fetching technician data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const technicianId = this.$route.params.id; // Assuming technician ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/technician/${technicianId}`,
                        this.technician
                    );

                    if (response.data.success) {
                        this.$router.push({ name: "TechnicianIndex" }); // Redirect to technician list page
                        toast.success("Technician Update successfully!");
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        toast.error("Failed to Update technician.");
                    } else {
                        toast.error("Failed to Update technician.");
                        this.serverError = "Error updating technician.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchTechnician(); // Reset the form with existing technician data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
