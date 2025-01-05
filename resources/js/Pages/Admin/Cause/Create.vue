<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Create Cause</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="cause.problem_note_id"
              :items="problemnotes"
              item-value="id"
              :item-title="formatProblemNote"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.problem_note_id || ''"
              @update:search="fetchProblemNotes"
              no-filter
            >
              <template v-slot:label>
                Select Problem Note <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              v-model="cause.status"
              :items="statusItems"
              label="Status"
              outlined
              clearable
              density="comfortable"

            ></v-select>
          </v-col>
        </v-row>

        <v-textarea
          v-model="cause.name"
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
              Create Cause
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
      cause: {
        problem_note_id: null,
        name: "", 
        status: "Active",
      },
      problemnotes: [],
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
      Object.entries(this.cause).forEach(([key, value]) => {
        formData.append(key, value);
      });

      try {
        const response = await this.$axios.post("/cause", formData);
        if (response.data.success) {
          toast.success("Cause created successfully!");
          this.resetForm();
        }
      } catch (error) {
        console.log(error);
        
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          toast.error("Failed to create cause.");
        } else {
          toast.error("An error occurred. Please try again.");
          this.serverError = "An error occurred. Please try again.";
        }
      } finally {
        this.loading = false;
      }
    },
    async fetchProblemNotes(search) {
      try {
        const response = await this.$axios.get("/get_problemnotes", {
          params: { search: search || "", limit: this.limit },
        });
        this.problemnotes = response.data;
      } catch (error) {
        console.error("Error fetching companies:", error);
        toast.error("Failed to fetch companies. Please try again later.");
      }
    },
    formatProblemNote(problemnote){
      if (problemnote) {
        if (typeof problemnote === "number") {
          problemnote = this.problemnotes.find((item) => item.id === problemnote);
        }
        if (problemnote) {
          const companyName = problemnote.company?.name || "No Company Name";
          this.cause.problem_note_id = problemnote.problem_note_id;
          return `${
            problemnote.name || "No Problem Note Name"
          } -- ${companyName}`;
        }
      }
      return "No ProblemNote Data";
    },
    resetForm() {
      this.cause = {
        company_id: null,
        name: "", // Reset the name field
        status: "Active",
      };
      this.errors = {};
      this.serverError = null;
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    },
  },
};
</script>
