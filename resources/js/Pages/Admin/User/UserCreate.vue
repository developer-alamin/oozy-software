<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Create user</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <!-- Name Field -->
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
                <!-- Name Field -->
                <v-text-field
                    v-model="user.email"
                    :rules="[rules.required, rules.email]"
                    label="Email"
                    outlined
                    :error-messages="errors.email ? errors.email : ''"
                >
                    <template v-slot:label>
                        Email <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <v-text-field
                    v-model="user.phone"
                    :rules="[rules.phone]"
                    label="Phone"
                    outlined
                    :error-messages="errors.phone ? errors.phone : ''"
                >
                    <template v-slot:label> Phone </template>
                </v-text-field>

                <v-text-field
                    v-model="user.password"
                    :rules="[rules.required]"
                    label="Password"
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'"
                    placeholder="Enter your password"
                    outlined
                    @click:append-inner="visible = !visible"
                    :error-messages="errors.password ? errors.password : ''"
                    ><template v-slot:label>
                        Password <span style="color: red">*</span></template
                    ></v-text-field
                >

                <v-text-field
                    v-model="user.confirm_password"
                    :rules="[rules.required, rules.confirm_password]"
                    label=" Confirm Password "
                    :append-inner-icon="
                        confirm_visible ? 'mdi-eye-off' : 'mdi-eye'
                    "
                    :type="confirm_visible ? 'text' : 'password'"
                    placeholder="Enter your Confirm Password "
                    outlined
                    @click:append-inner="confirm_visible = !confirm_visible"
                    :error-messages="
                        errors.confirm_password ? errors.confirm_password : ''
                    "
                    ><template v-slot:label>
                        Confirm Password
                    </template></v-text-field
                >
                <!-- Description Field -->
                <v-textarea
                    v-model="user.address"
                    label="Address"
                    :error-messages="errors.description ? errors.address : ''"
                />
                <v-select
                    v-model="user.status"
                    :items="statusItems"
                    label="User Status"
                    clearable
                ></v-select>

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
                            Create user
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
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "Inactive"],
            user: {
                role: "admin",
                name: "",
                email: "",
                phone: "",
                password: "",
                confirm_password: "",
                photo: "",
                address: "",
                status: "Active", // New property for checkbox
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
                confirm_password: (value) =>
                    value === this.user.password || "Passwords must match.", // Confirms password matches
                phone: (value) =>
                    /^\d{11}$/.test(value) || "Phone number must be valid.",
            },
            visible: false,
            confirm_visible: false,
        };
    },
    methods: {
        async submit() {
            // Reset errors and loading state before submission
            this.errors = {};
            this.serverError = null;
            this.loading = true; // Start loading when submit is clicked

            const formData = new FormData();
            Object.entries(this.user).forEach(([key, value]) => {
                formData.append(key, value);
            });

            // Simulate a 3-second loading time (e.g., for an API call)
            setTimeout(async () => {
                try {
                    // Assuming the actual API call here
                    const response = await this.$axios.post(
                        "/admin/company/user/store",
                        formData
                    );

                    if (response.data.success) {
                        toast.success("User create successfully!");
                        // localStorage.setItem("token", response.data.token);
                        this.resetForm();
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to create user.");
                        // Handle validation errors from the server
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Failed to create user.");
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
            this.user = {
                name: "",
                email: "",
                phone: "",
                password: "",
                confirm_password: "",
                photo: "",
                address: "",
                status: "Active", // New property for checkbox
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },
    },
};
</script>
