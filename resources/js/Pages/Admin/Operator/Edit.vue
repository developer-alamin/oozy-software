<template>
  <v-card outlined class="mx-auto my-5" max-width="900">
    <v-card-title>Edit Operator</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="update">
        <!-- Company Selection -->
        <v-autocomplete
          v-model="operator.company_id"
          :items="companies"
          item-value="id"
          item-title="name"
          outlined
          clearable
          density="comfortable"
          :rules="[rules.required]"
          :error-messages="errors.company_id ? errors.company_id : ''"
          @update:search="fetchCompanies"
          @update:model-value="onCompanyChange"
        >
          <template v-slot:label>
            Select Company <span style="color: red">*</span>
          </template>
        </v-autocomplete>
        <v-autocomplete
          v-model="operator.factory_id"
          :items="factories"
          item-value="id"
          item-title="name"
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

        <!-- Name Field -->
        <v-text-field
          v-model="operator.name"
          :rules="[rules.required]"
          label="Name *"
          outlined
          :error-messages="errors.name ? errors.name : ''"
        >
          <template v-slot:label>
            Name <span style="color: red">*</span>
          </template>
        </v-text-field>
        <v-select
          v-model="operator.type"
          :items="typeItems"
          :rules="[rules.required]"
          label="Operator Type"
          clearable
        >
          <template v-slot:label>
            Operator Type <span style="color: red">*</span>
          </template>
        </v-select>

        <!-- Email Field -->
        <v-text-field
          v-model="operator.email"
          :rules="[rules.email]"
          label="Email"
          outlined
          :error-messages="errors.email ? errors.email : ''"
        ></v-text-field>

        <!-- Phone Field -->
        <v-text-field
          v-model="operator.phone"
          label="Phone"
          outlined
          :error-messages="errors.phone ? errors.phone : ''"
        ></v-text-field>

        <!-- Address Field -->
        <v-textarea
          v-model="operator.address"
          label="Address"
          outlined
          :error-messages="errors.address ? errors.address : ''"
        ></v-textarea>

        <!-- Description Field -->
        <v-textarea
          v-model="operator.description"
          label="Description"
          outlined
          :error-messages="errors.description ? errors.description : ''"
        ></v-textarea>

        <!-- Status Field -->
        <v-select
          v-model="operator.status"
          :items="statusItems"
          label="Operator Status"
          clearable
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
              color="primary"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Update Operator
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
      typeItems: ["General", "Special", "Manager"],
      operator: {
        company_id: null,
        name: "",
        email: "",
        type: "General",
        phone: "",
        address: "",
        description: "",
        status: "Inactive", // Default status
      },
      companies: [],
      factories: [],
      errors: {},
      serverError: null,
      rules: {
        required: (value) => !!value || "Required.",
        email: (value) => /.+@.+\..+/.test(value) || "E-mail must be valid.",
      },
    };
  },
  created() {
    this.fetchOperator();
    this.fetchCompanies();
  },
  methods: {
    async fetchOperator() {
      const operatorId = this.$route.params.uuid;
      try {
        const response = await this.$axios.get(`/operator/${operatorId}/edit`);
        this.fetchFactories();
        this.operator = response.data.operator;
        this.operator.status =
          this.operator.status === "Active" ? "Active" : "Inactive";

        // Set the selected company based on the company_id
        const selectedCompany = this.companys.find(
          (c) => c.id === this.operator.company_id
        );
        if (selectedCompany) {
          this.operator.company_id = selectedCompany.id; // Set the company_id for v-autocomplete
        }
      } catch (error) {
        this.serverError = "Error fetching operator data.";
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
      } catch (error) {
        console.error("Error fetching companies:", error);
      }
    },
    async fetchFactories(search = "") {
      if (!this.operator.company_id) {
        this.factories = []; // Clear factories if no company is selected
        return;
      }
      try {
        const response = await this.$axios.get(`/get_company_ways_factories`, {
          params: {
            search,
            company_id: this.operator.company_id,
            limit: this.limit,
          },
        });
        this.factories = response.data;
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },

    async onCompanyChange(companyId) {
      this.operator.factory_id = null; // Reset selected factory
      this.factories = []; // Clear factories
      if (!companyId) return; // Exit if no company is selected
      this.fetchFactories();
    },
    async update() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;
      const operatorId = this.$route.params.uuid;
      try {
        const response = await this.$axios.put(
          `/operator/${operatorId}`,
          this.operator
        );
        if (response.data.success) {
          this.$router.push({ name: "OperatorIndex" });
          toast.success("Operator updated successfully!");
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          toast.error("Failed to update operator.");
        } else {
          this.serverError = "Error updating operator.";
          toast.error(this.serverError);
        }
      } finally {
        this.loading = false;
      }
    },
    resetForm() {
      this.fetchOperator(); // Reset the form with existing operator data
      this.errors = {};
      this.$refs.form.resetValidation();
    },
  },
};
</script>
