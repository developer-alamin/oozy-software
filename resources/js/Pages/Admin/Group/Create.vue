<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create Group</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="group.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Number <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Technician Autocomplete -->
                <v-autocomplete
                    :items="technicians"
                    v-model="group.technician_id"
                    item-value="id"
                    item-title="name"
                    label="Technician"
                    :search-input.sync="search"
                    @update:search-input="fetchTechnicians"
                    clearable
                    chips
                ></v-autocomplete>

                <!-- Description Field -->
                <v-textarea
                    v-model="group.description"
                    label="Description"
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
                            Create Group
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <v-alert v-if="serverError" type="error" class="my-4">
            {{ serverError }}
        </v-alert>
    </v-card>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            valid: false,
            loading: false,
            loadingTechnicians: false,
            group: {
                name: "",
                description: "",
                technician_id: null,
            },
            technicians: [], // Stores original technician data from the API
            errors: {},
            search: "",
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
        console.log();
    },
    computed: {
        filteredTechnicians() {
            // Ensure the technicians array is well-formed before filtering
            if (!Array.isArray(this.technicians)) return [];

            return this.technicians
                .filter((item) => item && item.name) // Filter out invalid items and those without a name
                .map((item) => ({
                    id: item.id,
                    name: item.name,
                }));
        },
    },
    methods: {
        async fetchTechnicians() {
            console.log(this.search)
            try {
                const response = await this.$axios.get("/get-technician", {
                    params: { search: this.search, limit: 10 },
                });
                console.log(response.data)

                this.technicians = response.data; // Set fetched technicians
            } catch (error) {
                console.error("Error fetching technicians:", error);
                this.serverError = "Failed to fetch technicians.";
            }
        },
        async submit() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;

            const formData = new FormData();
            Object.entries(this.group).forEach(([key, value]) => {
                formData.append(key, value);
            });

            try {
                const response = await this.$axios.post("/group", formData);
                if (response.data.success) {
                    this.resetForm();
                    toast.success("Group Created successfully!");
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    console.log(error);

                    this.serverError = "An error occurred. Please try again.";
                }
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.group.name = "";
            this.group.description = "";
            this.group.technician_id = null;
            this.errors = {};
            if (this.$refs.form) {
                this.$refs.form.reset();
            }
        },
    },
    mounted() {
        // Fetch initial list of technicians when the component mounts
        this.fetchTechnicians();
    },
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
