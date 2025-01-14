<template>
    <v-card outlined class="mx-auto my-5">
      <v-card-title>Edit Cause</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid" @submit.prevent="update">
          <v-row>
            <!-- Select Problem Note -->
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="cause.problem_note_id"
                :items="problemnotes"
                item-value="id"
                :item-title="formatProblemNote"
                outlined
                clearable
                density="comfortable"
                :rules="[rules.required]"
                :error-messages="errors.problem_note_id || ''"
                @update:search="fetchProblemNotes"
                no-filter
              >
                <template v-slot:label>
                  Select Problem Note <span style="color: red">*</span>
                </template>
              </v-autocomplete>
            </v-col>
  
            <!-- Select Status -->
            <v-col cols="12" md="6">
              <v-select
                v-model="cause.status"
                :items="statusItems"
                label="Status"
                outlined
                clearable
                density="comfortable"
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
  
          <!-- Cause Buttons -->
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
                color="primary"
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
          problem_note_id: null,
          name: "",
          status: "Active",
        },
        problemnotes: [],
        errors: {},
        serverError: null,
        limit: 5,
        rules: {
          required: (value) => !!value || "Required.",
        },
      };
    },
    created() {
      this.fetchProblemNotes().then(() => {
        this.fetchCause();
      });
    },
    methods: {
      // Fetch Cause Details (using UUID from route params)
      async fetchCause() {
        const uuid = this.$route.params.uuid; // UUID from route params
        try {
          const response = await this.$axios.get(`/cause/${uuid}/edit`);
          this.cause = response.data.item; // Populate form data
        } catch (error) {
          console.error("Error fetching cause data:", error);
          this.serverError = "Failed to fetch cause data.";
        }
      },
  
      // Submit Form (Update Cause)
      async update() {
        this.errors = {};
        this.serverError = null;
        this.loading = true;
        const uuid = this.$route.params.uuid;
  
        try {
          const response = await this.$axios.put(`/cause/${uuid}`, this.cause);
          if (response.data.success) {
            toast.success("Cause updated successfully!");
            this.$router.push({ name: "FisboneCategoryIndex" }); // Redirect to cause index page
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors || {};
            toast.error("Validation errors occurred.");
          } else {
            console.error("Error updating cause:", error);
            this.serverError = "Failed to update cause.";
          }
        } finally {
          this.loading = false;
        }
      },
  
      // Fetch Problem Notes
      async fetchProblemNotes(search) {
        try {
          const response = await this.$axios.get("/get_problemnotes", {
            params: {
              search: search || "",
              limit: this.limit,
            },
          });
          this.problemnotes = response.data;
        } catch (error) {
          console.error("Error fetching problem notes:", error);
          toast.error("Failed to fetch problem notes.");
        }
      },
  
      // Format Problem Note Display
      formatProblemNote(problemnote) {
        if (problemnote) {
          if (typeof problemnote === "number") {
            problemnote = this.problemnotes.find((item) => item.id === problemnote);
          }
          if (problemnote) {
            const companyName = problemnote.company?.name || "No Company Name";
            return `${
              problemnote.name || "No Problem Note Name"
            } -- ${companyName}`;
          }
        }
        return "No ProblemNote Data";
      },
  
      // Reset Form
      resetForm() {
        this.errors = {};
        this.$refs.form.resetValidation();
        this.fetchCause(); // Re-fetch the cause to reset values
      },
    },
  };
  </script>
  
  <style scoped>
  /* Optional styles */
  </style>
  