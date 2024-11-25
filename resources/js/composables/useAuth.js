import { computed } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { appCookie, loadSiteBasicData } from "@/composables/useCookie";
import { useAxios } from "@/composables/useAxios";
import { useRouter, useRoute } from "vue-router";

// const toast = useToast();
const { getCookie, setCookie, removeCookie } = appCookie();

export function useAuth() {
  const router = useRouter();
  const route = useRoute();
  const store = useAuthStore();
  const { makeRequest } = useAxios();

  const setToken = (token, expire = "1D") => {
    store.setToken(token);
  };

  const getToken = () => {
    return getCookie("__token");
  };

  const isAuthenticated = computed(() => {
    if (store.getters.getToken) {
      isAuthenticatedDeep();
      return true;
    } else {
      return false;
    }
  });
  const isAuthenticatedDeep = async () => {
    if (store.getters.getToken) {
      let req = await useAxios("post", "auth/verify-auth");
      if (!req.data) {
        store.dispatch("setToken", null);
        removeCookie("user_id");
        toast.error("Your Session Expired. Please login again");
        // router.push({ name: "Home" })
      }
    }
  };
  const login = async (userData) => {
    let authReq = await useAxios("post", "auth/login", userData);
    if (authReq.status == 200) {
      store.dispatch("setToken", authReq.data.access_token);
      toast.success("Login Successful");
      setCookie({
        name: "user_id",
        data: authReq?.data?.user?.id,
        expire: "7D",
      });
      removeCookie("temp_user_id");
      store.dispatch("setUser", authReq.data.user);
      router.push({ name: "AuthDashboard" });
    } else {
      toast.error(authReq?.data?.message);
    }
  };
  const logOut = async (cb = () => {}) => {
    let req = await useAxios("get", "auth/logout", {}, true);
    let token_remove;
    if (req.status == 200) {
      token_remove = store.dispatch("setToken", null);
      removeCookie("user_id");
      store.dispatch("setUser", {});
      toast.success("Logout Successful");
    } else {
      token_remove = false;
      toast.success("Logout Failed");
    }
    cb(token_remove);
  };

  const syncAuthToken = (state) => {
    state.watch(
      (state) => state.token,
      (token) => {
        setCookie({
          name: "__token",
          data: token,
          expore: "7D",
        });
      }
    );
  };
  return {
    setToken,
    getToken,
    syncAuthToken,
    logOut,
    login,
    isAuthenticated,
    isAuthenticatedDeep,
  };
}
