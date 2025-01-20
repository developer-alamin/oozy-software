<template>
    <v-card outlined class="mx-auto my-5">
      <v-card-title>Edit Tag</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid" @submit.prevent="update">
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
                color="primary"
                :disabled="!valid || loading"
                :loading="loading"
              >
                Update Tag
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
        tag: {
          name: "",
          note: "",
          status: "", // Default status
          company_id: null, // Default company
        },
        companies: [], // Company options
        limit: 5, // Number of companies fetched
        errors: {}, // Validation errors
        serverError: null, // Server error message
        rules: {
          required: (value) => !!value || "Required.", // Basic required rule
        },
      };
    },
    created() {
      this.fetchCompanies().then(() => {
        this.fetchTag();
      });
    },
    methods: {
      async fetchTag() {
        // Fetch the tag data to populate the form
        const tagId = this.$route.params.uuid; // Assuming the tag ID is passed in the route params
        try {
          const response = await this.$axios.get(`/machine-tag/${tagId}/edit`);
        
          this.tag = response.data.machineTag; // Populate form with the existing tag data
          this.tag.status =
            this.tag.status === "Active" ? "Active" : "Inactive";
        } catch (error) {
          this.serverError = "Error fetching tag data.";
        }
      },
      async update() {
        this.errors = {}; // Reset errors before submission
        this.serverError = null;
        this.loading = true;
        const tagId = this.$route.params.uuid; // Assuming tag ID is in route params
  
        setTimeout(async () => {
          try {
            const response = await this.$axios.put(
              `/machine-tag/${tagId}`,
              this.tag
            );
            if (response.data.success) {
              toast.success("Tag updated successfully!");
              this.$router.push({ name: "TagIndex" }); // Redirect to tags list page
            }
          } catch (error) {
            if (error.response && error.response.status === 422) {
              toast.error("Failed to update tag.");
              this.errors = error.response.data.errors || {};
            } else {
              toast.error("Error updating tag. Please try again.");
              this.serverError = "Error updating tag.";
            }
          } finally {
            this.loading = false; // Stop loading after the request
          }
        }, 1000);
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
        this.fetchTag(); // Reset the form with existing tag data
        this.errors = {}; // Clear errors
        this.$refs.form.resetValidation(); // Reset form validation
      },
    },
  };
  </script>
  