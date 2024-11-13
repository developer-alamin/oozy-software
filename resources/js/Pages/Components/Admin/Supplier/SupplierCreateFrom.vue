<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>
            <v-row>
                <v-col cols="12" md="6"> Create Supplier </v-col>
                <v-col cols="12" md="6">
                    <v-img
                        :width="50"
                        aspect-ratio="16/9"
                        cover
                        :src="imageUrl"
                        style="margin-left: auto"
                        v-if="imageUrl"
                    ></v-img>
                </v-col>
            </v-row>
        </v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-select
                    v-model="supplier.type"
                    :rules="[rules.required]"
                    :items="statusSupplierItems"
                    label="Supplier Type"
                    density="comfortable"
                    clearable
                >
                    <template v-slot:label>
                        Supplier Type <span style="color: red">*</span>
                    </template>
                </v-select>
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
                <v-file-input
                    accept="image/png, image/jpeg, image/bmp"
                    label="Photo"
                    placeholder="Pick an avatar"
                    prepend-icon="mdi-camera"
                    @change="onFilePicked($event)"
                >
                </v-file-input>

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
                            Create Supplier
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
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            imageUrl: "",
            valid: false,
            loading: false,
            statusSupplierItems: ["Mechine", "Parse"],
            supplier: {
                type: "Mechine",
                name: "",
                email: "",
                phone: "",
                imageFile: "",
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
        async onFilePicked(e) {
            const files = e.target.files;
            if (files[0] !== undefined) {
                const fr = new FileReader();
                fr.readAsDataURL(files[0]);
                fr.addEventListener("load", () => {
                    this.imageUrl = fr.result;
                    this.supplier.imageFile = files[0];
                });
            }
        },
        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null; // Reset server error
            this.loading = true;

            const formData = new FormData();
            Object.entries(this.supplier).forEach(([key, value]) => {
                formData.append(key, value);
            });
            // if (this.photo) {
            //     formData.append("photo", this.photo);
            // }
            setTimeout(async () => {
                try {
                    const response = await this.$axios.post(
                        "/suppliers",
                        formData
                    );
                    // console.log(response.data)

                    if (response.data.success) {
                        this.resetForm();
                        toast.success("Supplier Created successfully!");
                        // Notify the user on success (e.g., with a toast)
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        // Handle validation errors
                        this.errors = error.response.data.errors || {};
                    } else {
                        // Handle other types of errors
                        this.serverError =
                            "An error occurred. Please try again.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.supplier = {
                type: "",
                name: "",
                email: "",
                phone: "",
                imageFile: "",
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
