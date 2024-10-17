<template>
    <div>
        <h2>Register</h2>
        <form @submit.prevent="handleRegister">
            <input
                v-model="name"
                type="text"
                class="form-control"
                placeholder="Name"
                required
            />
            <input
                v-model="email"
                type="email"
                class="form-control mb-2"
                placeholder="Email"
                required
            />
            <input
                v-model="password"
                type="password"
                placeholder="Password"
                class="form-control mb-2"
                required
            />
            <input
                v-model="password_confirmation"
                type="password"
                class="form-control mb-2"
                placeholder="Confirm Password"
                required
            />
            <select v-model="role" class="form-control mb-2">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" class="btn btn-primary">Register</button>
            <p v-if="error">{{ error }}</p>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../../stores/authStore";
import { useRouter } from "vue-router";

const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const role = ref("user"); // Default to user
const error = ref("");
const authStore = useAuthStore();
const router = useRouter();

const handleRegister = async () => {
    try {
        await authStore.register(
            name.value,
            email.value,
            password.value,
            password_confirmation.value,
            role.value
        );
        if (authStore.role === "admin") {
            router.push({ name: "AdminDashboard" });
        } else {
            router.push({ name: "UserDashboard" });
        }
    } catch (err) {
        error.value = "Registration failed";
    }
};
</script>
