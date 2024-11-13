<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Floor</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="floor.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Number <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="floor.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
                <v-select
                    v-model="floor.status"
                    :items="statusItems"
                    label="floor Status"
                    clearable
                    :error-messages="errors.status ? errors.status : ''"
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
                            Update Floor
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
            floor: {
                name: "",
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
        this.fetchFloor();
    },
    methods: {
        async fetchFloor() {
            // Fetch the floor data to populate the form
            const floorId = this.$route.params.uuid; // Assuming the floor ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/floor/${floorId}/edit`
                );
                console.log(response.data);

                this.floor = response.data.floor; // Populate form with the existing floor data
                this.floor.status =
                    this.floor.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                this.serverError = "Error fetching floor data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const floorId = this.$route.params.uuid; // Assuming floor ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/floor/${floorId}`,
                        this.floor
                    );

                    if (response.data.success) {
                        toast.success("floor update successfully!");
                        this.$router.push({ name: "FloorIndex" }); // Redirect to floor list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update floor.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating floor. Please try again.");
                        this.serverError = "Error updating floor.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchfloor(); // Reset the form with existing floor data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
