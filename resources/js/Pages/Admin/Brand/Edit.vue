<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Brand</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="brand.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-select
                    v-model="brand.type"
                    :rules="[rules.required]"
                    :items="statusTypeItems"
                    label="Brand Type"
                    density="comfortable"
                    clearable
                >
                    <template v-slot:label>
                        Brand Type <span style="color: red">*</span>
                    </template>
                </v-select>
                <v-select
                    v-model="brand.status"
                    :items="statusItems"
                    label="Brand Status"
                    clearable
                    :error-messages="errors.status ? errors.status : ''"
                ></v-select>

                <!-- Description Field -->
                <v-textarea
                    v-model="brand.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Status Field (Checkbox) -->
                <!-- <v-checkbox
                    v-model="brand.status"
                    label="Status"
                    :error-messages="errors.status ? errors.status : ''"
                /> -->

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
                            Update Brand
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
            statusTypeItems: ["Mechine", "Parse"],
            brand: {
                name: "",
                type: false,
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
        this.fetchBrand();
    },
    methods: {
        async fetchBrand() {
            // Fetch the brand data to populate the form
            const brandId = this.$route.params.uuid; // Assuming the brand ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/brand/${brandId}/edit`
                );
                console.log(response.data);

                this.brand = response.data.brand; // Populate form with the existing brand data
                this.brand.status =
                    this.brand.status === "Active" ? "Active" : "Inactive";
                this.brand.type =
                    this.brand.type === "Mechine" ? "Mechine" : "Parse";
            } catch (error) {
                this.serverError = "Error fetching brand data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const brandId = this.$route.params.uuid; // Assuming brand ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/brand/${brandId}`,
                        this.brand
                    );

                    if (response.data.success) {
                        toast.success("Brand update successfully!");
                        this.$router.push({ name: "BrandIndex" }); // Redirect to brand list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update brand.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating brand. Please try again.");
                        this.serverError = "Error updating brand.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchBrand(); // Reset the form with existing brand data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
