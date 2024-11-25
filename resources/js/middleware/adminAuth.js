// src/middleware/auth.js
import { useAuthStore } from "../stores/authStore";

export default function adminAuthMiddleware(to, from, next) {
  const authStore = useAuthStore();
  const isAuthenticated = authStore?.auth;
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: "Login" });
  } else {
    next();
  }
}
