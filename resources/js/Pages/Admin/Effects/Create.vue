<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Effect</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <v-col cols="12">
            <v-autocomplete
              v-model="effect.cause_id"
              :items="causes"
              item-value="id"
              :item-title="formatCause"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.cause_id || ''"
              @update:search="fetchCause"
              no-filter
            >
              <template v-slot:label>
                Select Cause <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12">
            <v-select
              v-model="effect.status"
              :items="statusItems"
              label="Status"
              clearable
            ></v-select>
          </v-col>
        </v-row>

        <v-textarea
          v-model="effect.name"
          label="Name"
          :rules="[rules.required]"
          outlined
          density="comfortable"
          :error-messages="errors.name || ''"
        >
          <template v-slot:label>
            Name <span style="color: red">*</span>
          </template>
        </v-textarea>

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
              Create Effect
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
import { toast } from "vue3-toastify";

export default {
  data() {
    return {
      valid: false,
      loading: false,
      statusItems: ["Active", "Inactive"],
      effect: {
        cause_id: null,
        name: "", // Updated property name
        status: "Active",
      },
      causes: [],
      errors: {},
      serverError: null,
      rules: {
        required: (value) => !!value || "Required.",
      },
      limit: 5,
    };
  },
  methods: {
    async submit() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;

      const formData = new FormData();
      Object.entries(this.effect).forEach(([key, value]) => {
        formData.append(key, value);
      });

      try {
        const response = await this.$axios.post("/effect", formData);  // Updated API endpoint to /effect
    
        if (response.data.success) {
          toast.success("Effect created successfully!");
          this.resetForm();
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          toast.error("Failed to create effect.");
        } else {
          toast.error("An error occurred. Please try again.");
          this.serverError = "An error occurred. Please try again.";
        }
      } finally {
        this.loading = false;
      }
    },
    async fetchCause(search) {
      try {
        const response = await this.$axios.get("/get_causes", {
          params: { search: search || "", limit: this.limit },
        });
        this.causes = response.data;
      } catch (error) {
        console.error("Error fetching companies:", error);
        toast.error("Failed to fetch companies. Please try again later.");
      }
    },
    formatCause(cause){
      if (cause) {
        if (typeof cause === "number") {
          cause = this.causes.find((item) => item.id === cause);
        }
        if (cause) {
          const problemnoteName = cause.problem_note?.name || "No Problem Note Name";
          const companyName = cause.problem_note?.company?.name || "No Company Name";

          this.effect.cause_id = cause.cause_id;
          return `${
            cause.name || "No Cause Name"
          } -- ${problemnoteName} -- ${companyName}`;
        }
      }
      return "No Cause Data";
    },
    resetForm() {
      this.effect = {
        company_id: null,
        name: "", // Reset the name field
        status: "Active",
      };
      this.errors = {};
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    },
  },
};
</script>
