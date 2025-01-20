<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Breakdown Problem Note</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
       <v-row>
        <v-col col="12" md="6">
          <v-autocomplete
              v-model="breakDownProblems.company_id"
              :items="companies"
              item-value="id"
              item-title="name"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.company_id || ''"
              @update:search="fetchCompanies"
              no-filter
              >
              <template v-slot:label>
                  Select Company <span style="color: red">*</span>
              </template>
          </v-autocomplete>
        </v-col>
          <v-col col="12" md="6">
            <!-- Featured Checkbox -->
            <v-select
              v-model="breakDownProblems.status"
              :items="statusItems"
              label="Status"
              @change="updateStatus"
              clearable
            ></v-select>
          </v-col>
          
       </v-row>
        
        <!-- break_down_problem_note Field -->
        <v-textarea
          v-model="breakDownProblems.note"
          label="Note"
          :rules="[rules.required]"
          outlined
            density="comfortable"
          :error-messages="
            errors.note ? errors.note : ''
          "
        >
        <template v-slot:label>
            Note <span style="color: red">*</span>
        </template>
        </v-textarea>
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
              Create Note
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
import { toast } from "vue3-toastify";
export default {
  data() {
    return {
      valid: false,
      loading: false, // Controls loading state of the button
      statusItems: ["Active", "Inactive"],
      breakDownProblems: {
        company_id:null,
        note: "",
        status: "Active", // New property for checkbox
      },
      limit: 5,
      companies:[],
      selectedCompany: null,
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      rules: {
        required: (value) => !!value || "Required.",
      },
    };
  },
  methods: {
    async submit() {
      // Reset errors and loading state before submission
      this.errors = {};
      this.serverError = null;
      this.loading = true; // Start loading when submit is clicked

      const formData = new FormData();
      Object.entries(this.breakDownProblems).forEach(([key, value]) => {
        formData.append(key, value);
      });

      // Simulate a 3-second loading time (e.g., for an API call)
      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post(
            "/breakdown-problem-notes",
            formData
          );

          if (response.data.success) {
            toast.success("Breakdown Problem Note created successfully!");
            this.resetForm();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
            toast.error("Failed to create  Breakdown Problem Note.");
          } else {
            toast.error("An error occurred. Please try again.");
            // Handle other server errors
            this.serverError = "An error occurred. Please try again.";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000); // Simulates a 3-second loading duration
    },
    formatCompany(company) {
      if (company) {
          console.log(company)
          if (typeof company === "number") {
              // Use strict equality (===) and ensure proper assignment in the find function
              company = this.companies.find((item) => item.id === company);
          }
          // Safely return the company name or a fallback if the name is missing
          return company && company.name ? company.name : "No Company Name";
          }
          // Fallback if no company data is provided
          return "No Company Data";
    },
    async fetchCompanies(search) {
      try {
          // Make a GET request to the '/get_companies' endpoint with query parameters
          const response = await this.$axios.get('/get_companies', {
              params: {
              search: this.search || '', // Use `this.search` or fallback to an empty string
              limit: this.limit || 5,   // Use `this.limit` or fallback to default value (5)
              },
          });
          // Update the companies array with the fetched data
          this.companies = response.data;
          } catch (error) {
          // Log any errors that occur during the request
          console.error('Error fetching companies:', error);
          // Optionally, handle the error (e.g., show an error message to the user)
          this.$toast.error('Failed to fetch companies. Please try again later.');
          }

    },
    resetForm() {
      this.breakDownProblems = {
        company_id:'',
        name: "",
        break_down_problem_note: "",
        status: "", // Reset checkbox on form reset
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },
  },
};
</script>
