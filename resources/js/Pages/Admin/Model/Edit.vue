<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Model</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="model.brand_id"
                    :items="brands"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    chips
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.brand_id ? errors.brand_id : ''"
                    @update:search="fetchBrands"
                >
                    <template v-slot:label>
                        Select Brand <span style="color: red">*</span>
                    </template>
                </v-autocomplete>
                <!-- Name Field -->
                <v-text-field
                    v-model="model.name"
                    :rules="[rules.required]"
                    density="comfortable"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-select
                    v-model="model.type"
                    :rules="[rules.required]"
                    :items="statusTypeItems"
                    label="Model Type"
                    density="comfortable"
                    clearable
                >
                    <template v-slot:label>
                        Model Type <span style="color: red">*</span>
                    </template>
                </v-select>

                <!-- Status Field (Checkbox) -->
                <v-select
                    v-model="model.status"
                    density="comfortable"
                    :items="statusItems"
                    label="Model Status"
                    clearable
                ></v-select>

                <!-- Description Field -->
                <v-textarea
                    v-model="model.description"
                    density="comfortable"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

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
                            Update Model
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
            model: {
                brand_id: "",
                type: "",
                name: "",
                model_number: "",
                description: "",
                status: "", // Default to false (inactive)
            },
            brands: [],
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchBrands().then(() => {
            this.fetchModel();
        });
        this.fetchModel();
    },
    methods: {
        async fetchModel() {
            // Fetch the model data to populate the form
            const modelId = this.$route.params.uuid; // Assuming the model ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/models/${modelId}/edit`
                );
                // console.log(response.data);

                this.model = response.data.model; // Populate form with the existing model data
                const selectedBrand = this.brands.find(
                    (c) => c.id === this.model.brand_id
                );
                if (selectedBrand) {
                    this.model.brand_id = selectedBrand.id; // Set the company_id for v-autocomplete
                }

                this.model.status =
                    this.model.status === "Active" ? "Active" : "Inactive";
                this.model.type =
                    this.model.type === "Mechine" ? "Mechine" : "Parse";
            } catch (error) {
                this.serverError = "Error fetching model data.";
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const modelId = this.$route.params.uuid; // Assuming model ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/models/${modelId}`,
                        this.model
                    );

                    if (response.data.success) {
                        toast.success("Model update successfully!");
                        this.$router.push({ name: "ModelIndex" }); // Redirect to model list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update model.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating model.");
                        this.serverError = "Error updating model.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        async fetchBrands(search) {
            try {
                const response = await this.$axios.get(`/get_brand_alls`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.brands = response.data;
            } catch (error) {
                console.error("Error fetching brands:", error);
            }
        },
        resetForm() {
            this.fetchModel(); // Reset the form with existing model data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
