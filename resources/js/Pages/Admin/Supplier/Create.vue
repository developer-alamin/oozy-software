<template>
    <v-card outlined class="mx-auto my-5">
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
            
                 <v-row>
                    <v-col col="12" md="6">
                        <v-autocomplete
                            v-model="supplier.company_id"
                            :items="companies"
                            item-value="id"
                            item-title="name"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="errors.company_id || ''"
                            @update:search="fetchCompanies"
                            no-filter
                            >
                            <template v-slot:label>
                                Select Company <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                        
                    </v-col>
                    <v-col col="12" md="6">
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
                        
                    </v-col>
                 </v-row>
                <v-row>
                    <v-col col="12" md="6">
                        <!-- Email Field -->
                        <v-text-field
                            v-model="supplier.email"
                            label="Email"
                            :rules="[rules.required, rules.email]"
                            :error-messages="errors.email ? errors.email : ''"
                            required
                        />
                    </v-col>
                    <v-col col="12" md="6">
                        <!-- Phone Field -->
                        <v-text-field
                            v-model="supplier.phone"
                            label="Phone"
                            :rules="[rules.required]"
                            :error-messages="errors.phone ? errors.phone : ''"
                            required
                        />
                        
                    </v-col>
                </v-row>
                <v-row>
                    <v-col col="12" md="6">
                       <!-- Contact Person Field -->
                       <v-text-field
                            v-model="supplier.contact_person"
                            label="Contact Person"
                            :error-messages="
                                errors.contact_person ? errors.contact_person : ''
                            "
                        />
                    </v-col>
                    <v-col col="12" md="6">
                        <v-file-input
                            accept="image/png, image/jpeg, image/bmp"
                            label="Photo"
                            placeholder="Pick an avatar"
                            prepend-icon="mdi-camera"
                            @change="onFilePicked($event)"
                        >
                        </v-file-input>
                    </v-col>
                </v-row>
            
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
                           class="primary-color"
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
                company_id:null,
                name: "",
                email: "",
                phone: "",
                imageFile: "",
                contact_person: "",
                address: "",
                description: "",
            },
            limit: 5,
            companies:[],
            selectedCompany: null,
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
                    if (response.data.success) {
                       
                        toast.success("Supplier Created successfully!");
                         this.resetForm();
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
                company_id:'',
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
            this.supplier.type = "Mechine";
        },
        formatCompany(company) {
        if (company) {
            console.log(company)
            if (typeof company === "number") {
                // Use strict equality (===) and ensure proper assignment in the find function
                company = this.companies.find((item) => item.id === company);
            }
            // Safely return the company name or a fallback if the name is missing
            return company && company.name ? company.name : "No Company Name";
            }
            // Fallback if no company data is provided
            return "No Company Data";
        },
        async fetchCompanies(search) {
            try {
                // Make a GET request to the '/get_companies' endpoint with query parameters
                const response = await this.$axios.get('/get_companies', {
                    params: {
                    search: this.search || '', // Use `this.search` or fallback to an empty string
                    limit: this.limit || 5,   // Use `this.limit` or fallback to default value (5)
                    },
                });
                // Update the companies array with the fetched data
                this.companies = response.data;
                } catch (error) {
                // Log any errors that occur during the request
                console.error('Error fetching companies:', error);
                // Optionally, handle the error (e.g., show an error message to the user)
                this.$toast.error('Failed to fetch companies. Please try again later.');
                }

        },
        onFileChange(event) {
            this.photo = event.target.files[0];
        },
    },
};
</script>
