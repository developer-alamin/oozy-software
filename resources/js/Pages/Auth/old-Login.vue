<template>
    <div class="container">
        <h2>Login</h2>
        <form @submit.prevent="handleLogin">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    v-model="email"
                    class="form-control"
                    id="email"
                    required
                />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    v-model="password"
                    class="form-control"
                    id="password"
                    required
                />
            </div>

            <button type="submit" class="btn btn-primary">Login</button>

            <p v-if="error" class="text-danger">{{ error }}</p>
        </form>

        <p class="mt-3">
            Don't have an account?
            <router-link to="/register">Register here</router-link>
        </p>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useAuthStore } from "../../stores/authStore";
import { useRouter } from "vue-router";

const email = ref("");
const password = ref("");
const error = ref("");
const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
    try {
        await authStore.login(email.value, password.value);

        // Redirect based on role
        if (authStore.role === "admin") {
            router.push({ name: "AdminDashboard" }); // Admin dashboard route
        } else {
            router.push({ name: "UserDashboard" }); // User dashboard route
        }
    } catch (err) {
        error.value = err.response?.data.message || "Login failed";
    }
};
</script>
