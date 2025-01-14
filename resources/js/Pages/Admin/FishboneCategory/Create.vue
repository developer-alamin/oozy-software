<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Fishbone Category</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <!-- Problem Note Autocomplete -->
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="category.problem_note_id"
              :items="problemNotes"
              item-value="id"
              :item-title="formatProblemNote"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.problem_note_id || ''"
              @update:search="fetchProblemNotes"
            >
              <template v-slot:label>
                Select Problem Note <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>

          <!-- Status Dropdown -->
          <v-col cols="12" md="6">
            <v-select
              v-model="category.status"
              :items="statusItems"
              label="Status"
              outlined
              clearable
              density="comfortable"
            ></v-select>
          </v-col>
        </v-row>

        <!-- Category Name Textarea -->
        <v-textarea
          v-model="category.name"
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
            <v-btn type="button" color="secondary" @click="resetForm" class="mr-3">
              Reset Form
            </v-btn>
            <v-btn
              type="submit"
              color="primary"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Create Fishbone Category
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <!-- Server Error Alert -->
    <v-alert v-if="serverError" type="error" class="my-4">
      {{ serverError }}
    </v-alert>
  </v-card>
</template>

---

### Script with Correct API Integration
```javascript
<script>
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      search: "",
      valid: false,
      loading: false,
      statusItems: ["Active", "Inactive"], // Status dropdown options
      category: {
        problem_note_id: null, // Selected Problem Note ID
        name: "", // Name of the Fishbone Category
        status: "Active", // Default Status
      },
      problemNotes: [], // List of Problem Notes
      errors: {}, // Validation Errors
      serverError: null, // Server Error Message
      rules: {
        required: (value) => !!value || "Required.", // Validation Rule
      },
      limit: 5, // Pagination limit for Problem Notes
    };
  },
  methods: {
    // Submit the form data to create a category
    async submit() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;

      try {
        // API Request to create Fishbone Category
        const response = await this.$axios.post("fishbone-category", this.category);
        if (response.data.success) {
          toast.success("Fishbone Category created successfully!");
          this.resetForm();
        }
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          // Validation Errors
          this.errors = error.response.data.errors || {};
          toast.error("Validation failed. Please check the form.");
        } else {
          // General Error
          toast.error("An error occurred. Please try again.");
          this.serverError = "An error occurred. Please try again.";
        }
      } finally {
        this.loading = false;
      }
    },

    // Fetch Problem Notes for Autocomplete
    async fetchProblemNotes(search) {
      
      try {
        const response = await this.$axios.get("get_problemnotes", {
          params: { search: search , limit: this.limit },
        });
        this.problemNotes = response.data || [];

      } catch (error) {
        console.error("Error fetching problem notes:", error);
        toast.error("Failed to fetch problem notes. Please try again.");
      }
    },

    // Format Problem Note display in Autocomplete
    formatProblemNote(problemNote) {
      if (problemNote) {
        const companyName = problemNote.company?.name || "No Company Name";
        return `${problemNote.name || "No Name"} -- ${companyName}`;
      }
      return "No Problem Note Data";
    },

    // Reset the form to default state
    resetForm() {
      this.category = {
        problem_note_id: null,
        name: "",
        status: "Active",
      };
      this.errors = {};
      this.serverError = null;
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    },
  },
};
</script>
