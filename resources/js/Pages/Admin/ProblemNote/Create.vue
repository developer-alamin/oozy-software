<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create ProblemNote</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <!-- Company Selection -->
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="problemNote.company_id"
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
          <!-- Status Selection -->
          <v-col cols="12" md="6">
            <v-select
              v-model="problemNote.status"
              :items="statusItems"
              label="Status"
              outlined
              clearable
              density="comfortable"
            ></v-select>
          </v-col>
        </v-row>

        <!-- Name Input -->
        <v-textarea
          v-model="problemNote.name"
          label="Name"
          :rules="[rules.required]"
          outlined
          density="comfortable"
          :error-messages="errors.name || ''"
        >
          <template v-slot:label>
            Name <span style="color: red">*</span>
          </template>
        </v-textarea>

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
              Create ProblemNote
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>

    <!-- Error Alert -->
    <v-alert v-if="serverError" type="error" class="my-4">
      {{ serverError }}
    </v-alert>
  </v-card>
</template>

<script>
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      valid: false,
      loading: false,
      statusItems: ["Active", "Inactive"], // Available status options
      problemNote: {
        company_id: null,
        name: "", // The name of the problem note
        status: "Active", // Default status is 'Active'
      },
      companies: [], // List of companies fetched from the API
      errors: {}, // To store validation errors
      serverError: null, // To store server-side error messages
      rules: {
        required: (value) => !!value || "Required.", // Validation rule for required fields
      },
      limit: 5, // Limit for fetching companies
    };
  },
  methods: {
    // Method to submit the form
    async submit() {
      this.errors = {}; // Reset any existing errors
      this.serverError = null; // Reset server-side error message
      this.loading = true; // Set loading state

      const formData = new FormData();
      // Append problemNote data to formData
      Object.entries(this.problemNote).forEach(([key, value]) => {
        formData.append(key, value);
      });

      try {
        // Make an API call to create the ProblemNote
        const response = await this.$axios.post("/problemnote", formData);
        if (response.data.success) {
          toast.success("ProblemNote created successfully!"); // Show success message
          this.resetForm(); // Reset the form after successful creation
        }
      } catch (error) {
        // Handle validation errors (422) and other errors
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}; // Populate errors object
          toast.error("Failed to create ProblemNote.");
        } else {
          toast.error("An error occurred. Please try again.");
          this.serverError = "An error occurred. Please try again."; // Display error message
        }
      } finally {
        this.loading = false; // Reset loading state
      }
    },
    
    // Method to fetch companies based on search input
    async fetchCompanies(search) {
      try {
        const response = await this.$axios.get("/get_companies", {
          params: { search: search || "", limit: this.limit },
        });
        this.companies = response.data; // Update companies list with the response
      } catch (error) {
        console.error("Error fetching companies:", error);
        toast.error("Failed to fetch companies. Please try again later.");
      }
    },

    // Method to reset the form
    resetForm() {
      this.problemNote = {
        company_id: null,
        name: "", // Reset name field
        status: "Active", // Reset status to default 'Active'
      };
      this.errors = {}; // Reset any errors
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form
      }
    },
  },
};
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
