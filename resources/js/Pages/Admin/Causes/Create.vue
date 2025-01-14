<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Cause</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
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
          <v-col cols="12">
            <v-select
              v-model="cause.status"
              :items="statusItems"
              label="Status"
              clearable
            ></v-select>
          </v-col>
        </v-row>

        <v-textarea
          v-model="cause.name"
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
              Create Cause
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
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
      search: "",
      valid: false,
      loading: false,
      statusItems: ["Active", "Inactive"],
      cause: {
        fishbone_category_id: null,  // Updated to category_id
        name: "", 
        status: "Active",
      },
      categories: [],  // Updated to categories
      errors: {},
      serverError: null,
      rules: {
        required: (value) => !!value || "Required.",
      },
      limit: 5,
    };
  },
  methods: {
    async submit() {
      this.errors = {}; // Reset errors
      this.serverError = null; // Reset server error
      this.loading = true; // Set loading to true

      const formData = new FormData();
      Object.entries(this.cause).forEach(([key, value]) => {
        formData.append(key, value); // Append the cause data to formData
      });

      try {
        // Make the API request to the causes resource
        const response = await this.$axios.post("/cause", formData); // Ensure the correct URL
        console.log(response.data);
        
        // Check the response data for success
        if (response.data.success) {
          toast.success("Cause created successfully!");
          this.resetForm(); // Reset the form after success
        } else {
          toast.error("Failed to create cause.");
        }
      } catch (error) {
        // Handle validation errors (422)
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}; // Capture validation errors
          toast.error("Failed to create cause.");
        } else {
          // Handle other errors (e.g., server issues, network)
          toast.error("An error occurred. Please try again.");
          this.serverError = "An error occurred. Please try again.";
        }
      } finally {
        this.loading = false; // Set loading to false once the request is complete
      }
    },

    async fetchCategory(search) {
      try {
        const response = await this.$axios.get("/get-fishbone-category", {  // Updated API endpoint to get_categories
          params: { search: this.search || "", limit: this.limit },
        });
        this.categories = response.data.items || [];
      } catch (error) {
        console.error("Error fetching categories:", error);
        toast.error("Failed to fetch categories. Please try again later.");
      }
    },
    formatCategory(category) {  // Updated function to handle category
      if (category) {
        const problemnoteName = category.problem_note?.name || "No Problem Note Name";
        const companyName = category.problem_note?.company?.name || "No Company Name";
        this.cause.fishbone_category_id = category.id;  // Updated to set category_id
        return `${
          category.name || "No Category Name"
        } -- ${problemnoteName} -- ${companyName}`;
      }
      return "No Category Data";
    },
    resetForm() {
      this.cause = {
        fishbone_category_id: null,  // Reset category_id
        name: "",  // Reset the name field
        status: "Active",
      };
      this.errors = {};
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    },
  },
};
</script>
