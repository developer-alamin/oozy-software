<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Create Breakdown Problem Note</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <!-- Name Field -->
        <!-- <v-text-field
          v-model="category.name"
          :rules="[rules.required]"
          label="Name"
          outlined
          :error-messages="errors.name ? errors.name : ''"
        >
          <template v-slot:label>
            Name <span style="color: red">*</span>
          </template>
        </v-text-field> -->

        <!-- break_down_problem_note Field -->
        <v-textarea
          v-model="category.break_down_problem_note"
          label="Note"
          :error-messages="
            errors.break_down_problem_note ? errors.break_down_problem_note : ''
          "
        />

        <!-- Featured Checkbox -->
        <!-- <v-checkbox
                    v-model="category.status"
                    label="Status"
                    :error-messages="errors.status ? errors.status : ''"
                >
                </v-checkbox> -->

        <v-select
          v-model="category.status"
          :items="statusItems"
          label="Status"
          @change="updateStatus"
          clearable
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
      category: {
        name: "",
        break_down_problem_note: "",
        status: "Active", // New property for checkbox
      },
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
      Object.entries(this.category).forEach(([key, value]) => {
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
    resetForm() {
      this.category = {
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
