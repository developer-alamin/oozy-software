<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Unit</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="unit.name"
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
                    v-model="unit.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Status Field (Checkbox) -->
                <v-checkbox
                    v-model="unit.status"
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
                            Update Unit
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
            unit: {
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
        this.fetchUnit();
    },
    methods: {
        async fetchUnit() {
            // Fetch the unit data to populate the form
            const unitId = this.$route.params.id; // Assuming the unit ID is passed in the route params
            try {
                const response = await this.$axios.get(`/units/${unitId}/edit`);
                console.log(response.data);

                this.unit = response.data.unit; // Populate form with the existing unit data
                this.unit.status =
                    this.unit.status == "true" || this.unit.status == true;
            } catch (error) {
                this.serverError = "Error fetching unit data.";
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const unitId = this.$route.params.id; // Assuming unit ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/units/${unitId}`,
                        this.unit
                    );

                    if (response.data.success) {
                        this.$router.push({ name: "UnitIndex" }); // Redirect to unit list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating unit.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchUnit(); // Reset the form with existing unit data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
