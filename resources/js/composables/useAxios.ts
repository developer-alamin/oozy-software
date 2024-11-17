// Import required packages
import axios, { AxiosError, AxiosRequestConfig, AxiosResponse } from "axios";
import { ref } from "vue";

// Create an Axios instance
const axiosInstance = axios.create({
  baseURL: "/api",
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
});


const isLoading = ref(false);

// Interceptors to handle token
axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    console.error("Request Error:", error);
    return Promise.reject(error);
  }
);


export function useAxios() {
  const makeRequest = async (config: AxiosRequestConfig): Promise<AxiosResponse | undefined> => {
  try {
    isLoading.value = true;
    const response: AxiosResponse = await axiosInstance(config);
    isLoading.value = false;
    return response;
  } catch (error: unknown) {
    isLoading.value = false;
    if (axios.isAxiosError(error)) {
      return error.response;
    } else {
      return undefined;
    }
  }
};

  return {
    axiosInstance,
    makeRequest,
    isLoading,
  };
}
