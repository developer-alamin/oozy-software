import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Load jQuery first
// import "../assets/js/vendors/jquery-3.6.0.min.js";
// window.jQuery = window.$ = require("jquery"); // Set jQuery to the global scope

// // Load other libraries and scripts after jQuery
import "../assets/vendor/apexcharts/apexcharts.min.js";
import "../assets/vendor/bootstrap/js/bootstrap.bundle.min.js";
import "../assets/vendor/chart.js/chart.umd.js";
import "../assets/vendor/echarts/echarts.min.js";
import "../assets/vendor/quill/quill.js";
import "../assets/vendor/simple-datatables/simple-datatables.js";
import "../assets/vendor/tinymce/tinymce.min.js";
import "../assets/vendor/php-email-form/validate.js";
import "../assets/js/main.js";
