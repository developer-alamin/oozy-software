<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Edit Floor</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="update">
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
        <div v-if="selectedUserName" style="margin-top: 2px">
          <strong>Company Name:</strong> {{ selectedUserName }}
        </div>
        <!-- Name Field -->
        <v-text-field
          v-model="floor.name"
          :rules="[rules.required]"
          label="Name"
          outlined
          :error-messages="errors.name ? errors.name : ''"
        >
          <template v-slot:label>
            Name <span style="color: red">*</span>
          </template>
        </v-text-field>

        <!-- Description Field -->
        <v-textarea
          v-model="floor.description"
          label="Description"
          outlined
          :error-messages="errors.description ? errors.description : ''"
        />
        <v-select
          v-model="floor.status"
          :items="statusItems"
          label="Floor Status"
          clearable
          :error-messages="errors.status ? errors.status : ''"
        ></v-select>

        <!-- Action Buttons -->

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
               class="primary-color"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Update Floor
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
      floor: {
        name: "",
        description: "",
        status: false, // Default to false (inactive)
      },
      selectedUserName: "",
      errors: {},
      factories: [],
      serverError: null,
      rules: {
        required: (value) => !!value || "Required.",
      },
    };
  },
  created() {
    this.fetchFactories().then(() => {
      this.fetchFloor();
    });
    this.fetchFloor();
  },
  methods: {
    async fetchFloor() {
      // Fetch the floor data to populate the form
      const floorId = this.$route.params.uuid; // Assuming the floor ID is passed in the route params
      try {
        const response = await this.$axios.get(`/floor/${floorId}/edit`);

        // console.log(response.data);
        const selectedFactory = this.factories.find(
          (c) => c.id === this.floor.factory_id
        );
        if (selectedFactory) {
          this.floor.factory_id = selectedFactory.id;
        }
        // console.log(response.data);
        if (response.data.success) {
          this.floor = response.data.floor;
        }
        // this.floor = response.data.floor; // Populate form with the existing floor data
        this.floor.status =
          this.floor.status === "Active" ? "Active" : "Inactive";
      } catch (error) {
        this.serverError = "Error fetching floor data.";
      }
    },
    // Format factory name with user name
    formatFactory(factory) {
      if (factory) {
        return `${factory.name} -- ${factory.company?.name || "No Company"}`;
      }
    },
    async update() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;

      try {
        const floorId = this.$route.params.uuid; // Assuming floor ID is in route params
        const response = await this.$axios.put(`/floor/${floorId}`, this.floor);
      
        if (response.data.success) {
          toast.success("Floor updated successfully!");
          this.$router.push({ name: "FloorIndex" }); // Redirect to the floor list page
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          // Handle validation errors
          toast.error("Failed to update floor.");
          this.errors = error.response.data.errors || {};
        } else {
          // Handle general server errors
          toast.error("Error updating floor. Please try again.");
          this.serverError = "Error updating floor.";
        }
      } finally {
        // Stop the loading indicator regardless of success or failure
        this.loading = false;
      }
    },
    resetForm() {
      // Reset the form with existing floor data
      this.fetchFloor();
      this.errors = {};
      this.$refs.form.resetValidation();
    },

    async fetchFactories(search) {
      try {
        const response = await this.$axios.get(`/get_factories`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.factories = response.data;
        // console.log(response.data);
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },
    // updateSelectedFactory(factoryId) {
    //     const selectedFactory = this.factories.find(
    //         (factory) => factory.id === factoryId
    //     );
    //     this.selectedUserName =
    //         selectedFactory?.user?.name || "No Company Name";
    // },
  },
  // watch: {
  //     // Watch for changes in the selected factory and update the user name
  //     "floor.factory_id": function (newFactoryId) {
  //         this.updateSelectedFactory(newFactoryId); // Update user name when factory changes
  //     },
  // },
};
</script>
