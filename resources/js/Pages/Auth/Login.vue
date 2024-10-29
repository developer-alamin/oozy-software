<template>
    <div class="login-bg">
        <v-img class="mx-auto my-6" max-width="100" :src="loginImage"></v-img>

        <v-card
            class="mx-auto pa-10 pb-3"
            elevation="8"
            max-width="448"
            rounded="lg"
        >
            <div class="text-h5 text-center mb-3 font-weight-bold">Sing In</div>

            <v-form v-model="formValid" ref="loginForm">
                <!-- Email Field -->
                <v-text-field
                    class="mb-1"
                    density="compact"
                    label="Email address"
                    v-model="email"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    type="email"
                    :rules="emailRules"
                    required
                ></v-text-field>

                <!-- Password Field -->
                <v-text-field
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'"
                    density="compact"
                    label="Password"
                    v-model="password"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    @click:append-inner="visible = !visible"
                    :rules="passwordRules"
                    required
                ></v-text-field>

                <!-- Remember Me Checkbox -->
                <v-checkbox
                    v-model="rememberMe"
                    label="Remember me"
                ></v-checkbox>

                <!-- Submit Button -->
                <v-btn
                    class="mb-4"
                    color="blue"
                    size="large"
                    variant="tonal"
                    block
                    :disabled="!formValid"
                    @click="handleLogin"
                >
                    Log In
                </v-btn>

                <!-- Backend Error Message -->
                <p v-if="error" class="text-danger">{{ error }}</p>
            </v-form>

            <v-card-text class="text-center">
                Don't have an account?
                <router-link
                    to="/register"
                    class="text-blue text-decoration-none"
                >
                    Sign up now <v-icon icon="mdi-chevron-right"></v-icon>
                </router-link>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../../stores/authStore";
import loginImage from "../../../img/login_page_img.png";

import { useRouter } from "vue-router";

// Frontend form state
const email = ref("");
const password = ref("");
const rememberMe = ref(false); // Remember me checkbox
const error = ref("");
const formValid = ref(false); // Form validation status
const visible = ref(false);

// Validation rules for frontend
const emailRules = [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "Email must be valid",
];
const passwordRules = [
    (v) => !!v || "Password is required",
    (v) => v.length >= 8 || "Password must be at least 8 characters long",
];

// Auth store and router
const authStore = useAuthStore();
const router = useRouter();

// Handle login function
const handleLogin = async () => {
    if (!formValid.value) return; // Prevent submission if form is invalid

    try {
        await authStore.login(email.value, password.value, rememberMe.value); // Pass the remember me option

        // Redirect based on role
        if (authStore.role === "admin" || authStore.role === "superadmin") {
            router.push({ name: "AdminDashboard" });
        } else {
            router.push({ name: "UserDashboard" });
        }
    } catch (err) {
        // Capture backend error message
        error.value = err.response?.data.message || "Login failed";
    }
};
</script>
<style scoped></style>
