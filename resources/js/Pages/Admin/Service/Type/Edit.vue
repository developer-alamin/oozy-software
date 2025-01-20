<template>
    <v-card outlined class="mx-auto my-5" >
        <v-card-title>Edit Mechine Type</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="type.name"
                            :rules="[rules.required]"
                            label="Name"
                            outlined
                            :error-messages="errors.name ? errors.name : ''"
                        >
                            <template v-slot:label>
                                Name <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="type.day"
                            :rules="day"
                            label="Days"
                        ></v-text-field>
                    </v-col>
                </v-row>

                <!-- Description Field -->
                <v-textarea
                    v-model="type.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <v-select
                    v-model="type.status"
                    :items="statusItems"
                    label="Type Status"
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
                            Update type
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
            type: {
                name: "",
                day: "",
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
        this.fetchtype();
    },
    methods: {
        async fetchtype() {
            // Fetch the type data to populate the form
            const typesId = this.$route.params.uuid; // Assuming the type ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/mechine/type/${typesId}/edit`
                );
                this.type = response.data.mechinetype; // Populate form with the existing type data
                this.type.status =
                    this.type.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                this.serverError = "Error fetching type data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const typeId = this.$route.params.uuid; // Assuming type ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `mechine/type/${typeId}`,
                        this.type
                    );
                    console.log(response.data);
                    if (response.data.success) {
                        toast.success("type update successfully!");
                        this.$router.push({ name: "MechineTypeIndex" }); // Redirect to type list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update type.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating type. Please try again.");
                        this.serverError = "Error updating type.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchtype(); // Reset the form with existing type data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
