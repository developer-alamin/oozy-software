<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Category</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="category.name"
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
                    v-model="category.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Status Field (Checkbox) -->
                <!-- <v-checkbox
                    v-model="category.status"
                    label="Status"
                    :error-messages="errors.status ? errors.status : ''"
                /> -->

                <v-select
                    v-model="category.status"
                    :items="statusItems"
                    label="Category Status"
                    @change="updateStatus"
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
                            Update Category
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
            category: {
                name: "",
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
        this.fetchCategory();
    },
    methods: {
        async fetchCategory() {
            // Fetch the category data to populate the form
            const categoryId = this.$route.params.uuid; // Assuming the category ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/category/${categoryId}/edit`
                );
                console.log(response.data);

                this.category = response.data.category; // Populate form with the existing category data
                // this.category.status =
                //     this.category.status == "true" ||
                //     this.category.status == true;
                this.category.status =
                    this.category.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                this.serverError = "Error fetching category data.";
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const categoryId = this.$route.params.uuid; // Assuming category ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/category/${categoryId}`,
                        this.category
                    );

                    if (response.data.success) {
                        toast.success("Category updated successfully!");
                        this.$router.push({ name: "CategoryIndex" }); // Redirect to category list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update category.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating category.");
                        this.serverError = "Error updating category.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchCategory(); // Reset the form with existing category data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
