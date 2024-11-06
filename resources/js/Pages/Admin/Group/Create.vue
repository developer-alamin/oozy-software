<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create Group</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Technician Autocomplete -->
                <v-autocomplete
                    v-model="group.technician_id"
                    :items="technicians"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="
                        errors.technician_id ? errors.technician_id : ''
                    "
                    @update:search="fetchTechnicians"
                >
                    <template v-slot:label>
                        Select Technician <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <!-- Name Field -->
                <v-text-field
                    v-model="group.name"
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
                    v-model="group.description"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
                <v-select
                    v-model="group.status"
                    :items="statusItems"
                    label="Group Status"
                    @change="updateStatus"
                    clearable
                ></v-select>

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
            statusItems: ["Active", "Inactive"],
            group: {
                technician_id: null,
                name: "",
                description: "",
                status: "Active",
            },
            technicians: [], // Stores original technician data from the API
            errors: {},
            search: "",
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
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
        async fetchTechnicians(search) {
            try {
                const response = await this.$axios.get(`/get-technician`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                console.log(response.data);
                this.technicians = response.data;
            } catch (error) {
                console.error("Error fetching technicians:", error);
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
            setTimeout(async () => {
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
                        this.serverError =
                            "An error occurred. Please try again.";
                    }
                } finally {
                    this.loading = false;
                }
            }, 1000); // Delay time in milliseconds
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
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
