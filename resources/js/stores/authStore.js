import { defineStore } from "pinia";
import axios from "../axiosInstance";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        role: null, // Store the role of the user/admin/superadmin
    }),
    actions: {
        initializeAuth() {
            const storedToken = localStorage.getItem("token");
            const storedRole = localStorage.getItem("role"); // Assuming you store the role as well
            if (storedToken) {
                this.token = storedToken;
                this.role = storedRole; // Restore the role from localStorage
            }
        },
        setAuth(token, role) {
            this.token = token;
            this.role = role;
            localStorage.setItem("token", token);
            localStorage.setItem("role", role);
        },

        async login(email, password) {
            const response = await axios.post("/user/login", {
                email,
                password,
            });
            this.user = response.data.user;
            this.role = response.data.role; // Store the role from response
            localStorage.setItem("token", response.data.token); // Save token for future requests
            localStorage.setItem("role", response.data.role);
        },
        async logout() {
            await axios.post("/user/logout");
            this.user = null;
            this.role = null;
            localStorage.removeItem("token");
            localStorage.removeItem("role");
        },

        async fetchUser() {
            const response = await axios.get("/user/dashboard"); // Adjust endpoint if necessary
            this.user = response.data;
        },
        async register(name, email, password, password_confirmation, role) {
            const response = await axios.post("/user/register", {
                name,
                email,
                password,
                password_confirmation,
                role,
            });
            this.user = response.data.user;
            this.role = response.data.role;
            localStorage.setItem("token", response.data.token);
        },
    },
});
