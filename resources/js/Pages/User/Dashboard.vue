<template>
    <div>
        <h2>User Dashboard</h2>
        <p>Welcome to the user dashboard, {{ authStore.user?.name }}!</p>

        <!-- Logout button -->
        <button @click="logout" class="btn btn-primary">Logout</button>
    </div>
</template>

<script setup>
import { useAuthStore } from "../../stores/authStore"; // Path to your auth store
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

// Logout function
const logout = async () => {
    try {
        await authStore.logout(); // Call the Pinia store's logout action
        router.push({ name: "Login" }); // Redirect to login page after logout
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>

<style scoped>
/* Optional: Style the button */
button {
    margin-top: 20px;
}
</style>
