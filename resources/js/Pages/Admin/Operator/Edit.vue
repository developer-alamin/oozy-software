<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Operator</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Company Selection -->
                <v-autocomplete
                    v-model="operator.company_id"
                    :items="companys"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.company_id ? errors.company_id : ''"
                    @update:search="fetchCompanys"
                    label="Select Company *"
                >
                    <template v-slot:label>
                        Select Company <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <!-- Name Field -->
                <v-text-field
                    v-model="operator.name"
                    :rules="[rules.required]"
                    label="Name *"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>
                <v-select
                    v-model="operator.type"
                    :items="typeItems"
                    :rules="[rules.required]"
                    label="Operator Type"
                    clearable
                >
                    <template v-slot:label>
                        Operator Type <span style="color: red">*</span>
                    </template>
                </v-select>

                <!-- Email Field -->
                <v-text-field
                    v-model="operator.email"
                    :rules="[rules.email]"
                    label="Email"
                    outlined
                    :error-messages="errors.email ? errors.email : ''"
                ></v-text-field>

                <!-- Phone Field -->
                <v-text-field
                    v-model="operator.phone"
                    label="Phone"
                    outlined
                    :error-messages="errors.phone ? errors.phone : ''"
                ></v-text-field>

                <!-- Address Field -->
                <v-textarea
                    v-model="operator.address"
                    label="Address"
                    outlined
                    :error-messages="errors.address ? errors.address : ''"
                ></v-textarea>

                <!-- Description Field -->
                <v-textarea
                    v-model="operator.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                ></v-textarea>

                <!-- Status Field -->
                <v-select
                    v-model="operator.status"
                    :items="statusItems"
                    label="Operator Status"
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
                            Update Operator
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
            typeItems: ["General", "Special", "Manager"],
            operator: {
                company_id: null,
                name: "",
                email: "",
                type: "General",
                phone: "",
                address: "",
                description: "",
                status: "Inactive", // Default status
            },
            companys: [],
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
            },
        };
    },
    created() {
        this.fetchCompanys().then(() => {
            this.fetchOperator();
        });
    },
    methods: {
        async fetchOperator() {
            const operatorId = this.$route.params.uuid;
            try {
                const response = await this.$axios.get(
                    `/operator/${operatorId}/edit`
                );
                this.operator = response.data.operator;
                this.operator.status =
                    this.operator.status === "Active" ? "Active" : "Inactive";

                // Set the selected company based on the company_id
                const selectedCompany = this.companys.find(
                    (c) => c.id === this.operator.company_id
                );
                if (selectedCompany) {
                    this.operator.company_id = selectedCompany.id; // Set the company_id for v-autocomplete
                }
            } catch (error) {
                this.serverError = "Error fetching operator data.";
            }
        },
        async fetchCompanys(search = "") {
            try {
                const response = await this.$axios.get(`/get_companys`, {
                    params: { search, limit: 10 },
                });
                this.companys = response.data;
            } catch (error) {
                console.error("Error fetching companies:", error);
            }
        },
        async update() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;
            const operatorId = this.$route.params.uuid;
            try {
                const response = await this.$axios.put(
                    `/operator/${operatorId}`,
                    this.operator
                );
                if (response.data.success) {
                    this.$router.push({ name: "OperatorIndex" });
                    toast.success("Operator updated successfully!");
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    toast.error("Failed to update operator.");
                } else {
                    this.serverError = "Error updating operator.";
                    toast.error(this.serverError);
                }
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.fetchOperator(); // Reset the form with existing operator data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
