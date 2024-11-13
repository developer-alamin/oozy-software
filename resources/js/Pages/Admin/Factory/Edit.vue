<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Edit Factory</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-autocomplete
                    v-model="factory.company_id"
                    :items="companys"
                    item-value="id"
                    item-title="name"
                    outlined
                    clearable
                    chips
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.company_id ? errors.company_id : ''"
                    @update:search="fetchCompanys"
                >
                    <template v-slot:label>
                        Select Company <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <v-text-field
                    v-model="factory.name"
                    :rules="[rules.required]"
                    label="Factory Name"
                    outlined
                    density="comfortable"
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Factory Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-autocomplete
                    v-model="factory.floor_ids"
                    :items="floors"
                    item-value="id"
                    item-title="name"
                    label="Select Floor"
                    outlined
                    multiple
                    clearable
                    chips
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.floor_ids ? errors.floor_ids : ''"
                    @update:search="fetchFloors"
                >
                    <template v-slot:label>
                        Select Floor <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <v-autocomplete
                    v-model="factory.unit_ids"
                    :items="units"
                    item-value="id"
                    item-title="name"
                    label="Select Unit"
                    outlined
                    multiple
                    clearable
                    chips
                    density="comfortable"
                    :rules="[rules.required]"
                    :error-messages="errors.unit_ids ? errors.unit_ids : ''"
                    @update:search="fetchUnits"
                >
                    <template v-slot:label>
                        Select Unit <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <v-autocomplete
                    v-model="factory.line_ids"
                    :items="lines"
                    item-value="id"
                    item-title="name"
                    label="Select Line"
                    density="comfortable"
                    multiple
                    clearable
                    chips
                    :rules="[rules.required]"
                    :error-messages="errors.line_ids ? errors.line_ids : ''"
                    @update:search="fetchLines"
                >
                    <template v-slot:label>
                        Select Line <span style="color: red">*</span>
                    </template>
                </v-autocomplete>

                <v-text-field
                    v-model="factory.email"
                    :rules="[rules.required, rules.email]"
                    label="Email"
                    outlined
                    density="comfortable"
                    :error-messages="errors.email ? errors.email : ''"
                >
                    <template v-slot:label>
                        Email <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-text-field
                    v-model="factory.phone"
                    :rules="[rules.phone]"
                    label="Phone"
                    outlined
                    density="comfortable"
                    :error-messages="errors.phone ? errors.phone : ''"
                >
                    <template v-slot:label> Phone </template>
                </v-text-field>

                <v-text-field
                    v-model="factory.factory_code"
                    :rules="[rules.factory_code]"
                    label="Factory Code"
                    outlined
                    density="comfortable"
                    :error-messages="
                        errors.factory_code ? errors.factory_code : ''
                    "
                >
                    <template v-slot:label> Factory Code </template>
                </v-text-field>

                <v-textarea
                    v-model="factory.location"
                    label="Location"
                    density="comfortable"
                    :error-messages="errors.location ? errors.location : ''"
                />
                <v-select
                    v-model="factory.status"
                    :items="statusItems"
                    label="Factory Status"
                    clearable
                    density="comfortable"
                ></v-select>

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
                            Update Factory
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
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            valid: false,
            loading: false,
            statusItems: ["Active", "Inactive"],
            factory: {
                company_id: null,
                floor_ids: [],
                unit_ids: [],
                line_ids: [],
                name: "",
                email: "",
                phone: "",
                location: "",
                factory_code: "",
                status: "Active",
            },
            errors: {},
            serverError: null,
            limit: 5,
            companys: [],
            floors: [],
            units: [],
            lines: [],
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
                confirm_password: (value) =>
                    value === this.company.password || "Passwords must match.", // Confirms password matches
                phone: (value) =>
                    /^\d{11}$/.test(value) || "Phone number must be valid.",
            },
        };
    },
    methods: {
        async submit() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;

            const formData = new FormData();
            Object.entries(this.factory).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach((val) => formData.append(`${key}[]`, val));
                } else {
                    formData.append(key, value);
                }
            });

            try {
                const response = await this.$axios.put(
                    `/factory/${this.factory.id}`,
                    formData
                );
                if (response.data.success) {
                    toast.success("Factory updated successfully!");
                    this.resetForm();
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.serverError = "An unexpected error occurred.";
                }
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.factory = {
                company_id: null,
                floor_ids: [],
                unit_ids: [],
                line_ids: [],
                name: "",
                email: "",
                phone: "",
                location: "",
                factory_code: "",
                status: "Active",
            };
            this.errors = {};
            this.serverError = null;
            if (this.$refs.form) {
                this.$refs.form.reset();
            }
        },
        async fetchFactoryData() {
            const factoryId = this.$route.params.uuid; // Assuming you're passing factory ID in route
            const response = await this.$axios.get(
                `/factory/${factoryId}/edit`
            );
            console.log(response.data);

            if (response.data.success) {
                this.factory = response.data.factory;
            }
        },
        // Fetch companys, floors, units, and lines as needed
        async fetchCompanys(search) {
            // Implement your logic here
        },
        async fetchFloors(search) {
            // Implement your logic here
        },
        async fetchUnits(search) {
            // Implement your logic here
        },
        async fetchLines(search) {
            // Implement your logic here
        },
    },
    mounted() {
        this.fetchFactoryData();
    },
};
</script>
