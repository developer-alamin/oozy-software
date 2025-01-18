<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create technician</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <div class="row">
          <v-col cols="6">
            <v-autocomplete
              v-model="technician.company_id"
              :items="companies"
              item-value="id"
              item-title="name"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.company_id ? errors.company_id : ''"
              @update:model-value="onCompanyChange"
              @update:search="fetchCompanies"
            >
              <template v-slot:label>
                Select Company <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="6">
            <v-autocomplete
              v-model="technician.factory_id"
              :items="factories"
              item-value="id"
              item-title="name"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.factory_id ? errors.factory_id : ''"
              @update:model-value="fetchFactories"
              @update:search="fetchFactories"
            >
              <template v-slot:label>
                Select Factory <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
        </div>
        <v-row>
          <v-col cols="6">
            <v-autocomplete
              v-model="technician.group_id"
              :items="groups"
              item-value="id"
              item-title="name"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.group_id ? errors.group_id : ''"
              @update:search="fetchGroups"
            >
              <template v-slot:label>
                Select Group <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="6">
            <!-- Name Field -->
            <v-text-field
              v-model="technician.name"
              :rules="[rules.required]"
              label="Name"
              outlined
              :error-messages="errors.name ? errors.name : ''"
            >
              <template v-slot:label>
                Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
        </v-row>

        <v-select
          v-model="technician.type"
          :items="typeItems"
          :rules="[rules.required]"
          label="Technician Type"
          clearable
        >
          <template v-slot:label>
            Technician Type <span style="color: red">*</span>
          </template>
        </v-select>
        <!-- Name Field -->
        <v-text-field
          v-model="technician.email"
          label="Email"
          outlined
          :error-messages="errors.email ? errors.email : ''"
        >
          <template v-slot:label> Email </template>
        </v-text-field>

        <v-text-field
          v-model="technician.phone"
          label="Phone"
          outlined
          :error-messages="errors.phone ? errors.phone : ''"
        >
          <template v-slot:label> Phone </template>
        </v-text-field>

        <!-- Description Field -->
        <v-textarea
          v-model="technician.address"
          label="Address"
          :error-messages="errors.description ? errors.address : ''"
        />
        <!-- Description Field -->
        <v-textarea
          v-model="technician.description"
          label="Description"
          :error-messages="errors.description ? errors.description : ''"
        />

        <v-select
          v-model="technician.status"
          :items="statusItems"
          label="Technician Status"
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
              Create technician
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
      statusItems: ["Available", "Not Available"],
      typeItems: ["General", "Special", "Manager"],
      technician: {
        company_id: null,
        factory_id: null,
        group_id: null,
        name: "",
        type: "General",
        email: "",
        phone: "",
        photo: "",
        address: "",
        description: "",
        status: "Not Available", // New property for checkbox
      },
      companies: [],
      factories: [],
      groups: [],
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
      Object.entries(this.technician).forEach(([key, value]) => {
        formData.append(key, value);
      });

      // Simulate a 3-second loading time (e.g., for an API call)
      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post("/technician", formData);

          if (response.data.success) {
            toast.success("Technician create successfully!");
            this.resetForm();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to create technician.");
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Failed to create technician.");
            // Handle other server errors
            this.serverError = "An error occurred. Please try again.";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000); // Simulates a 3-second loading duration
    },
    async fetchCompanies(search) {
      try {
        const response = await this.$axios.get(`/get_companies`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.companies = response.data;
      } catch (error) {
        console.error("Error fetching companies:", error);
      }
    },
    async fetchFactories(search = "") {
      if (!this.technician.company_id) {
        this.factories = []; // Clear factories if no company is selected
        return;
      }
      try {
        const response = await this.$axios.get(`/get_company_ways_factories`, {
          params: {
            search,
            company_id: this.technician.company_id,
            limit: this.limit,
          },
        });
        this.factories = response.data;
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },

    async onCompanyChange(companyId) {
      this.technician.factory_id = null; // Reset selected factory
      this.factories = []; // Clear factories
      if (!companyId) return; // Exit if no company is selected
      this.fetchFactories();
    },
    async fetchGroups(search = "") {
      try {
        const response = await this.$axios.get(`/get_groups`, {
          params: {
            search,
            limit: this.limit,
          },
        });
        this.groups = response.data;
      } catch (error) {
        console.error("Error fetching groups:", error);
      }
    },
    resetForm() {
      this.technician = {
        name: "",
        email: "",
        phone: "",
        photo: "",
        address: "",
        description: "",
        status: "Not Available", // New property for checkbox
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },
  },
};
</script>
