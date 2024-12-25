<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Start Breakdown Service</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-autocomplete
          v-model="breakdown_service.machine_id"
          :items="machine_codes"
          item-value="id"
          item-title="machine_code"
          label="Select Machine Code"
          outlined
          clearable
          density="comfortable"
          :rules="[rules.required]"
          :error-messages="errors.machine_id ? errors.machine_id : ''"
          @update:search="fetchMachineCodes"
        >
          <template v-slot:label>
            Select Machine Code <span style="color: red">*</span>
          </template>
        </v-autocomplete>
        <v-select
          v-model="breakdown_service.breakdown_service_status"
          :items="statusItems"
          label="Service Status"
          clearable
          density="comfortable"
        ></v-select>

        <!-- Action Buttons -->
        <v-row class="mt-4">
          <!-- Submit Button -->

          <!-- Reset Button -->
          <v-col cols="12" class="text-right">
            <v-btn
              type="button"
              color="secondary"
              @click="resetForm"
              class="mr-3"
            >
              Reset Form
            </v-btn>

            <v-btn
              type="submit"
              color="primary"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Start Service
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>

  <!-- Server Error Message -->
  <v-alert v-if="serverError" type="error" class="my-4">
    {{ serverError }}
  </v-alert>
</template>
<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";
// import VDateInput from "../../Components/VDateInput.vue";

export default {
  // components: {
  //     VDateInput,
  // },
  data() {
    return {
      valid: false,
      loading: false, // Controls loading state of the button
      statusItems: ["Processing"],

      statusTechnicianItems: [
        "Pending",
        "Coming",
        "Service Running",
        "Success",
        "Failed",
      ],

      breakdown_service: {
        machine_id: null,
        breakdown_service_status: "Processing", // New property for checkbox
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      limit: 5,
      machine_codes: [], // Array to store machine_codes data
      selectedCompany: null, // Bound to selected Company in v-autocomplete

      rules: {
        required: (value) => !!value || "Required.",
        email: (value) => /.+@.+\..+/.test(value) || "E-mail must be valid.",
        confirm_password: (value) =>
          value === this.company.password || "Passwords must match.", // Confirms password matches
        phone: (value) =>
          /^\d{11}$/.test(value) || "Phone number must be valid.",
      },
      visible: false,
      confirm_visible: false,
    };
  },
  computed: {
    // Function to limit the allowed dates within the min and max date range
    allowedDates() {
      return (date) => {
        return date >= new Date();
      };
    },
  },
  created() {},
  methods: {
    async fetchMachineCodes(search = "") {
      try {
        const response = await this.$axios.get("/get_machine_codes", {
          params: {
            search,
          },
        });
        this.machine_codes = response.data; // Populate machine codes
      } catch (error) {
        console.error("Error fetching machine codes:", error);
      }
    },

    async submit() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;
      const serviceId = this.$route.params.uuid; // Assuming brand ID is in route params
      setTimeout(async () => {
        try {
          const response = await this.$axios.put(
            `/breakdown-service/${serviceId}`,
            this.breakdown_service
          );

          if (response.data.success) {
            toast.success("Breakdown Service Start successfully!");
            this.$router.push({ name: "ServiceIndex" }); // Redirect to brand list page
          }
        } catch (error) {
          // console.log(error.response.data);
          if (error.response.data.success == false) {
            toast.error(
              "Invalid Machine Code. Please provide a valid machine!"
            );
          }
          if (error.response && error.response.status === 422) {
            toast.error("Failed to start Breakdown Service .");
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Error start Breakdown Service . Please try again.");
            this.serverError = "Error start Breakdown Service .";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000);
    },

    async submitOnd() {
      // Reset errors and loading state before submission
      this.errors = {};
      this.serverError = null;
      this.loading = true; // Start loading when submit is clicked

      const formData = new FormData();
      Object.entries(this.breakdown_service).forEach(([key, value]) => {
        formData.append(key, value);
      });
      // const formData = new FormData();
      // Object.entries(this.factory).forEach(([key, value]) => {
      //     if (Array.isArray(value)) {
      //         value.forEach((val) => formData.append(`${key}[]`, val));
      //     } else {
      //         formData.append(key, value);
      //     }
      // });
      // Simulate a 3-second loading time (e.g., for an API call)
      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post("/mechine-assing", formData);
          console.log(response.data);

          if (response.data.success) {
            toast.success("mechine assing create successfully!");
            // localStorage.setItem("token", response.data.token);
            this.resetForm();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to create mechine assing.");
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Failed to create mechine assing.");
            // Handle other server errors
            this.serverError = "An error occurred. Please try again.";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000); // Simulates a 3-second loading duration
    },
    resetForm() {
      this.breakdown_service = {
        company_id: "",
        name: "",
        email: "",
        phone: "",
        factory_code: "",
        location: "",
        status: "Preventive", // New property for checkbox
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },
  },
};
</script>
