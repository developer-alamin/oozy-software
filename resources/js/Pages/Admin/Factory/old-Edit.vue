<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Edit Factory</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="update">
        <v-row>
          <v-col cols="6">
            <v-autocomplete
              v-model="factory.company_id"
              :items="companies"
              item-value="id"
              :item-title="formatCompany"
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

        <!-- Email and Phone -->
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="factory.email"
              label="Email"
              outlined
              density="comfortable"
              :error-messages="errors.email ? errors.email : ''"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="factory.phone"
              label="Phone"
              outlined
              density="comfortable"
              :error-messages="errors.phone ? errors.phone : ''"
            ></v-text-field>
          </v-col>
        </v-row>

        <!-- Owner, Size, Capacity -->
        <v-row>
          <v-col cols="4">
            <v-text-field
              v-model="factory.factory_owner"
              label="Factory Owner"
              outlined
              density="comfortable"
              :error-messages="errors.factory_owner ? errors.factory_owner : ''"
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="factory.factory_size"
              label="Factory Size"
              outlined
              density="comfortable"
              :error-messages="errors.factory_size ? errors.factory_size : ''"
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="factory.factory_capacity"
              label="Factory Capacity"
              outlined
              density="comfortable"
              :error-messages="
                errors.factory_capacity ? errors.factory_capacity : ''
              "
            ></v-text-field>
          </v-col>
        </v-row>

        <!-- Code and Status -->
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="factory.factory_code"
              label="Factory Code"
              outlined
              density="comfortable"
              :error-messages="errors.factory_code ? errors.factory_code : ''"
            ></v-text-field>
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

        <!-- Location and Note -->
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
              Update Factory
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
        status: null,
      },
      errors: {},
      serverError: null,
      companies: [],
      rules: {
        required: (value) => !!value || "Required.",
      },
    };
  },
  created() {
    this.fetchCompanies();
    this.fetchFactoryData();
  },
  methods: {
    async fetchCompanies() {
      try {
        const response = await this.$axios.get("/companies");
        this.companies = response.data.data;
      } catch (error) {
        console.error("Error fetching companies:", error);
      }
    },
    async fetchFactoryData() {
      const factoryId = this.$route.params.uuid;
      try {
        const response = await this.$axios.get(`/factory/${factoryId}/edit`);
        if (response.data.success) {
          this.factory = response.data.factory;
        }
      } catch (error) {
        console.error("Error fetching factory data:", error);
      }
    },
    async update() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;

      const factoryId = this.$route.params.uuid;
      try {
        const response = await this.$axios.put(
          `/factory/${factoryId}`,
          this.factory
        );
        if (response.data.success) {
          this.$toast.success("Factory updated successfully!");
          this.$router.push({ name: "FactoryIndex" });
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.serverError = "Error updating Factory.";
        }
      } finally {
        this.loading = false;
      }
    },
    resetForm() {
      this.factory = {
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
        status: null,
      };
      this.errors = {};
    },
    // formatCompany(company) {
    //   console.log(company);

    //   // const company = this.companies.find((item) => item.id === floor);
    //   return company ? company.name : "No Floor Data";
    // },
    formatCompany(company) {
      if (company) {
        if (typeof company == "number") {
          company = this.companies.find((item) => (item.id = company));
        }
        return `${company.name || "No Company Name"}`;
      }
      return "No Company Data";
    },
  },
};
</script>
