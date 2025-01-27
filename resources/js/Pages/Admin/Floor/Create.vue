<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Floor</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
       <v-row>
        <v-col cols="12">
          <v-autocomplete
          v-model="floor.factory_id"
          :items="factories"
          item-value="id"
          :item-title="formatFactory"
          outlined
          clearable
          density="comfortable"
          :rules="[rules.required]"
          :error-messages="errors.factory_id ? errors.factory_id : ''"
          @update:search="fetchFactories"
        >
          <template v-slot:label>
            Select Factory <span style="color: red">*</span>
          </template>
        </v-autocomplete>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
          v-model="floor.name"
          :rules="[rules.required]"
          label="Name"
          outlined
          :error-messages="errors.name ? errors.name : ''"
        >
          <template v-slot:label>
            Floor <span style="color: red">*</span>
          </template>
        </v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-select
          v-model="floor.status"
          :items="statusItems"
          label="Floor Status"
          clearable
        ></v-select>
        </v-col>
        <v-col cols="12" md="12">
           <!-- Description Field -->
          <v-textarea
            v-model="floor.description"
            label="Description"
            :error-messages="errors.description ? errors.description : ''"
          />
        </v-col>
       </v-row>
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
               class="primary-color"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Create Floor
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
      statusItems: ["Active", "Inactive","Pending"],
      floor: {
        factory_id: "",
        campany_id:'',
        name: "",
        description: "",
        status: "Active", // New property for checkbox
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      factories: [],
      companyName: null,
      selectedUserName: "",
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
      Object.entries(this.floor).forEach(([key, value]) => {
        formData.append(key, value);
      });

      // Simulate a 3-second loading time (e.g., for an API call)
      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post("/floor", formData);
          if (response.data.success) {
            toast.success("Floor create successfully!");
            this.resetForm();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to create Floor.");
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
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
      this.floor = {
        name: "",
        description: "",
        //status: "", // Reset checkbox on form reset
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },

    async fetchFactories(search) {
      try {
        const response = await this.$axios.get(`/get_factories`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        this.factories = response.data;
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },
    formatFactory(factory) {
      if (factory) {
          if (typeof factory === "number") {
            factory = this.factories.find((item) => item.id === factory);
          }
          if (factory) {
            const factoryName = factory.name || "No Factory Name";
            const userName = factory.company?.name || "No Company";
            this.floor.company_id = factory.company_id;
            return `${factoryName} -- ${userName}`;
          }
        }
        return "No Factory Data";

    },
  },
};
</script>
