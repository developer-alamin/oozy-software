<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Model</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="model.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Model Number Field -->
                <v-text-field
                    v-model="model.model_number"
                    :rules="[rules.required]"
                    label="Model Number"
                    outlined
                    :error-messages="
                        errors.model_number ? errors.model_number : ''
                    "
                >
                    <template v-slot:label>
                        Model Number <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="model.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Status Field (Checkbox) -->
                <v-checkbox
                    v-model="model.status"
                    label="Status"
                    :error-messages="errors.status ? errors.status : ''"
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
export default {
    data() {
        return {
            valid: false,
            loading: false,
            model: {
                name: "",
                model_number: "",
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
        this.fetchModel();
    },
    methods: {
        async fetchModel() {
            // Fetch the model data to populate the form
            const modelId = this.$route.params.id; // Assuming the model ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/models/${modelId}/edit`
                );
                console.log(response.data);

                this.model = response.data.model; // Populate form with the existing model data
                this.model.status =
                    this.model.status == "true" || this.model.status == true;
            } catch (error) {
                this.serverError = "Error fetching model data.";
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const modelId = this.$route.params.id; // Assuming model ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/models/${modelId}`,
                        this.model
                    );

                    if (response.data.success) {
                        this.$router.push({ name: "ModelIndex" }); // Redirect to model list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating model.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchModel(); // Reset the form with existing model data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
