<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Breakdown Service</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-autocomplete
          v-model="breakdown_service.breakdown_problem_note_id"
          :items="breakdown_problem_notes"
          item-value="id"
          item-title="break_down_problem_note"
          label="Select Machine Code"
          outlined
          clearable
          density="comfortable"
          :error-messages="
            errors.breakdown_problem_note_id
              ? errors.breakdown_problem_note_id
              : ''
          "
          @update:search="fetchBreakdownProblemNote"
        >
          <template v-slot:label> Select Breakdown Problem Note </template>
        </v-autocomplete>
        <v-textarea
          v-model="breakdown_service.breakdown_problem_note"
          label="Supervisor Note"
          :error-messages="
            errors.breakdown_problem_note ? errors.breakdown_problem_note : ''
          "
        />

        <v-textarea
          v-model="breakdown_service.breakdown_technician_problem_note"
          :rules="[rules.required]"
          :error-messages="errors.breakdown_technician_problem_note || ''"
        >
          <template v-slot:label>
            Technician Note <span style="color: red">*</span>
          </template>
        </v-textarea>
        <v-row>
          <v-col cols="6">
            <v-autocomplete
              v-model="breakdown_service.parts_id"
              :items="parts"
              item-value="id"
              item-title="name"
              label="Select Parts"
              outlined
              clearable
              density="comfortable"
              :error-messages="errors.parts_id ? errors.parts_id : ''"
              @update:search="fetchParts"
              @update:model-value="updatePartsUnit"
            >
              <template v-slot:label> Select Parts </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="breakdown_service.parts_quantity"
              label="Quantity"
              outlined
              density="comfortable"
              :error-messages="
                errors.parts_quantity ? errors.parts_quantity : ''
              "
            >
              <template v-slot:label> Quantity </template>
            </v-text-field>
            <b>Total Qty : {{ breakdown_service.parts_quantity_show }}</b>
          </v-col>
        </v-row>

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
              Service
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
      statusItems: ["Processing", "Done", "Cancel"],

      statusTechnicianItems: [
        "Pending",
        "Coming",
        "Service Running",
        "Success",
        "Failed",
      ],
      breakdown_service: {
        breakdown_problem_note_id: null,
        breakdown_problem_note: "",
        breakdown_service_status: null, // New property for checkbox
        breakdown_technician_problem_note: null,
        parts_id: null,
        parts_quantity: "",
        parts_quantity_show: "",
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      limit: 5,
      parts: [], // Array to store machine_codes data
      breakdown_problem_notes: [],
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
  created() {
    this.fetchItem();
    this.fetchBreakdownProblemNote();
    this.fetchParts();
  },
  methods: {
    async fetchItem() {
      // Fetch the brand data to populate the form
      const itemId = this.$route.params.uuid; // Assuming the brand ID is passed in the route params
      try {
        const response = await this.$axios.get(
          `/breakdown-service/${itemId}/processing`
        );
        // console.log(response.data);
        this.breakdown_service = response.data.breakdownService; // Populate form with the existing brand data
        this.breakdown_service.breakdown_service_status =
          this.breakdown_service.breakdown_service_status == "Processing"
            ? "Processing"
            : "";
        this.breakdown_service.breakdown_problem_note =
          this.breakdown_service.breakdown_problem_note == null
            ? ""
            : this.breakdown_service.breakdown_problem_note;
        // this.brand.type = this.brand.type === "Mechine" ? "Mechine" : "Parse";
      } catch (error) {
        this.serverError = "Error fetching Breakdown Service data.";
      }
    },
    async fetchParts(search = "") {
      try {
        const response = await this.$axios.get("/get_parts", {
          params: {
            search,
          },
        });
        this.parts = response.data; // Populate parts
      } catch (error) {
        console.error("Error fetching parts:", error);
      }
    },
    async fetchBreakdownProblemNote(search) {
      try {
        const response = await this.$axios.get(`/get_breakdown_problem_notes`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.breakdown_problem_notes = response.data;
      } catch (error) {
        console.error("Error fetching breakdown problem notes:", error);
      }
    },
    updatePartsUnit() {
      const selectedType = this.parts.find(
        (part) => part.id == this.breakdown_service.parts_id
      );
      // console.log("Selected Type:", selectedType); // Debugging log

      this.breakdown_service.parts_quantity_show = selectedType
        ? selectedType.quantity
        : "";
    },
    async submit() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;
      const serviceId = this.$route.params.uuid; // Assuming brand ID is in route params
      setTimeout(async () => {
        try {
          const response = await this.$axios.put(
            `/breakdown-service-processing/${serviceId}`,
            this.breakdown_service
          );

          if (response.data.success) {
            toast.success("Breakdown Service successfully!");
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
