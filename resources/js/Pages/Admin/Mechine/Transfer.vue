<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Machine Movement</v-card-title>
    <v-card-text>
      <v-form v-model="valid" ref="form" @submit.prevent="update">
        <v-row>
          <v-col cols="6">
            <v-select
              v-model="machine.location_status"
              :items="statusLocation"
              label="Location"
              clearable
              density="comfortable"
              @update:model-value="handleLocationChange"
            ></v-select>
          </v-col>
          <v-col cols="6">
            <v-autocomplete
              v-model="machine.machine_status_id"
              :items="machine_statuses"
              item-value="id"
              :rules="[rules.required]"
              item-title="name"
              label="Select Machine Status"
              density="comfortable"
              clearable
              :error-messages="
                errors.machine_status_id ? errors.machine_status_id : ''
              "
              @update:search="fetchMachineStatus"
            >
              <template v-slot:label>
                Select Machine Status <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
        </v-row>

        <v-autocomplete
          v-if="machine.location_status == 'Sewing Line'"
          v-model="machine.line_id"
          :items="lines"
          item-value="id"
          item-title="name"
          label="Select Line"
          outlined
          clearable
          density="comfortable"
          @update:model-value="fetchLines"
        >
          <template v-slot:label>
            Select Line <span style="color: red">*</span>
          </template>
        </v-autocomplete>

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
              Machine Movement
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
      loading: false,
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      machine: {
        location_status: "",
        machine_status_id: null,
        line_id: null,
        factory_id: null,
      },
      lines: [],
      machine_statuses: [],
      statusLocation: ["Sewing Line", "Idle Storage", "Out of Factory"],
      errors: {},
      rules: {
        required: (value) => !!value || "Required.",
      },
    };
  },
  created() {
    this.transferMachine();
    this.fetchMachineStatus();
  },
  methods: {
    async update() {
      this.errors = {}; // Reset errors before submission
      // this.serverError = null;
      this.loading = true;
      const machineId = this.$route.params.uuid; // Assuming type ID is in route params
      setTimeout(async () => {
        try {
          const response = await this.$axios.post(
            `/machine/transfer/update/${machineId}`,
            this.machine
          );
          console.log(response);
          if (response.data.success) {
            toast.success("mechine transfer successfully!");
            this.$router.push({ name: "MechineIndex" }); // Redirect to type list page
          }
        } catch (error) {
          console.log(error);
          
          if (error.response && error.response.status === 422) {
            toast.error("Failed to update mechine transfer .");
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Error updating mechine transfer . Please try again.");
            this.serverError = "Error mechine transfer .";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000);
    },
    // Handle location change and set machine status and lines accordingly

    // Fetch available lines based on factory_id
    async fetchLines() {
      if (!this.machine.factory_id) {
        console.warn("Factory ID is missing; cannot fetch lines.");
        this.lines = [];
        return;
      }
      try {
        const response = await this.$axios.get("/get_factory_lines", {
          params: { factory_id: this.machine.factory_id },
        });
        this.lines = response.data;

        console.log(response.data);
      } catch (error) {
        console.error("Error fetching lines:", error);
      }
    },

    async handleLocationChange(value) {
      if (value == "Sewing Line") {
        const productionStatus = this.machine_statuses.find(
          (status) => status.name == "Production"
        );
        if (productionStatus) {
          this.machine.machine_status_id = productionStatus.id;
        }
        this.fetchLines(); // Fetch lines when 'Sewing Line' is selected
      } else if (value == "Idle Storage") {
        const idleStatus = this.machine_statuses.find(
          (status) => status.name == "Idle"
        );
        if (idleStatus) {
          this.machine.machine_status_id = idleStatus.id;
        }
      } else if (value == "Out of Factory") {
        const factoryOutStatus = this.machine_statuses.find(
          (status) => status.name == "Factory Out"
        );
        if (factoryOutStatus) {
          this.machine.machine_status_id = factoryOutStatus.id;
        }
      } else {
        this.machine.machine_status_id = null;
      }

      if (value !== "Sewing Line") {
        this.machine.line_id = null;
        this.lines = [];
      }
    },
    // Fetch machine statuses (for demonstration)
    async fetchMachineStatus(query) {
      try {
        const response = await this.$axios.get("/get_machine_statuses", {
          params: { search: query },
        });
        this.machine_statuses = response.data;
      } catch (error) {
        console.error("Error fetching machine statuses:", error);
      }
    },

    // Initiate the machine transfer
    async transferMachine() {
      const machineId = this.$route.params.uuid;
      // console.log(machineId);

      try {
        const response = await this.$axios.get(
          `/mechine/${machineId}/transfer`
        );

        // console.log(response.data);

        if (response.data.success) {
          this.machine = response.data.mechineTransfer;
          this.fetchLines();
          // this.$toast.success("Machine transferred successfully.");
        } else {
          this.$toast.error(response.data.message);
        }
      } catch (error) {
        console.error("Error transferring machine:", error);
        this.$toast.error("An error occurred while transferring the machine.");
      }
    },
    resetForm() {
      this.machine = {
        location_status: "",
        machine_status_id: null,
        line_id: null,
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },
  },
};
</script>
