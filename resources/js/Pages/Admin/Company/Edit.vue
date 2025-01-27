<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>Edit Company</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="company.name"
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
                    v-model="company.status"
                    :items="statusItems"
                    label="Company Status"
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
                           class="primary-color"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update Group
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
            statusItems: ["Active", "In-active"],
            company: {
                name: "",
                status: "Active",
            },
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchCompany();
    },
    methods: {
        async fetchCompany() {
            const id = this.$route.params.id;
            try {
                const response = await this.$axios.get(
                    `/company/${id}/edit`
                );
                this.company = response.data.item;
                this.company.status =
                    this.company.status === "Active" ? "Active" : "In-active";
            } catch (error) {
                this.serverError = "Error fetching group data.";
            }
        },
        async update() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;
            const id = this.$route.params.id;
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/company/${id}`,
                        this.company
                    );

                    if (response.data.success) {

                        toast.success("Company update successfully!");
                        setTimeout(async () => {
                            this.$router.push({ name: "AllCompanyIndex" });
                        }, 2000);
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating company.";
                    }
                } finally {
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchCompany();
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
