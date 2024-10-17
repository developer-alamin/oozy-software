import axios from "axios";

// Create an Axios instance
const axiosInstance = axios.create({
    baseURL: "/api", // Your API base URL
});

// Interceptors to handle token
axiosInstance.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("token"); // Adjust based on your token storage
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default axiosInstance;
