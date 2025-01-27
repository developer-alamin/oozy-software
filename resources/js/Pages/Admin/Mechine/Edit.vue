<template>
  <v-card outlined class="mx-auto my-5" >
    <v-card-title>Edit Machine</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <v-col cols="6" md="6">
            <v-text-field
              v-model="machine.machine_code"
              label="Machine Code"
              outlined
              density="comfortable"
              :error-messages="errors.machine_code ? errors.machine_code : ''"
            >
            </v-text-field>
          </v-col>
          <v-col cols="6" md="6">
            <v-text-field
              v-model="machine.name"
              :rules="[rules.required]"
              label="Machine Name"
              outlined
              density="comfortable"
              :error-messages="errors.name ? errors.name : ''"
            >
              <template v-slot:label>
                Machine Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" md="6"> 
            <v-autocomplete
              v-model="machine.factory_id"
              :items="factories"
              item-value="id"
              item-title="name"
              label="Select Factory"
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
            <v-autocomplete
              v-model="machine.brand_id"
              :items="brands"
              item-value="id"
              item-title="name"
              label="Select Machine Brand"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.brand_id ? errors.brand_id : ''"
              @update:search="fetchBrands"
              @update:model-value="onBrandChange"
            >
              <template v-slot:label>
                Select Machine Brand
                <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="machine.product_model_id"
              :items="models"
              item-value="id"
              item-title="name"
              label="Select Machine Model"
              density="comfortable"
              clearable
              :rules="[rules.required]"
              :error-messages="errors.product_model_id ? errors.product_model_id : ''"
              @update:search="fetchModels"
              :disabled="!machine.brand_id"
            >
              <template v-slot:label>
                Select Machine Model
                <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
                v-model="machine.machine_type_id"
                :items="types"
                item-value="id"
                item-title="name"
                label="Select Mechine Type"
                density="comfortable"
                clearable
                :rules="[rules.required]"
                :error-messages="errors.mechine_type_id ? errors.mechine_type_id : ''"
                @update:search="fetchTypes"
                @update:model-value="updatePreventiveServiceDays"
              >
                <template v-slot:label>
                  Select Mechine Type
                  <span style="color: red">*</span>
                </template>
              </v-autocomplete>

          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="machine.partial_maintenance_day"
              :rules="[rules.required]"
              label="Mechine Preventive Service Days"
              outlined
              density="comfortable"
              :error-messages="
                errors.partial_maintenance_day
                  ? errors.partial_maintenance_day
                  : ''
              "
            >
              <template v-slot:label>
                Mechine Preventive Service Days
                <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="machine.source_id"
              :items="sources"
              item-value="id"
              item-title="name"
              label="Select Mechine Source"
              density="comfortable"
              clearable
              :rules="[rules.required]"
              :error-messages="
                errors.mechine_source_id ? errors.mechine_source_id : ''
              "
              @update:search="fetchSources"
            >
              <template v-slot:label>
                Select Mechine Source
                <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="machine.supplier_id"
              :items="suppliers"
              item-value="id"
              item-title="name"
              label="Select Supplier"
              density="comfortable"
              clearable
              :error-messages="errors.supplier_id ? errors.supplier_id : ''"
              @update:search="fetchSuppliers"
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-date-input
              v-model="machine.purchase_date"
              label="Purchase Date"
              density="comfortable"
              :error-messages="errors.purchase_date ? errors.purchase_date : ''"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="machine.purchase_price"
              label="Purchase Price"
              outlined
              density="comfortable"
              :error-messages="
                errors.purchace_price ? errors.purchace_price : ''
              "
            >
              <template v-slot:label> Purchase Price </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
                v-model="machine.machine_status_id"
                :items="MachineStatus"
                item-value="id"
                item-title="name"
                outlined
                clearable
                density="comfortable"
                :rules="[rules.required]"
                :error-messages="errors.machine_status_id ? errors.machine_status_id : ''"
                @update:search="fetchStatus"
                >
                <template v-slot:label>
                    Select Status <span style="color: red">*</span>
                </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12">
            <v-textarea
            v-model="machine.rent_note"
            label="Note"
            density="comfortable"
            :error-messages="errors.rent_note ? errors.rent_note : ''"
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
              Update Machine
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
// import VDateInput from "../../Components/VDateInput.vue";

export default {
  // components: {
  //     VDateInput,
  // },
  data() {
    return {
      valid: false,
      loading: false, // Controls loading state of the button
      statusItems: [
        "Preventive",
        "Production",
        "Breakdown",
        "Under Maintenance",
        "Loan",
        "Idol",
        "AsFactory",
        "Scraped",
        "Assign"
      ],

      machine: {
        rent_date: new Date(),
        purchase_date: null,
        purchase_price: 0,
        name: "",
        date: null,
        company_id: null,
        factory_id: null,
        brand_id: null,
        product_model_id: '',
        machine_type_id: null,
        partial_maintenance_day: "",
        full_maintenance_day: "",
        source_id: null,
        supplier_id: null,
        rent_id: null,
        machine_code: "",
        rent_note: "",
        machine_status_id: null, // New property for checkbox
        rent_note: "",
        rent_amount_type: null,
        rent_price: "",
        rent_name: "",
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      limit: 5,
      companys: [], // Array to store Company data
      factories: [], // Array to store factories data
      brands: [], // Array to store brands data
      models: [], // Array to store models data
      types: [], // Array to store types data
      sources: [], // Array to store sources data
      suppliers: [], // Array to store suppliers data
      rents: [], // Array to store rents data
      MachineStatus:[],
      selectedCompany: null, // Bound to selected Company in v-autocomplete
      rules: {
        required: (value) => !!value || "Required.",
        email: (value) => /.+@.+\..+/.test(value) || "E-mail must be valid.",
        confirm_password: (value) =>
          value === this.company.password || "Passwords must match.", // Confirms password matches
        phone: (value) =>
          /^\d{11}$/.test(value) || "Phone number must be valid.",
      },
      visible: false,
      confirm_visible: false,
    };
  },
  computed: {
    // Function to limit the allowed dates within the min and max date range
    allowedDates() {
      return (date) => {
        return date >= new Date();
      };
    },
  },
  created() {
    this.fetchFactories().then(() => {
      this.fetchMechine();
    });
    this.fetchModels().then(() => {
      this.fetchMechine();
    });
    this.fetchTypes().then(() => {
      this.fetchMechine();
    });
    this.fetchSources().then(() => {
      this.fetchMechine();
    });
    this.fetchSuppliers().then(() => {
      this.fetchMechine();
    });
    this.fetchBrands().then(() => {
      this.fetchMechine();
    });
    this.fetchStatus().then(() => {
      this.fetchMechine();
    });
  },
  methods: {
    async fetchMechine() {
      const mecineAssingId = this.$route.params.uuid;
      try {
        const response = await this.$axios.get(
          `/mechine/assing/${mecineAssingId}/edit`
        );

        this.machine = response.data.mechineAssing;
      
      } catch (error) {
        console.log(error);
        
        this.serverError = "Error fetching technician data.";
      }
    },
    async submit() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;
      const mechineId = this.$route.params.uuid; // Assuming type ID is in route params
      setTimeout(async () => {
        try {
          const response = await this.$axios.put(
            `/machine-assing/${mechineId}`,this.machine);

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

    resetForm() {
      this.machine = {
        company_id: "",
        name: "",
        email: "",
        phone: "",
        factory_code: "",
        location: "",
        status: "Preventive", // New property for checkbox
      };
      this.errors = {}; // Reset errors on form reset
      if (this.$refs.form) {
        this.$refs.form.reset(); // Reset the form via its ref if necessary
      }
    },
    async fetchStatus(search) {
        try {
            // Make a GET request to the '/get_companies' endpoint with query parameters
            const response = await this.$axios.get('/machine/get_status', {
                params: {
                search: this.search || '', // Use `this.search` or fallback to an empty string
                limit: this.limit || 5,   // Use `this.limit` or fallback to default value (5)
                },
            });
            // Update the companies array with the fetched data
            this.MachineStatus = response.data;
            } catch (error) {
            // Log any errors that occur during the request
            console.error('Error fetching companies:', error);
            // Optionally, handle the error (e.g., show an error message to the user)
            this.$toast.error('Failed to fetch companies. Please try again later.');
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
        // console.log(response.data);
        this.factories = response.data;
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },
    async fetchBrands(search) {
      try {
        const response = await this.$axios.get("/get_brands", {
          params: { search },
        });
        this.brands = response.data;
      } catch (error) {
        console.error("Error fetching brands:", error);
      }
    },
    // Handle brand change, fetch models based on selected brand

   // Fetch models based on search term (for model autocomplete)
    async fetchModels(search = "") {
      if (!this.machine.brand_id) return; // Ensure brand_id is available
      try {
        const response = await this.$axios.get("/get_models", {
          params: { search, brand_id: this.machine.brand_id },
        });
        this.models = response.data;
      } catch (error) {
        console.error("Error fetching models:", error);
      }
    },
    // Handle brand change
    async onBrandChange() {
      this.machine.product_model_id = null; // Reset product model selection
      if (!this.machine.brand_id) {
        this.models = []; // Clear models if no brand is selected
        return;
      }
      try {
        // Fetch models for the selected brand
        await this.fetchModels(); // This will handle fetching based on brand_id
      } catch (error) {
        console.error("Error fetching models:", error);
      }
    },
    async fetchTypes(search) {
      try {
        // Fetch types based on search term and limit
        const response = await this.$axios.get('/get_types', {
          params: {
            search: search,  // Pass the search term
            limit: this.limit // Apply limit
          }
        });
        
        // Update the types list with the response data
        this.types = response.data;
        console.log(this.types); // Log the fetched types
      } catch (error) {
        // Error handling
        console.error("Error fetching types:", error);
        // Optionally, you can set an error message for the UI
      }
    },

    updatePreventiveServiceDays() {
      const selectedType = this.types.find(
        (type) => type.id === this.machine.mechine_type_id
      );
      // console.log("Selected Type:", selectedType); // Debugging log

      this.machine.preventive_service_days = selectedType
        ? selectedType.day
        : "";
    },
    async fetchSources(search) {
      try {
        const response = await this.$axios.get(`/get_sources`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.sources = response.data;
      } catch (error) {
        console.error("Error fetching sources:", error);
      }
    },
    async fetchSuppliers(search) {
      try {
        const response = await this.$axios.get(`/get_suppliers`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.suppliers = response.data;
      } catch (error) {
        console.error("Error fetching suppliers:", error);
      }
    },
  },
};
</script>
