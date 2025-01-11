<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Breakdown Service</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <v-col cols="6">
            <v-autocomplete
              v-model="breakdown_service.mechine_assing_id"
              :items="machineItems"
              item-value="id"
              item-title="machine_code"
              label="Select Machine Code"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.mechine_assing_id"
              @update:search="fetchMachine"
            />
          </v-col>
          <v-col cols="6">
            <v-autocomplete
              v-model="breakdown_service.supervisor_problem_note_id"
              :items="BreakdownProblemNotes"
              item-value="id"
              item-title="note"
              label="Select Finding"
              outlined
              clearable
              multiple
              small-chips
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.supervisor_problem_note_id"
              @update:search="fetchBreakdownProblemNote"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-textarea
              v-model="breakdown_service.supervisor_note"
              label="Note"
              outlined
              density="comfortable"
              :error-messages="errors.supervisor_note"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="6">
            <v-date-input
              v-model="breakdown_service.service_date"
              label="Service Date"
              density="comfortable"
              :error-messages="errors.service_date"
            />
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="breakdown_service.service_time"
              label="Service Time"
              type="time"
              density="comfortable"
              :error-messages="errors.service_time"
            />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-select
              v-model="breakdown_service.service_status"
              :items="statusItems"
              label="Service Status"
              outlined
              clearable
              density="comfortable"
              :disabled="!breakdown_service.mechine_assing_id"
            />
          </v-col>
        </v-row>

        <!-- Action Buttons -->
        <v-row class="mt-4">
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
              Create Breakdown Service
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>

  <v-alert v-if="serverError" type="error" class="my-4">
    {{ serverError }}
  </v-alert>
</template>

<script>
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      valid: false,
      loading: false,
      statusItems: ["Pending", "Processing", "Done", "Cancel"],
      breakdown_service: {
        mechine_assing_id: null,
        service_date:this.getCurrentDate(), // Ensure it's in YYYY-MM-DD format
        service_time:this.getCurrentTime(), // Ensure it's in HH:MM format
        supervisor_problem_note_id: null,
        supervisor_note: "",
        service_status: "Pending",
      },
      errors: {},
      serverError: null,
      limit: 5,
      machineItems: [],
      BreakdownProblemNotes: [],
      rules: {
        required: (value) => !!value || "Required.",
      },
      visible: false,
      confirm_visible: false,
    };
  },
  methods: {
    // Fetch machine items
    async fetchMachine(search) {
      try {
        const response = await this.$axios.get("/search_machine", {
          params: { search: search || "" },
        });
        this.machineItems = response.data;
      } catch (error) {
        console.error("Error fetching machine codes:", error);
      }
    },

    // Fetch breakdown problem notes
    async fetchBreakdownProblemNote(search) {
      try {
        const response = await this.$axios.get("/get_breakdown_problem_notes", {
          params: {
            search: search || "",
            limit: this.limit,
          },
        });
        this.BreakdownProblemNotes = response.data;
      } catch (error) {
        console.error("Error fetching problem notes:", error);
      }
    },

    // Handle form submission
    async submit() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;

      // Validate service_date before submission
      const serviceDate = new Date(this.breakdown_service.service_date);
      
      // Check if serviceDate is a valid date
      if (!this.isValidDate(serviceDate)) {
        this.errors.service_date = "Invalid date format.";
        this.loading = false;
        return;
      }

      const formData = new FormData();
      Object.entries(this.breakdown_service).forEach(([key, value]) => {
        formData.append(key, value);
      });

      try {
        const response = await this.$axios.post("/breakdown-service", formData);
       console.log(response);
       
       
        if (response.data.success) {
          toast.success("Breakdown Service created successfully!");
          this.resetForm();
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.serverError = "An error occurred. Please try again.";
        }
      } finally {
        this.loading = false;
      }
    },

    // Reset form fields
    resetForm() {
      this.breakdown_service = {
        mechine_assing_id: null,
        service_date: this.getCurrentDate(),
        service_time: this.getCurrentTime(),
        supervisor_problem_note_id: null,
        supervisor_note: "",
        service_status: "Pending",
      };
      this.errors = {};
      if (this.$refs.form) {
        this.$refs.form.resetValidation(); // Ensure you're using a validation library like VeeValidate
      }
    },

    // Get current date in YYYY-MM-DD format
    getCurrentDate() {
      const currentDate = new Date();
      return currentDate.toISOString().split("T")[0]; // Format: YYYY-MM-DD
    },

    // Get current time in HH:MM format
    getCurrentTime() {
      const currentTime = new Date();
      return currentTime.toTimeString().split(" ")[0].slice(0, 5); // Format: HH:MM
    },

    // Helper method to validate if the date is valid
    isValidDate(date) {
      return date instanceof Date && !isNaN(date.getTime()); // Check if it's a valid Date object
    },
  },
  mounted() {
    this.getCurrentDate();
    this.getCurrentTime();
  },
};
</script>
