<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Create Floor</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Factory Selection -->
                <v-autocomplete
                    v-model="floor.factory_id"
                    :items="factories"
                    item-value="id"
                    :item-title="formatFactory"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.factory_id || ''"
                    @update:search="fetchFactories"
                >
                    <template v-slot:label>
                        Select Factory <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <!-- Name Field -->
                <v-text-field
                    v-model="floor.name"
                    :rules="[rules.required]"
                    label="Floor Name"
                    outlined
                    :error-messages="errors.name || ''"
                >
                    <template v-slot:label>
                        Floor <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="floor.description"
                    label="Description"
                    outlined
                    :error-messages="errors.description || ''"
                />

                <!-- Status Dropdown -->
                <v-select
                    v-model="floor.status"
                    :items="statusItems"
                    label="Floor Status"
                    clearable
                    outlined
                ></v-select>

                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <v-col cols="12" class="text-right">
                        <!-- Reset Button -->
                        <v-btn
                            type="button"
                            color="secondary"
                            @click="resetForm"
                            class="mr-3"
                        >
                            Reset Form
                        </v-btn>

                        <!-- Submit Button -->
                        <v-btn
                            type="submit"
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Create Floor
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
import { ref } from "vue";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            valid: false,
            loading: false,
            statusItems: ["Active", "Inactive"],
            floor: {
                factory_id: "",
                name: "",
                description: "",
                status: "Active",
            },
            factories: [], // List of factories
            errors: {}, // Validation errors
            serverError: null, // Server-side error
            rules: {
                required: (value) => !!value || "This field is required.",
            },
        };
    },
    methods: {
        // Format factory name with user name
        formatFactory(factory) {
            if (factory) {
                return `${factory.name} -- ${factory.user?.name || "No User"}`;
            }
        },
        async fetchFactories(search = "") {
            try {
                const response = await this.$axios.get(`/get_factories`, {
                    params: { search, limit: 10 },
                });
                this.factories = response.data; // Populate the factories list
            } catch (error) {
                console.error("Error fetching factories:", error);
            }
        },
        async submit() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;

            const formData = new FormData();
            Object.entries(this.floor).forEach(([key, value]) => {
                formData.append(key, value);
            });

            try {
                const response = await this.$axios.post("/floor", formData);
                if (response.data.success) {
                    toast.success("Floor created successfully!");
                    this.resetForm();
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                    toast.error("Validation failed. Please check the form.");
                } else {
                    this.serverError = "An error occurred. Please try again.";
                    toast.error(this.serverError);
                }
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.floor = {
                factory_id: "",
                name: "",
                description: "",
                status: "Active",
            };
            this.errors = {};
            if (this.$refs.form) {
                this.$refs.form.reset();
            }
        },
    },
};
</script>
