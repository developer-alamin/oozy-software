import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";
import { createPinia } from "pinia";
import router from "./router/router.js";
import { VDateInput } from "vuetify/labs/VDateInput";
// import router from "./router";
import axios from "./axiosInstance"; // Import your Axios instance
// import "bootstrap/dist/css/bootstrap.min.css"; // Import Bootstrap CSS
// import "bootstrap"; // Import Bootstrap JS

// Vuetify Imports
import "vuetify/styles"; // Ensure Vuetify styles are imported
import { createVuetify } from "vuetify";
import { aliases, mdi } from "vuetify/iconsets/mdi";
import "@mdi/font/css/materialdesignicons.css";
import "vue3-toastify/dist/index.css";
import { toast } from "vue3-toastify";
import VueApexCharts from "vue3-apexcharts";



// Import fonts and any global custom styles
// import "@/assets/styles/global.css"; // (If you have any custom global styles)

// Create Vuetify instance with custom theme (optional)
const vuetify = createVuetify({
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
    theme: {
        themes: {
            light: {
                colors: {
                    primary: "#1976D2",
                    secondary: "#424242",
                },
            },
        },
    },
    components: {
        VDateInput, // Register VDateInput component
    },
});

const app = createApp(App);

app.use(createPinia());
app.use(router);
// Use Vuetify
app.use(vuetify);
app.use(VueApexCharts);
app.config.globalProperties.$toast = toast;

app.config.globalProperties.$axios = axios; // Make Axios instance globally accessible
// app.component("apexchart", VueApexCharts);

app.mount("#app");
