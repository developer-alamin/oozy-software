import { defineStore } from "pinia";
import axios from "../axiosInstance";

import router from "@/router/router";

import { useCookie } from "../composables/useCookie";
import { useAxios } from "../composables/useAxios";

const { getCookie, setCookie, updateCookie, removeCookie } = useCookie();

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token:
      getCookie("__authToken") == "null"
        ? null
        : getCookie("__authToken") || null,
    user_id:
      getCookie("user_id") == "null" ? null : getCookie("user_id") || null,
    user: localStorage.getItem("user") || null,
    role: localStorage.getItem("role") || null,
  }),
  getters: {
    getToken: (state) => {
      return state.token;
    },
    getUserId: (state) => {
      return state.user_id;
    },
    getUser: (state) => {
      return state.user;
    },
    getRole: (state) => {
      return state.role;
    },
    auth: (state) => {
      return state.token ? true : false;
    },
  },
  actions: {
    setToken(token) {
      setCookie("__authToken", token);
      this.token = token;
    },
    setUserId(user_id) {
      this.user_id = user_id;
    },
    setUser(user) {
      this.user = user;
    },
    setRole(role) {
      this.role = role;
    },

    async login(email, password) {
      const response = await useAxios("post", "/user/login", {
        email,
        password,
      });

      this.token = response.data.token;
      this.user = response.data.user;
      this.role = response.data.user.role;

      console.log(this.token);

      setCookie({ name: "__authToken", data: this.token, expire: "2H" });
      setCookie({
        name: "user_id",
        data: response?.data?.user?.id,
        expire: "7D",
      });
    },
    async logout() {
      try {
        const req = await useAxios("post", "/admin/logout");
        if (req.status) {
          this.removeUserToken();
          router.push({ name: "Login" });
        }
      } catch (error) {
        console.error("Logout failed:", error);
      }
    },

    async checkServerAuth() {
      let req = await useAxios("get", "/admin/verify-auth");
      if (!req.data.auth) {
        return false;
      } else {
        return true;
      }
    },

    removeUserToken() {
      this.token = null;
      this.user_id = null;
      this.user = null;
      this.role = null;
      removeCookie("__authToken");
      removeCookie("user_id");
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
