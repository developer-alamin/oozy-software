<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>
            <v-row>
                <v-col cols="12" md="6"> Create Rent </v-col>
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
                <v-container>
                    <v-row>
                        <!-- <v-col cols="12" md="6">
                            <v-file-input
                                accept="image/png, image/jpeg, image/bmp"
                                label="Photo"
                                placeholder="Pick an avatar"
                                prepend-icon="mdi-camera"
                                @change="onFilePicked($event)"
                            >    
                        </v-file-input>
                        </v-col> -->
                        <v-col cols="12" md="6">
                            <!-- Name Field -->
                            <v-text-field
                                v-model="rent.name"
                                :rules="[rules.required]"
                                label="Name"
                                outlined
                                :error-messages="errors.name ? errors.name : ''"
                            >
                                <template v-slot:label>
                                    Name <span style="color: red">*</span>
                                </template>
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="rent.email" label="E-mail">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="rent.phone" label="Phone">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="rent.address"
                                label="Address"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                </v-container>

                <!-- Description Field -->
                <v-textarea
                    v-model="rent.description"
                    label="Description"
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <!-- Submit Button -->

                    <!-- Reset Button -->
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
                           class="primary-color"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Create Rent
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
import { ref } from "vue";
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            imageUrl: "",
            valid: false,
            loading: false, // Controls loading state of the button
            rent: {
                name: "",
                description: "",
                imageFile: "",
                email: "",
                phone: "",
                address: "",
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
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
                    this.rent.imageFile = files[0];
                });
            }
        },
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();

            Object.entries(this.rent).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post("/rent", formData);
                    console.log(response.data);
                    if (response.data.success) {
                        this.resetForm();
                        toast.success("Rent Created successfully!");
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        // Handle other server errors
                        this.serverError =
                            "An error occurred. Please try again.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000); // Simulates a 3-second loading duration
        },
        resetForm() {
            this.rent = {
                name: "",
                description: "",
                status: "", // Reset checkbox on form reset
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
};
</script>
