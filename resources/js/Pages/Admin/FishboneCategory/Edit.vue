<template>
  <v-card outlined class="mx-auto my-5">
    <v-card-title>Edit Fishbone Category</v-card-title>
    <v-card-text>
      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-row>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="category.problem_note_id"
              :items="problemNotes"
              item-value="id"
              :item-title="formatProblemNote"
              outlined
              clearable
              density="comfortable"
              :rules="[rules.required]"
              :error-messages="errors.problem_note_id || ''"
              @update:search="fetchProblemNotes"
            >
              <template v-slot:label>
                Select Problem Note <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>

          <v-col cols="12" md="6">
            <v-select
              v-model="category.status"
              :items="statusItems"
              label="Status"
              outlined
              clearable
              density="comfortable"
            ></v-select>
          </v-col>
        </v-row>

        <v-textarea
          v-model="category.name"
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
            <v-btn type="button" color="secondary" @click="resetForm" class="mr-3">
              Reset Form
            </v-btn>
            <v-btn
              type="submit"
              class="primary-color"
              :disabled="!valid || loading"
              :loading="loading"
            >
              Update Fishbone Category
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
      category: {
        problem_note_id: null,
        name: "",
        status: "Active",
      },
      problemNotes: [],
      errors: {},
      serverError: null,
      rules: {
        required: (value) => !!value || "Required.",
      },
      limit: 5,
    };
  },
  created() {
    this.fetchProblemNotes();
    this.fetchCategory();
  },
  methods: {
    async fetchCategory() {
      const uuid = this.$route.params.uuid;
      try {
        const response = await this.$axios.get(`/fishbone-category/${uuid}/edit`);
        this.category = response.data.item;
      } catch (error) {
        this.serverError = "Failed to fetch category data.";
      }
    },
    async submit() {
      this.errors = {};
      this.serverError = null;
      this.loading = true;
      const uuid = this.$route.params.uuid;
      try {
        const response = await this.$axios.put(`/fishbone-category/${uuid}`, this.category);
        if (response.data.success) {
          toast.success("Fishbone Category updated successfully!");
          this.$router.push({ name: "FishboneCategoryIndex" });
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          toast.error("Validation failed. Please check the form.");
        } else {
          this.serverError = "An error occurred. Please try again.";
        }
      } finally {
        this.loading = false;
      }
    },
    async fetchProblemNotes(search) {
      try {
        const response = await this.$axios.get("get_problemnotes", {
          params: { search: this.search || "", limit: this.limit },
        });
        this.problemNotes = response.data || [];
      } catch (error) {
        toast.error("Failed to fetch problem notes. Please try again.");
      }
    },
    formatProblemNote(problemNote) {
      if (problemNote) {
        const companyName = problemNote.company?.name || "No Company Name";
        this.category.problem_note_id = problemNote.id;
        return `${problemNote.name || "No Name"} -- ${companyName}`;
      }
      return "No Problem Note Data";
    },
    resetForm() {
      this.errors = {};
      this.serverError = null;
      if (this.$refs.form) {
        this.$refs.form.reset();
      }
      this.fetchCategory();
    },
  },
};
</script>
