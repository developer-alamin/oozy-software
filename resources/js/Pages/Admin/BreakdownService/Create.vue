<template>
  <v-card outlined class="mx-auto my-5" max-width="">
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
              :error-messages="errors.mechine_assing_id ? errors.mechine_assing_id : ''"
              @update:search="fetchMachine"
            >
              <template v-slot:label>
                Select Machine Code <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="6">
              <v-autocomplete
                v-model="breakdown_service.supervisor_problem_note_id"
                :items="BreakdownProblemNotes"
                item-value="id"
                item-title="note"
                label="Select Problem Notes"
                outlined
                clearable
                multiple
                small-chips
                density="comfortable"
            :rules="[rules.required]"
                :error-messages="errors.supervisor_problem_note_id ? errors.supervisor_problem_note_id : ''"
                @update:search="fetchBreakdownProblemNote"
              >
                <template v-slot:label> Select Problem Notes <span style="color: red">*</span> </template>
              </v-autocomplete>
            </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-textarea
              v-model="breakdown_service.supervisor_note"
              :error-messages="errors.note || ''"
            >
                <template v-slot:label>
                 Note
                </template>
            </v-textarea>
          </v-col>
        </v-row>

         <v-row>
          <v-col cols="6">
            <v-date-input
              v-model="breakdown_service.service_date"
              label="Service Date"
              density="comfortable"
              :error-messages="errors.service_date ? errors.service_date : ''"
            />
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="breakdown_service.service_time"
              label="Service Time"
              type="time"
              density="comfortable"
              :error-messages="errors.service_time ? errors.service_time : ''"
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
	            ></v-select>
          </v-col>
        </v-row>


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
              Create Breakdown Service
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

export default {
  data() {
    return {
      valid: false,
      loading: false,
      statusItems: ["Pending", "Processing", "Done", "Cancel"],
      breakdown_service: {
        mechine_assing_id: null,
        service_date: this.getCurrentDate(),
        service_time: this.getCurrentTime(),
        supervisor_problem_note_id: null,
        supervisor_note: null,
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
    async fetchMachine(search) {
      try {
        const response = await this.$axios.get("/search_machine", {
          params: {
           search: this.search || '',
          },
        });
        
        this.machineItems = response.data;
      } catch (error) {
        console.error("Error fetching machine codes:", error);
      }
    },

    async fetchBreakdownProblemNote(search) {
        try {
          const response = await this.$axios.get(`/get_breakdown_problem_notes`, {
            params: {
              search: this.search || '',
              limit: this.limit,
            },
          });

          this.BreakdownProblemNotes = response.data;
        } catch (error) {
          console.error("Error fetching problem notes:", error);
        }
      },

    async submit() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;

      const formData = new FormData();
      Object.entries(this.breakdown_service).forEach(([key, value]) => {
        formData.append(key, value);
      });
       
      setTimeout(async () => {
        try {
          const response = await this.$axios.post(
            "/breakdown-service",
            formData
          );
          if (response.data.success) {
            toast.success("Breakdown Service create successfully!");
            this.resetForm();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to create Breakdown Service.");
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Failed to create Breakdown Service.");
            // Handle other server errors
            this.serverError = "An error occurred. Please try again.";
          }
        } finally {
          this.loading = false;
        }
      }, 1000);
    },
    resetForm() {
      this.breakdown_service = {
        mechine_assing_id: "",
        status: "Pending",
      };
      this.errors = {};
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    },

    getCurrentDate() {
      const currentDate = new Date();
      return currentDate.toISOString().split("T")[0]; // Format YYYY-MM-DD
    },
    getCurrentTime() {
      const currentTime = new Date();
      return currentTime.toTimeString().split(" ")[0].slice(0, 5); // Format HH:MM
    },

  },
  mounted() {
    this.breakdown_service.service_date = this.getCurrentDate();
    this.breakdown_service.service_time = this.getCurrentTime();
    this.fetchMachine();
  },
};
</script>
