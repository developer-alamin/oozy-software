<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Add New Service</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <!-- Machine Code Dropdown -->
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
          </v-col>


        </v-row>

<<<<<<< Updated upstream
=======
        <!-- Line Dropdown -->
        <v-row>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="breakdown_service.line_id"
              :items="lines"
              item-value="id"
              item-title="name"
              label="Select Line"
              outlined
              clearable
              density="comfortable"
              :disabled="!breakdown_service.machine_id"
            >
              <template v-slot:label>
                Select Line <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="breakdown_service.breakdown_problem_note_id"
              :items="breakdown_problem_notes"
              item-value="id"
              item-title="note"
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
          </v-col>
        </v-row>
>>>>>>> Stashed changes
       
        <v-textarea
          v-model="breakdown_service.breakdown_problem_note"
          label="Note"
          :error-messages="
            errors.breakdown_problem_note ? errors.breakdown_problem_note : ''
          "
        />
        <v-row>
          <v-col cols="6">
            <v-date-input
              v-model="breakdown_service.service_date"
              label="Service Date"
              density="comfortable"
              :error-messages="errors.service_date ? errors.service_date : ''"
              readonly
            />
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="breakdown_service.service_time"
              label="Service Time"
              type="time"
              density="comfortable"
              :error-messages="errors.service_time ? errors.service_time : ''"
              readonly
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-select
              v-model="breakdown_service.breakdown_service_status"
              :items="statusItems"
              label="Breakdown Service Status"
              clearable
              density="comfortable"
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
              Create Service
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
      loading: false, // Controls loading state of the button
      statusItems: ["Pending", "Processing", "Done", "Cancel"],
      statusTypeItems: ["Preventive", "Breakdown"],
      statusTechnicianItems: [
        "Pending",
        "Coming",
        "Service Running",
        "Success",
        "Failed",
      ],
      statusLocation: ["Sewing Line"],
      breakdown_service: {
        service_time: this.getCurrentTime(),
        service_date: this.getCurrentDate(),
        mechine_assing_id: null,
        breakdown_problem_note_id: null,
        breakdown_problem_note: null,
        breakdown_service_status: "Pending",
        description: "",
        status: "Pending",
        service_type_status: "Preventive",
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      limit: 5,
      machineItems: [],
      breakdown_problem_notes: [],
      lines: [],
      selectedCompany: null, // Bound to selected Company in v-autocomplete

      rules: {
        required: (value) => !!value || "Required.",
      },
      visible: false,
      confirm_visible: false,
    };
  },

  methods: {
    async fetchMachine(search = "") {
      try {
        const response = await this.$axios.get("/search_machine", {
          params: {
            search,
          },
        });
        this.machineItems = response.data;
      } catch (error) {
        console.error("Error fetching machine codes:", error);
      }
    },

    async submit() {
      // Reset errors and loading state before submission
      this.errors = {};
      this.serverError = null;
      this.loading = true; // Start loading when submit is clicked

      const formData = new FormData();
      Object.entries(this.breakdown_service).forEach(([key, value]) => {
        formData.append(key, value);
      });

      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post(
            "/breakdown-service",
            formData
          );

          console.log(response);
          

          if (response.data.success) {
            toast.success("breakdown service create successfully!");
            // console.log(response.data.service.id);
            // this.$router.push({
            //   name: "ServiceHistoryCreate",
            //   params: { id: response.data.service.id }, // Pass the ID here
            // });
            this.resetForm();
          }
        } catch (error) {
          console.log(error);
          
          if (error.response && error.response.status === 422) {
            toast.error("Failed to create breakdown service.");
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Failed to create breakdown service.");
            // Handle other server errors
            this.serverError = "An error occurred. Please try again.";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000); // Simulates a 3-second loading duration
    },

    addParse() {
      this.breakdown_service.parses.push({ parse_id: null, quantity: 1 });
    },
    removeParse(index) {
      this.breakdown_service.parses.splice(index, 1);
    },
    resetForm() {
      this.breakdown_service = {
        company_id: "",
        name: "",
        email: "",
        phone: "",
        parses: [{ parse_id: null, quantity: 1 }],
        factory_code: "",
        location: "",
        status: "Preventive", // New property for checkbox
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },

    async fetchCompanys(search) {
      try {
        const response = await this.$axios.get(`/get_companys`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.companys = response.data;
      } catch (error) {
        console.error("Error fetching companys:", error);
      }
    },

    async fetchOperator(search) {
      try {
        const response = await this.$axios.get(`/get_operators`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.operators = response.data;
      } catch (error) {
        console.error("Error fetching operators:", error);
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
        console.log(response.data);
        this.breakdown_problem_notes = response.data;
      } catch (error) {
        console.error("Error fetching breakdown problem notes:", error);
      }
    },
    async fetchParses(search) {
      try {
        const response = await this.$axios.get(`/get_parses`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.parseOptions = response.data;
      } catch (error) {
        console.error("Error fetching parses:", error);
      }
    },
    async fetchGroups(search) {
      try {
        const response = await this.$axios.get(`/get_sources`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.sources = response.data;
      } catch (error) {
        console.error("Error fetching sources:", error);
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
