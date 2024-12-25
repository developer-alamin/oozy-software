<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Edit Factory</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="update">
        <v-row>
          <v-col cols="6">
            <v-autocomplete
              v-model="factory.company_id"
              :items="companies"
              item-value="id"
              :item-title="formatFloor"
              outlined
              clearable
              chips
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.company_id ? errors.company_id : ''"
              @update:search="fetchCompanies"
            >
              <template v-slot:label>
                Select Company <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="factory.name"
              :rules="[rules.required]"
              label="Factory Name"
              outlined
              density="comfortable"
              :error-messages="errors.name ? errors.name : ''"
            >
              <template v-slot:label>
                Factory Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
        </v-row>
        <!-- Name Field -->
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="factory.email"
              label="Email"
              outlined
              density="comfortable"
              :error-messages="errors.email ? errors.email : ''"
            >
              <!-- <template v-slot:label>
                                Email <span style="color: red">*</span>
                            </template> -->
            </v-text-field></v-col
          >
          <v-col cols="6">
            <v-text-field
              v-model="factory.phone"
              label="Phone"
              outlined
              density="comfortable"
              :error-messages="errors.phone ? errors.phone : ''"
            >
              <template v-slot:label> Phone </template>
            </v-text-field>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="4">
            <v-text-field
              v-model="factory.factory_owner"
              :rules="[rules.factory_owner]"
              label="Factory Woner"
              outlined
              density="comfortable"
              :error-messages="errors.factory_owner ? errors.factory_owner : ''"
            >
              <template v-slot:label> Factory Woner Name</template>
            </v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="factory.factory_size"
              :rules="[rules.factory_size]"
              label="Factory Code"
              outlined
              density="comfortable"
              :error-messages="errors.factory_size ? errors.factory_size : ''"
            >
              <template v-slot:label> Factory Size </template>
            </v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="factory.factory_capacity"
              :rules="[rules.factory_capacity]"
              label="Factory Code"
              outlined
              density="comfortable"
              :error-messages="
                errors.factory_capacity ? errors.factory_capacity : ''
              "
            >
              <template v-slot:label> Factory Capacity </template>
            </v-text-field>
          </v-col>
          <!-- <v-col cols="4">
                        <v-select
                            v-model="factory.status"
                            :items="statusItems"
                            label="Factory Status"
                            clearable
                            density="comfortable"
                        ></v-select>
                    </v-col> -->
        </v-row>
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="factory.factory_code"
              :rules="[rules.factory_code]"
              label="Factory Code"
              outlined
              density="comfortable"
              :error-messages="errors.factory_code ? errors.factory_code : ''"
            >
              <template v-slot:label> Factory Code </template>
            </v-text-field>
          </v-col>
          <v-col cols="6">
            <v-select
              v-model="factory.status"
              :items="statusItems"
              label="Factory Status"
              clearable
              density="comfortable"
            ></v-select>
          </v-col>
        </v-row>

        <v-textarea
          v-model="factory.location"
          label="Location"
          density="comfortable"
          :error-messages="errors.location ? errors.location : ''"
        />
        <v-textarea
          v-model="factory.note"
          label="Note"
          density="comfortable"
          :error-messages="errors.note ? errors.note : ''"
        />

        <!-- Action Buttons -->
        <v-row class="mt-4">
          <!-- Submit Button -->
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
              Update factory
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>

    <v-alert v-if="serverError" type="error" class="my-4">
      {{ serverError }}
    </v-alert>
  </v-card>
</template>

<script>
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      valid: false,
      loading: false,
      statusItems: ["Active", "Inactive"],
      factory: {
        company_id: null,
        name: "",
        email: "",
        phone: "",
        location: "",
        note: "",
        factory_code: "",
        factory_owner: "",
        factory_size: "",
        factory_capacity: "",
        status: false,
      },
      errors: {},
      serverError: null,
      limit: 5,
      companies: [],
      rules: {
        required: (value) => !!value || "Required.",
      },
    };
  },

  created() {
    this.fetchCompanies().then(() => {
      this.fetchFactoryData();
    });
    this.fetchFactoryData();
  },
  methods: {
    async fetchFactoryData() {
      const factoryId = this.$route.params.uuid; // Assuming you're passing factory ID in route
      const response = await this.$axios.get(`/factory/${factoryId}/edit`);

      const selectedCompany = this.companies.find(
        (c) => c.id === this.factory.company_id
      );
      if (selectedCompany) {
        this.factory.company_id = selectedCompany.id; // Set the company_id for v-autocomplete
      }
      // console.log(response.data);
      if (response.data.success) {
        this.factory = response.data.factory;
      }
    },

    async update() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;
      const factoryId = this.$route.params.uuid; // Assuming brand ID is in route params
      setTimeout(async () => {
        try {
          const response = await this.$axios.put(
            `/factory/${factoryId}`,
            this.factory
          );

          if (response.data.success) {
            toast.success("Factory update successfully!");
            this.$router.push({ name: "FactoryIndex" }); // Redirect to Factory list page
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to update Factory.");
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Error updating Factory. Please try again.");
            this.serverError = "Error updating Factory.";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000);
    },

    formatFloor(floor) {
      if (floor) {
        if (typeof floor == "number") {
          floor = this.companies.find((item) => (item.id = floor));
        }
        return `${floor.name || "No Floor Name"}`;
      }
      return "No Floor Data";
    },
    // async submit() {
    //     this.errors = {};
    //     this.serverError = null;
    //     this.loading = true;

    //     const formData = new FormData();
    //     Object.entries(this.factory).forEach(([key, value]) => {
    //         if (Array.isArray(value)) {
    //             value.forEach((val) => formData.append(`${key}[]`, val));
    //         } else {
    //             formData.append(key, value);
    //         }
    //     });

    //     try {
    //         const response = await this.$axios.put(
    //             `/factory/${this.factory.id}`,
    //             formData
    //         );
    //         if (response.data.success) {
    //             toast.success("Factory updated successfully!");
    //             this.resetForm();
    //         }
    //     } catch (error) {
    //         if (error.response && error.response.status === 422) {
    //             this.errors = error.response.data.errors;
    //         } else {
    //             this.serverError = "An unexpected error occurred.";
    //         }
    //     } finally {
    //         this.loading = false;
    //     }
    // },
    resetForm() {
      this.factory = {
        company_id: null,
        name: "",
        email: "",
        phone: "",
        location: "",
        factory_code: "",
        status: false,
      };
      this.errors = {};
      this.serverError = null;
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    },

    // Fetch companies, floors, units, and lines as needed
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
  },
};
</script>
