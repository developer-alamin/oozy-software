<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>
            <v-row>
                <v-col cols="12" md="6"> Create User </v-col>
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
                        <v-file-input
                            accept="image/png, image/jpeg, image/bmp"
                            label="Photo"
                            placeholder="Pick an avatar"
                            prepend-icon="mdi-camera"
                            @change="onFilePicked($event)"
                        >
                        </v-file-input>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="user.name"
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
                            v-model="user.email"
                            :rules="[rules.required]"
                            label="Email"
                            outlined
                            :error-messages="errors.email ? errors.email : ''"
                        >
                            <template v-slot:label>
                                Email <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="user.phone"
                            label="Phone"
                            outlined
                            :error-messages="errors.phone ? errors.phone : ''"
                        >
                            <template v-slot:label>
                                Phone <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="user.code"
                            label="Code"
                            outlined
                            :error-messages="errors.code ? errors.code : ''"
                        >
                            <template v-slot:label>
                                Code <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="user.address"
                            label="Address"
                            outlined
                            :error-messages="errors.address ? errors.address : ''"
                        >
                            <template v-slot:label>
                                Address <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12 " md="6">
                        <v-text-field
                            v-model="user.password"
                            hint="Enter your password to access this website"
                            label="Password"
                            :rules="[rules.required]"
                            type="password"
                            :error-messages="errors.password ? errors.password : ''"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea
                            v-model="user.description"
                            label="Description"
                            :error-messages="
                                errors.description ? errors.description : ''
                            "
                        />
                    </v-col>
                </v-row>
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
                            Create User
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</template>
<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";
export default {
  name: "CompanyNext",
  data() {
    return {
        imageUrl: "",
        valid: false,
        loading: false, 
        user:{
            name:'',
            email:'',
            phone:'',
            code:'',
            password:'',
            address:'',
            description:'',
            imageFile: "",
            companyName:'',
            status:'',
        }, 
        errors: {}, // Stores validation errors
        serverError: null, // Stores server-side error messages
        rules: {
            required: (value) => !!value || "Required.",
        },
        visible: false,
        confirm_visible: false,
    };
  },
  methods:{
    onFilePicked(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
          this.imageUrl = reader.result;
           
          this.user.imageFile = file;
          console.log(this.user.imageFile);
        };
      }
    },   
    async submit(){
        this.errors = {}; // Reset errors before submission
        this.serverError = null; // Reset server error
        this.loading = true;
        const formData = new FormData();
        Object.entries(this.user).forEach(([key, value]) => {
            formData.append(key, value);
        });
        setTimeout(async () => {
            try {
                const response = await this.$axios.post(
                    "/company",
                    formData
                );
                if (response.data.success) {
                    toast.success("Company and user created successfully!");
                    this.$router.push({ name: "AllCompanyIndex" });
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
    }
  },
  created() {
    // Retrieve the state from the window.history API
    const state = window.history.state;

    if (state && state.item) {
       this.user.companyName = state.item.name;
       this.user.status = state.item.status;
    } else {
        this.$router.push({ name: "CompanyCreate" });
    }
  },
    resetForm() {
        if (this.$refs.form) {
            this.$refs.form.reset(); // Reset the form via its ref if necessary
        }
    },
};
</script>
