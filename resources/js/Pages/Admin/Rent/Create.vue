<template>
    <v-card outlined class="mx-auto my-5" max-width="">
         <v-card-title>
            <v-row>
                <v-col cols="12" md="6">
                     Create Rent
                </v-col>
                 <v-col cols="12" md="6">
                    <v-img
                      :width="50"
                      aspect-ratio="16/9"
                      cover
                      src="https://cdn.vuetifyjs.com/images/parallax/material.jpg" style="margin-left:auto"></v-img>
                </v-col>
            </v-row>
        
        </v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">

                <v-container>
                    <v-row>
                        <v-col cols="12" md="6">
                             <v-file-input
                                :rules="rules"
                                accept="image/png, image/jpeg, image/bmp"
                                label="Photo"
                                placeholder="Pick an avatar"
                                prepend-icon="mdi-camera"
                                @onchange="previewImg()"
                              >    
                              </v-file-input>
                        </v-col>
                        <v-col cols="12" md="6">
                            <!-- Name Field -->
                            <v-text-field
                                v-model="brand.name"
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
                            <v-text-field
                                label="E-mail"
                              >  
                              </v-text-field>
                        </v-col> 
                         <v-col cols="12" md="6">
                            <v-text-field
                                label="Phone"
                              >  
                              </v-text-field>
                        </v-col> 
                        <v-col cols="12">
                            <v-text-field
                                label="Address"
                              >  
                              </v-text-field>
                        </v-col>  
                    </v-row>
                </v-container>
                

                <!-- Description Field -->
                <v-textarea
                    v-model="brand.description"
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
                            color="primary"
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
export default {
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "In-Active"],
            brand: {
                name: "",
                description: "",
                status: "", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    methods: {
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.brand).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post("/brand", formData);

                    if (response.data.success) {
                        this.resetForm();
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
            this.brand = {
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
