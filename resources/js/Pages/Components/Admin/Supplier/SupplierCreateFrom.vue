<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Create Supplier</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
                <v-text-field
                    v-model="supplier.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Name <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Email Field -->
                <v-text-field
                    v-model="supplier.email"
                    label="Email"
                    :rules="[rules.required, rules.email]"
                    :error-messages="errors.email ? errors.email : ''"
                    required
                />

                <!-- Phone Field -->
                <v-text-field
                    v-model="supplier.phone"
                    label="Phone"
                    :rules="[rules.required]"
                    :error-messages="errors.phone ? errors.phone : ''"
                    required
                />

                <!-- Contact Person Field -->
                <v-text-field
                    v-model="supplier.contact_person"
                    label="Contact Person"
                    :error-messages="
                        errors.contact_person ? errors.contact_person : ''
                    "
                />

                <!-- Address Field -->
                <v-textarea
                    v-model="supplier.address"
                    label="Address"
                    :error-messages="errors.address ? errors.address : ''"
                />

                <!-- Description Field -->
                <v-textarea
                    v-model="supplier.description"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />

                <!-- Photo Upload Field -->
                <!-- <v-file-input
                    v-model="photo"
                    label="Upload Photo"
                    accept="image/*"
                    :error-messages="errors.photo ? errors.photo : ''"
                    @change="onFileChange"
                /> -->

                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <!-- Submit Button -->
                    <v-col cols="6">
                        <v-btn type="submit" color="primary" :disabled="!valid">
                            Create Supplier
                        </v-btn>
                    </v-col>

                    <!-- Reset Button -->
                    <v-col cols="6" class="text-right">
                        <v-btn
                            type="button"
                            color="secondary"
                            @click="resetForm"
                        >
                            Reset Form
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>

    <!-- Server Error Message -->
    <v-alert v-if="serverError" type="error" class="my-4">
        {{ serverError }}
    </v-alert>
</template>

<script>
export default {
    data() {
        return {
            valid: false,
            supplier: {
                name: "",
                email: "",
                phone: "",
                contact_person: "",
                address: "",
                description: "",
            },
            photo: null,
            errors: {}, // Initialize errors as an empty object
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
            },
        };
    },
    methods: {
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null; // Reset server error

            const formData = new FormData();
            Object.entries(this.supplier).forEach(([key, value]) => {
                formData.append(key, value);
            });
            if (this.photo) {
                formData.append("photo", this.photo);
            }

            try {
                const response = await this.$axios.post("/suppliers", formData);

                if (response.data.success) {
                    this.resetForm();
                    // Notify the user on success (e.g., with a toast)
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Handle validation errors
                    this.errors = error.response.data.errors || {};
                } else {
                    // Handle other types of errors
                    this.serverError = "An error occurred. Please try again.";
                }
            }
        },
        resetForm() {
            this.supplier = {
                name: "",
                email: "",
                phone: "",
                contact_person: "",
                address: "",
                description: "",
            };
            this.photo = null;
            this.errors = {}; // Reset errors on form reset
            this.$refs.form.reset();
        },
        onFileChange(event) {
            this.photo = event.target.files[0];
        },
    },
};
</script>
