<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row class="">
        <v-col cols="3">
          <v-autocomplete
          v-model="factory_id"
          :items="factories"
          item-value="id"
          :item-title="formatFactory"
          outlined
          clearable
          density="comfortable"
          @update:model-value="onChangeFactory"
          @update:search="fetchFactories"
        >
          <template v-slot:label>
            Select Factory <span style="color: red">*</span>
          </template>
        </v-autocomplete>
        </v-col>
        <v-col cols="3">
          <v-autocomplete
            v-model="floor_id"
            :items="floors"
            item-value="id"
            item-title="name"
            outlined
            clearable
            density="comfortable"
            @update:search="fetchFloors"
            @update:model-value="onChangeFloor"
            :disabled="!factory_id"
          >
          <template v-slot:label>
            Select Floor
          </template>
        </v-autocomplete>
        </v-col>
        <v-col cols="3">
          <v-autocomplete
            v-model="unit_id"
            :items="units"
            item-value="id"
            item-title="name"
            outlined
            clearable
            density="comfortable"
            @update:search="fetchUnits"
            :disabled="!floor_id"
          >
          <template v-slot:label>
            Select Unit 
          </template>
        </v-autocomplete>
        </v-col>
        <v-col cols="3" >
          <v-btn @click="addRequisition" class="primary-color">
            <v-icon>mdi-plus</v-icon>
            <span class="ml-2">Add New Requisition</span>
          </v-btn>
        </v-col>
      </v-row>
    </v-card-title>
    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :search="search"
      :unit="unit_id"
      :items="serverItems"
      :items-length="totalItems"
      :loading="loading"
      item-value="created_at"
      loading-text="Loading... Please wait"
      @update:options="loadItems"
    >
    </v-data-table-server>
  </v-card>

  <v-dialog v-model="dialog" max-width="900">
    <v-card>
      <v-card-title>
        <span>Add New Requisition</span>
      </v-card-title>
      <v-card-text>
        <v-form ref="createRequisition" v-model="requisitionValid" @submit.prevent="createRequisition">
          <v-row>
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="requisition.line"
                :items="lines"
                item-value="id"
                item-title="name"
                label="Select Line"
                outlined
                clearable
                density="comfortable"
                :rules="[rules.required]"
                @update:model-value="fetchMachineTypes"
                @update:search="fetchLines"
                :error-messages="errors.line ? errors.line : ''"
              >
                <template v-slot:label>
                  Select Line <span style="color: red">*</span>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col>
              <v-date-input
                v-model="requisition.dateRange"
                label="Select Date Range"
                multiple="range"
                outlined
                clearable
                density="comfortable"
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="requisition.style"
                label="Style"
                type="text"
                density="comfortable"
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                readonly
                v-model="requisition.total"
                label="Total R M/C"
                type="text"
                density="comfortable"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12">
              <table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Machine Type</th>
                    <th>M/C</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- If requisition.types is not empty -->
                  <template v-if="requisition.types && requisition.types.length > 0">
                    <tr v-for="(item, index) in requisition.types" :key="index">
                      <td>{{ item.name }}</td>
                      <td>
                        <v-text-field
                            type="number"
                            v-model="item.mc"
                            :rules="[rules.required]"
                            label="Enter M/C"
                            outlined
                            clearable
                            density="comfortable"
                            @input="updateTotal"
                          >
                            <template v-slot:label>
                              Enter M/C<span style="color: red">*</span>
                            </template>
                          </v-text-field>

                      </td>
                    </tr>
                  </template>

                  <!-- If requisition.types is empty -->
                  <template v-else>
                    <tr>
                      <td colspan="2" style="text-align: center; color: red;">
                        No machine types available. Please add at least one type.
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>

            </v-col>
          </v-row>
          <v-row class="justify-end">
            <v-card-actions>
              <v-btn color="danger" @click="dialog = false">Cancel</v-btn>
              <v-btn 
               class="primary-color"
               :disabled="!requisitionValid || loading || requisition.types.length === 0"
               :loading="loading"
               type="submit"
               >Save</v-btn>
            </v-card-actions>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
  

  <v-card class="mt-2 d-none">
    <v-row>
    <v-col cols="12">
      <apexchart
        width="500px"
        type="bar"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </v-col>
  </v-row>
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
      chartOptions: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
            'United States', 'China', 'Germany'],
        },
        plotOptions: {
          bar: {
            horizontal: true, // This enables horizontal bars
            borderRadius: 4,
            borderRadiusApplication: 'end',
          },
        },
      },
      series: [
        {
          name: "series-22",
          data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380],
        },
      ],

      line:null,
      search:'',
      unit:null,
      requisitionValid: false,
      requisition: {
        line: null,
        dateRange: null,
        style: "",
        types: [],
        total: 0,
      },
      itemsPerPage: 15,
      headers: [
        { title: "Sr", key: "sr_number", sortable: true },
        { title: "Unit", key: "unit.name", sortable: true },
        { title: "Line", key: "name", sortable: true },
        { title: "A M/C", key: "total_sum", sortable: true },
      ],

      serverItems: [],
      totalItems: 0,
      loading: true,
      units:[],
      unit_id:null,
      floors: [],
      floor_id: null,
      lines: [],
      factory_id:null,
      factories: [],
      loading: true,
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
        const response = await this.$axios.get("/machine-requisition/index", {
          params: {
            page,
            itemsPerPage,
            sortBy: sortKey,
            sortOrder,
            unit: this.unit_id,
          },
        });

        this.serverItems = response.data.items || [];
        this.totalItems = response.data.total || 0;

        console.log(response.data);
       
      } catch (error) {
        console.error("Error loading items:", error);
        toast.error("Failed to load items. Please try again.");
      } finally {
        this.loading = false;
      }
    },
    createRequisition() {
      this.requisitionValid = true;
      if (!this.$refs.createRequisition.validate()) return;

      this.loading = true;

      const formData = new FormData();
      Object.entries(this.requisition).forEach(([key, value]) => {
        if (key === "types") {
          formData.append(key, JSON.stringify(value));
        } else if (key === "dateRange" && Array.isArray(value) && value.length > 0) {
          const formattedDateRange = value.map((date) => new Date(date).toISOString()).join(",");
          formData.append(key, formattedDateRange);
        } else {
          formData.append(key, value);
        }
      });

      this.$axios
        .post("/machine-requisition/store-requisition", formData)
        .then((response) => {
          if (response.data.success) {
            this.resetForm();
            toast.success("Requisition created successfully.");
            this.dialog = false;
            this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
          }
        })
        .catch((error) => {
          console.error("Error creating requisition:", error);
          toast.error("Failed to create requisition.");
        })
        .finally(() => {
          this.loading = false;
        });
    },
    addRequisition() {
      this.dialog = true;
    },
    async onChangeFloor(){
      if (!this.floor_id) return;
      try {
        const response = await this.$axios.get("/FloorByUnits", {
          params: {
            id: this.floor_id,
          },
        });
        console.log(response.data);
        
        this.units = response.data;
      } catch (error) {
          console.log(error);
      }
    },
    async onChangeFactory(newValue) {
      if (!this.factory_id) {
        return;
      }
      try {
        const response = await this.$axios.get("/factoryByFloors", {
          params: {
            id: this.factory_id,
          },
        });
        this.floors = response.data;
      } catch (error) {
        // Log the error or handle it appropriately
        console.error("Error fetching factory by floors:", error);
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
        this.factories = response.data;
      } catch (error) {
        console.error("Error fetching factories:", error);
      }
    },
    formatFactory(factory) {
      if (factory) {
          if (typeof factory === "number") {
            factory = this.factories.find((item) => item.id === factory);
          }
          if (factory) {
            const factoryName = factory.name || "No Factory Name";
            const userName = factory.company?.name || "No Company";
            return `${factoryName} -- ${userName}`;
          }
        }
        return "No Factory Data";

    },
    
    async fetchLines(search) {
      try {
        const response = await this.$axios.get(`/get-lines`, {
          params: { search, limit: 5 },
        });
        this.lines = response.data || [];
      } catch (error) {
        console.error("Error fetching lines:", error);
        toast.error("Failed to fetch lines.");
      }
    },
    async fetchMachineTypes() {
      if (!this.requisition.line) {
        this.requisition.types = [];
        return;
      }
      try {
        const response = await this.$axios.get(`/machine-requisition/machine-types`, {
          params: { line: this.requisition.line },
        });
        this.requisition.types = response.data.machine_types || [];
      } catch (error) {
        console.error("Error fetching machine types:", error);
        toast.error("Failed to fetch machine types.");
      }
    },
    updateTotal() {
      this.requisition.total = this.requisition.types.reduce((acc, item) => {
        const mc = parseFloat(item.mc) || 0;
        return acc + mc;
      }, 0);
    },
    formatLine(line) {
        if (!line) return;
        const lineName = line.name || "No Line Name";
        const companyName = line.company?.name || "No Company";
        this.line = line.id;
        return `${lineName} -- ${companyName}`;
      },
    resetForm() {
      if (this.$refs.createRequisition) {
        this.$refs.createRequisition.resetValidation(); // Reset the form via its ref if necessary
        this.requisition = {
          line: "",
          dateRange: "",
          style: '',
          types: [],
        };
        this.errors = {}; // Reset errors on form reset
      }

    
      
    },
  },
  watch: {
    unit_id: {
      handler() {
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
      },
      immediate: true,
    },
  },
  created() {
    this.fetchMachineTypes();
    this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
  },
};
</script>


<style scoped>
/* Add styles if necessary */
</style>
