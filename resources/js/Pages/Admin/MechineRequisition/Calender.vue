<template>
    <v-card>
      <v-card-title class="pt-5">
        <v-row class="align-items-center">
          <v-col cols="4">
            <span>Machine Type Line Wise</span>
          </v-col>
          <v-col cols="4" class="text-center ms-auto">
            <v-autocomplete
              v-model="line"
              :items="lines"
              item-value="id"
              :item-title="formatLine"
              outlined
              clearable
              density="comfortable"
              @update:search="fetchLines"
            >
            
              <template v-slot:label>
                Select Line <span style="color: red">*</span>
              </template>
            </v-autocomplete>
          </v-col>
        </v-row>
      </v-card-title>
  
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :line="line"
        :items="serverItems"
        :items-length="totalItems"
        :loading="loading"
        item-value="created_at"
        loading-text="Loading... Please wait"
        @update:options="loadItems"
      >
        <template v-slot:body.append>
        <tr class="bg-primary">
          <td>Total MC Sum  </td>
          <td>
            {{totalMcSum}}
          </td>
          <td colspan="4"></td>
        </tr>
      </template>
    </v-data-table-server>
    </v-card>
  
  </template>
  
  <script>

    
  import { toast } from "vue3-toastify";
  import ConfirmDialog from "../../Components/ConfirmDialog.vue";
  
  export default {
    components: {
      ConfirmDialog,
    },
    data() {
      return {
        line: null,
        requisitionValid: false,
        itemsPerPage: 15,
        headers: [
          
          { title: "Type", value: "name", sortable: true },
          { title: "Total", value: "mc_sum", sortable: true },
        ],
        totalMcSum: 0,
        serverItems: [],
        lines: [],
        loading: true,
        totalItems: 0,
        dialog: false,
        errors: {}, // Stores validation errors
        rules: {
          required: (value) => !!value || "Required.",
        },
      };
    },
    methods: {
      async loadItems({ page, itemsPerPage, sortBy }) {
        this.loading = true;
        const sortOrder = sortBy.length ? sortBy[0].order : "desc";
        const sortKey = sortBy.length ? sortBy[0].key : "created_at";
        try {
          const response = await this.$axios.get("/machine-requisition/machine-calender", {
            params: {
              page,
              itemsPerPage,
              sortBy: sortKey,
              sortOrder,
              line: this.line,
            },
          });

          console.log(response.data);

          this.serverItems = response.data.items || [];
          this.totalItems = response.data.total || 0;
          this.totalMcSum = response.data.total_sum;

        } catch (error) {
          console.error("Error loading items:", error);
          toast.error("Failed to load items. Please try again.");
        } finally {
          this.loading = false;
        }
      },
      async fetchLines(search) {
        try {
          const response = await this.$axios.get(`/machine-requisition/lines`, {
            params: { search, limit: 10 },
          });
          this.lines = response.data || [];
        } catch (error) {
          console.error("Error fetching factories:", error);
          toast.error("Failed to fetch factories.");
        }
      },
      formatLine(line) {
        if (!line) return "No Factory Data";
        const lineName = line.name || "No Line Name";
        const companyName = line.company?.name || "No Company";
        return `${lineName} -- ${companyName}`;
      },
    },
    watch: {
        line: {
            handler() {
                this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
            },
               immediate: true,
            },
    },
    created() {
      this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
    },
  };
  </script>
  
  <style scoped>
  /* Add styles if necessary */
  </style>
  