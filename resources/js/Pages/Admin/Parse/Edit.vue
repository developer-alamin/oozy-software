<template>
  <v-card outlined class="mx-auto my-5" max-width="">
    <v-card-title>Spares Parts</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <v-col cols="6">
            <v-autocomplete
              v-model="parse.company_id"
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
              v-model="parse.factory_id"
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
        </v-row>
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="parse.name"
              :rules="[rules.required]"
              label="Parse Name"
              outlined
              density="comfortable"
              :error-messages="errors.name ? errors.name : ''"
            >
              <template v-slot:label>
                Parse Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="6">
            <v-autocomplete
              v-model="parse.category_id"
              :items="categories"
              item-value="id"
              item-title="name"
              label="Select Category"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.category_id ? errors.category_id : ''"
              @update:search="fetchCategories"
            >
              <template v-slot:label>
                Select Category
                <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="4">
            <v-text-field
              v-model="parse.supplier_name"
              :rules="[rules.required]"
              label="Supplier Name"
              outlined
              density="comfortable"
              :error-messages="errors.supplier_name ? errors.supplier_name : ''"
            >
              <template v-slot:label>
                Supplier Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="parse.brand_name"
              :rules="[rules.required]"
              label="Brand Name"
              outlined
              density="comfortable"
              :error-messages="errors.brand_name ? errors.brand_name : ''"
            >
              <template v-slot:label>
                Brand Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="parse.model_name"
              :rules="[rules.required]"
              label="Model Name"
              outlined
              density="comfortable"
              :error-messages="errors.model_name ? errors.model_name : ''"
            >
              <template v-slot:label>
                Model Name <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="3">
            <v-date-input
              v-model="parse.purchase_date"
              label="Purchase Date"
              density="comfortable"
              :error-messages="errors.purchase_date ? errors.purchase_date : ''"
            />
          </v-col>
          <v-col cols="3">
            <v-autocomplete
              v-model="parse.parse_unit_id"
              :items="units"
              item-value="id"
              item-title="name"
              label="Select Units"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.parse_unit_id ? errors.parse_unit_id : ''"
              @update:search="fetchParseUnit"
            >
              <template v-slot:label>
                Select Units
                <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="parse.quantity"
              label="Quantity"
              :rules="[rules.required]"
              outlined
              density="comfortable"
              :error-messages="errors.quantity ? errors.quantity : ''"
            >
              <template v-slot:label>
                Quantity <span style="color: red">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="3">
            <v-text-field
              v-model="parse.purchase_price"
              label="Purchase Price"
              outlined
              density="comfortable"
              :error-messages="
                errors.purchase_price ? errors.purchase_price : ''
              "
            >
              <template v-slot:label> Purchase Price </template>
            </v-text-field>
          </v-col>
        </v-row>

        <v-textarea
          v-model="parse.note"
          label="Note"
          density="comfortable"
          :error-messages="errors.note ? errors.note : ''"
        />
        <v-select
          v-model="parse.status"
          :items="statusItems"
          label="Mechine Status"
          clearable
          density="comfortable"
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
              Parse Update
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
      statusItems: ["Active", "Inactive"],
      parse: {
        rent_date: null,
        purchase_date: null,
        purchase_price: 0,
        name: "",
        date: null,
        company_id: null,
        factory_id: null,
        category_id: null,
        brand_name: null,
        model_name: null,
        supplier_name: null,
        parse_unit_id: null,
        supplier_id: null,
        quantity: 0,
        note: "",
        status: "Active",
      },
      errors: {}, // Stores validation errors
      serverError: null, // Stores server-side error messages
      limit: 5,
      companies: [],
      factories: [],
      categories: [],
      units: [],
      brands: [], // Array to store brands data
      models: [], // Array to store models data
      types: [], // Array to store types data
      sources: [], // Array to store sources data
      suppliers: [], // Array to store suppliers data
      rents: [], // Array to store rents data
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
    this.fetchCompanies().then(() => {
      this.fetchParse();
    });
    this.fetchCategories().then(() => {
      this.fetchParse();
    });

    this.fetchParseUnit().then(() => {
      this.fetchParse();
    });
  },
  methods: {
    async fetchParse() {
      const technicianId = this.$route.params.uuid;
      try {
        const response = await this.$axios.get(`/parse/${technicianId}/edit`);
        this.parse = response.data.parse;
        this.parse.status = this.statusItems.includes(this.parse.status)
          ? this.parse.status
          : "";
        this.fetchCompanies();
        // Set the selected company based on the company_id
      } catch (error) {
        this.serverError = "Error fetching Parts data.";
      }
    },

    async submit() {
      this.errors = {}; // Reset errors before submission
      this.serverError = null;
      this.loading = true;
      const parseId = this.$route.params.uuid; // Assuming type ID is in route params
      setTimeout(async () => {
        try {
          const response = await this.$axios.put(
            `parse/${parseId}`,
            this.parse
          );
          console.log(response.data);
          if (response.data.success) {
            toast.success("parse update successfully!");
            this.$router.push({ name: "ParseIndex" }); // Redirect to type list page
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to update parse .");
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Error updating parse . Please try again.");
            this.serverError = "Error parse .";
          }
        } finally {
          // Stop loading after the request (or simulated time) is done
          this.loading = false;
        }
      }, 1000);
    },

    async submitOnd() {
      // Reset errors and loading state before submission
      this.errors = {};
      this.serverError = null;
      this.loading = true; // Start loading when submit is clicked

      const formData = new FormData();
      Object.entries(this.parse).forEach(([key, value]) => {
        formData.append(key, value);
      });
      // const formData = new FormData();
      // Object.entries(this.factory).forEach(([key, value]) => {
      //     if (Array.isArray(value)) {
      //         value.forEach((val) => formData.append(`${key}[]`, val));
      //     } else {
      //         formData.append(key, value);
      //     }
      // });
      // Simulate a 3-second loading time (e.g., for an API call)
      setTimeout(async () => {
        try {
          // Assuming the actual API call here
          const response = await this.$axios.post("/mechine-assing", formData);
          console.log(response.data);

          if (response.data.success) {
            toast.success("mechine assing create successfully!");
            // localStorage.setItem("token", response.data.token);
            this.resetForm();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            toast.error("Failed to create mechine assing.");
            // Handle validation errors from the server
            this.errors = error.response.data.errors || {};
          } else {
            toast.error("Failed to create mechine assing.");
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
      this.parse = {
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

    async fetchCompanys(search) {
      try {
        const response = await this.$axios.get(`/get_companys`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.companys = response.data;
      } catch (error) {
        console.error("Error fetching companys:", error);
      }
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
        this.fetchFactories();
      } catch (error) {
        console.error("Error fetching companies:", error);
      }
    },
    async fetchFactories(search = "") {
      if (!this.parse.company_id) {
        this.factories = []; // Clear factories if no company is selected
        return;
      }
      try {
        const response = await this.$axios.get(`/get_company_ways_factories`, {
          params: {
            search,
            company_id: this.parse.company_id,
            limit: this.limit,
          },
        });
        this.factories = response.data;
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },

    async onCompanyChange(companyId) {
      this.parse.factory_id = null; // Reset selected factory
      this.factories = []; // Clear factories
      if (!companyId) return; // Exit if no company is selected
      this.fetchFactories();
    },
    async fetchCategories(search) {
      try {
        const response = await this.$axios.get(`/parse/get_category`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.categories = response.data;
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    },

    async fetchBrands(search) {
      try {
        const response = await this.$axios.get(`/parse/get_brands`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.brands = response.data;
      } catch (error) {
        console.error("Error fetching brands:", error);
      }
    },
    async fetchModels(search) {
      try {
        const response = await this.$axios.get(`/parse/get_models`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.models = response.data;
      } catch (error) {
        console.error("Error fetching models:", error);
      }
    },

    async fetchSuppliers(search) {
      try {
        const response = await this.$axios.get(`/parse/get_suppliers`, {
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
    async fetchParseUnit(search) {
      try {
        const response = await this.$axios.get(`/parse/units`, {
          params: {
            search: search,
            limit: this.limit,
          },
        });
        // console.log(response.data);
        this.units = response.data;
      } catch (error) {
        console.error("Error fetching unit:", error);
      }
    },
  },
};
</script>
