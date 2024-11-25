import { ref, onMounted } from "vue";
import axios from "axios";
import router from "@/router/router";
import { useAuthStore } from "@/stores/authStore";

const allowedMethods = ["get", "post", "put", "patch", "delete"] as const;
type Method = (typeof allowedMethods)[number];

export async function useAxios(
  method: Method,
  url: string,
  data = null,
  isAuth = true,
  headers: {} = {}
) {
  const store = useAuthStore();
  const api = axios.create({
    baseURL: "/api",
    xsrfCookieName: "XSRF-Token",
    xsrfHeaderName: "X-SRF-Token",
    headers: {
      Accept: "application/json",
      ContentType: "application/json",
      ...headers,
    },
  });

  api.interceptors.request.use(
    (config) => {
      const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
      if (csrfToken) {
        config.headers["X-CSRF-TOKEN"] = csrfToken.content;
      }
      if (isAuth) {
        config.withCredentials = true;
        config.headers["Authorization"] = `Bearer ${store.getToken}`;
      }
      return config;
    },
    (error) => {
      return Promise.reject(error);
    }
  );

  // Response Interceptor to handle 403 errors
  api.interceptors.response.use(
    (response) => {
      return response;
    },
    async (error) => {
      if (
        error.response &&
        (error.response.status === 401 || error.response.status === 403)
      ) {
        let auth = await store.checkServerAuth();
        if (!auth) {
          store.removeUserToken();
          router.push({ name: "Login" });
        }
      }
      return Promise.reject(error);
    }
  );

  try {
    return await (api as any)[method](url, data);
  } catch (e: any) {
    return e.response;
  }
}
