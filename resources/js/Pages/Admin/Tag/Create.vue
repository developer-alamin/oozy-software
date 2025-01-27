<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Tag</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <!-- Tag Name -->
        <v-text-field
          v-model="tag.name"
          :rules="[rules.required]"
          label="Tag Name"
          outlined
          density="comfortable"
          :error-messages="errors.name ? errors.name : ''"
        >
          <template v-slot:label>
            Tag Name <span style="color: red">*</span>
          </template>
        </v-text-field>

        <v-row>
          <!-- Company Selection -->
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="tag.company_id"
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
              v-model="tag.status"
              :items="statusItems"
              label="Status"
              outlined
              clearable
            ></v-select>
          </v-col>
        </v-row>

        <!-- Description -->
        <v-textarea
          v-model="tag.note"
          label="Description"
          outlined
        ></v-textarea>

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
             class="primary-color"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Create Tag
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      valid: false,
      loading: false, // Loading state for submit button
      statusItems: ["Active", "Inactive"], // Status dropdown options
      tag: {
        name: "",
        note: "",
        status: "Active", // Default status
        company_id: null, // Default company
      },
      companies: [], // Company options
      limit: 5, // Number of companies fetched
      errors: {}, // Validation errors
      rules: {
        required: (value) => !!value || "Required.", // Basic required rule
      },
    };
  },
  methods: {
    async submit() {
      this.errors = {}; // Reset errors
      this.loading = true; // Set loading state

      // Manually validate the form before proceeding
      const isValid = await this.$refs.form.validate(); // Use form validation
      if (!isValid) {
        this.loading = false;
        return; // Stop if validation fails
      }

      const formData = new FormData();
      Object.entries(this.tag).forEach(([key, value]) => {
        formData.append(key, value);
      });

      try {
        // API call to submit the form data
        const response = await this.$axios.post("/machine-tag", formData);

        if (response.data && response.data.success) {
          this.resetForm();
          toast.success("Tag created successfully!");
        } else {
          toast.error("Failed to create tag. Please try again.");
        }
      } catch (error) {
        // Handle validation and other server errors
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          console.error("Error creating tag:", error);
          toast.error("An unexpected error occurred. Please try again.");
        }
      } finally {
        this.loading = false; // Reset loading state
      }
    },

    async fetchCompanies(search) {
      try {
        // API call to fetch companies based on search term
        const response = await this.$axios.get("/get_companies", {
          params: { search: search || "", limit: this.limit },
        });
        this.companies = response.data || [];
      } catch (error) {
        console.error("Error fetching companies:", error);
        toast.error("Failed to fetch companies. Please try again.");
      }
    },

    resetForm() {
      // Reset form fields and validation
      this.tag = {
        name: "",
        note: "",
        status: "Active",
        company_id: null,
      };
      this.errors = {}; // Clear errors
      this.valid = false; // Reset the valid state explicitly
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset form itself (clear all inputs)
        this.$refs.form.resetValidation(); // Reset validation
      }
    },
  },
};
</script>
