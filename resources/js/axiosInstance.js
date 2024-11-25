import axios from "axios";
import router from "@/router/router";
import { useAuthStore as store } from "@/stores/authStore";

// Create an Axios instance
const axiosInstance = axios.create({
  baseURL: "/api", // Your API base URL
});

// Interceptors to handle token
axiosInstance.interceptors.request.use(
  (config) => {
    const token = store().getToken; // Adjust based on your token storage
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

axiosInstance.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    if (
      error.response &&
      (error.response.status === 401 || error.response.status === 403)
    ) {
      let auth = await store().checkServerAuth();
      if (!auth) {
        store().removeUserToken();
        router.push({ name: "Login" });
      }
    }
    return Promise.reject(error);
  }
);

export default axiosInstance;
