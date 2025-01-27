<template>
    <v-card outlined class="mx-auto my-5">
      <v-card-title>Edit Cause</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid" @submit.prevent="updateCause">
          <v-row>
            <!-- Select Category -->
            <v-col cols="12">
              <v-autocomplete
                v-model="cause.fishbone_category_id"
                :items="categories"
                item-value="id"
                :item-title="formatCategory"
                outlined
                clearable
                density="comfortable"
                :rules="[rules.required]"
                :error-messages="errors.fishbone_category_id || ''"
                @update:search="fetchCategory"
              >
                <template v-slot:label>
                  Select Category <span style="color: red">*</span>
                </template>
              </v-autocomplete>
            </v-col>
  
            <!-- Select Status -->
            <v-col cols="12">
              <v-select
                v-model="cause.status"
                :items="statusItems"
                label="Cause Status"
                outlined
                clearable
                :rules="[rules.required]"
              >
                <template v-slot:label>
                  Status <span style="color: red">*</span>
                </template>
              </v-select>
            </v-col>
          </v-row>
  
          <!-- Cause Name -->
          <v-textarea
            v-model="cause.name"
            label="Cause Name"
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
                class="mr-3"
                @click="resetForm"
              >
                Reset Form
              </v-btn>
              <v-btn
                type="submit"
               class="primary-color"
                :disabled="!valid || loading"
                :loading="loading"
              >
                Update Cause
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
  
      <!-- Server Error Message -->
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
        statusItems: ["Active", "Inactive"],
        cause: {
          fishbone_category_id: null,  // Category ID for the cause
          name: "",
          status: "Active",  // Default status
        },
        categories: [],  // List of categories for autocomplete
        errors: {},
        serverError: null,
        limit: 5,
        rules: {
          required: (value) => !!value || "Required.",
        },
      };
    },
    created() {
      this.fetchCategory(); // Fetch categories when the page loads
      this.fetchCause();  // Fetch the cause data if editing an existing one
    },
    methods: {
      // Fetch Cause Details for editing
      async fetchCause() {
        const uuid = this.$route.params.uuid;  // Fetch the UUID from route params for editing
        try {
          const response = await this.$axios.get(`/cause/${uuid}/edit`);
          this.cause = response.data.item;  // Populate the form with existing cause data
        } catch (error) {
          console.error("Error fetching cause data:", error);
          this.serverError = "Failed to fetch cause data.";
        }
      },
  
      // Submit the updated form data for cause
      async updateCause() {
        this.errors = {};
        this.serverError = null;
        this.loading = true;
        const uuid = this.$route.params.uuid;  // Use the UUID for updating
  
        try {
          const response = await this.$axios.put(`/cause/${uuid}`, this.cause);  // Send PUT request to update cause
  
          if (response.data.success) {
            toast.success("Cause updated successfully!");
            this.$router.push({ name: "CausesIndex" });  // Redirect to the cause list page
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors || {};  // Capture validation errors
            toast.error("Validation errors occurred.");
          } else {
            console.error("Error updating cause:", error);
            this.serverError = "Failed to update cause.";
          }
        } finally {
          this.loading = false;
        }
      },
  
      // Fetch Categories for the autocomplete field
      async fetchCategory(search = "") {
        try {
          const response = await this.$axios.get("/get-fishbone-category", {
            params: { search: search, limit: this.limit },
          });
          this.categories = response.data.items;
        } catch (error) {
          console.error("Error fetching categories:", error);
          toast.error("Failed to fetch categories.");
        }
      },
  
      // Format Category Display for the autocomplete field
      formatCategory(category) {
        if (category) {
          const problemNoteName = category.problem_note?.name || "No Problem Note";
          const companyName = category.problem_note?.company?.name || "No Company Name";
          return `${category.name} -- ${problemNoteName} -- ${companyName}`;
        }
        return "No Category Data";
      },
  
      // Reset the form to its initial state
      resetForm() {
        this.fetchCause();  // Refetch the cause data to reset the form
        this.errors = {};  // Clear previous errors
        this.$refs.form.resetValidation();  // Reset form validation
      },
    },
  };
  </script>
  
  <style scoped>
  /* Optional styles for the form */
  </style>
  