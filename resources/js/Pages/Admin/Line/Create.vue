<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Create Line</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-autocomplete
          v-model="line.unit_id"
          :items="units"
          item-value="id"
          :item-title="formatUnit"
          outlined
          clearable
          density="comfortable"
          :rules="[rules.required]"
          :error-messages="errors.unit_id ? errors.unit_id : ''"
          @update:search="fetchUnits"
        >
          <template v-slot:label>
            Select Unit <span style="color: red">*</span>
          </template>
        </v-autocomplete>

        <!-- Display factory name -->
        <!-- <div v-if="selectedFloorName" style="margin-top: 10px">
                    <strong>Floor Name:</strong> {{ selectedFloorName }}
                </div> -->
        <!-- Display factory name -->
        <!-- <div v-if="selectedFactoryName" style="margin-top: 10px">
                    <strong>Factory Name:</strong> {{ selectedFactoryName }}
                </div> -->

        <!-- Display user name -->
        <!-- <div v-if="selectedUserName" style="margin-top: 10px">
                    <strong>Company Name:</strong> {{ selectedUserName }}
                </div> -->
        <!-- Name Field -->
        <v-text-field
          v-model="line.name"
          :rules="[rules.required]"
          label="Name Number"
          outlined
          :error-messages="errors.name ? errors.name : ''"
        >
          <template v-slot:label>
            Line Name<span style="color: red">*</span>
          </template>
        </v-text-field>

        <!-- Description Field -->
        <v-textarea v-model="line.description" label="Description" />
        <v-select
          v-model="line.status"
          :items="statusItems"
          label="Line Status"
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
              Create Line
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>
</template>
<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";
export default {
  data() {
    return {
      valid: false,
      loading: false, // Controls loading state of the button
      statusItems: ["Active", "Inactive"],
      selectedFactoryName: null, // Displayed factory name
      selectedUserName: null, // Displayed user name
      selectedFloorName: null,
      line: {
        name: "",
        description: "",
        company_id:'',
        status: "Active",
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      units: [],
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
      Object.entries(this.line).forEach(([key, value]) => {
        formData.append(key, value);
      });

      // Simulate a 3-second loading time (e.g., for an API call)
      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post("/line", formData);
          if (response.data.success) {
            this.resetForm();
            toast.success("Line Created successfully!");
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
          } else {
            // Handle other server errors
            this.serverError = "An error occurred. Please try again.";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000); // Simulates a 3-second loading duration
    },

    async fetchUnits(search) {
      try {
        const response = await this.$axios.get(`/get_units`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.units = response.data;
      } catch (error) {
        console.error("Error fetching units:", error);
      }
    },
    // Format factory name with user name
    formatUnit(unit) {
      if (unit) {
        // Check if `unit` is a number, and find the corresponding object
        if (typeof unit === "number") {
          unit = this.units.find((item) => item.id === unit);
        }
        // If the unit is found, process further
        if (unit) {
          const lineName = unit.name || "No Unit Name";
          const floorName = unit.floors?.name || "No Floor";
          const factoryName = unit.floors?.factories?.name || "No Factory";
          const companyName = unit.floors?.factories?.company?.name || "No Company";

          // Set company_id for the line
          this.line.company_id = unit.company_id || null;
          // Return the formatted string
          return `${lineName} -- ${floorName} -- ${factoryName} -- ${companyName}`;
        }
      }
      return "No Unit Data";
    },
    resetForm() {
      this.line = {
        name: "",
        number: "",
        status: "Active",
        description: "",
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },
  },
};
</script>
