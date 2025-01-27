<template>
    <div>
      <!-- Machine Type Line Section -->
      <v-card>
        <v-card-title class="pt-5">
          <v-row class="align-items-center">
            <v-col cols="3" class="">
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
                  Select Factory 
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
            <v-col cols="2">
            <v-autocomplete
              v-model="unit_id"
              :items="units"
              item-value="id"
              item-title="name"
              outlined
              clearable
              density="comfortable"
              @update:model-value="onChangeUnit"
              @update:search="fetchUnits"
              :disabled="!floor_id"
            >
            <template v-slot:label>
              Select Unit 
            </template>
          </v-autocomplete>
          </v-col>
            <v-col cols="2">
            <v-autocomplete
               v-model="selectedMonth"
               :items="monthItems"
               item-value="value"
               item-title="name"
               label="Select Month"
               outlined
               density="comfortable"
               @update:model-value="getMonth"
            ></v-autocomplete>
          </v-col>
          <v-col cols="2">
            <v-autocomplete
              v-model="selectedYear"
              :items="years"
              label="Select Year"
              outlined
              density="comfortable"
              @update:model-value="getYear"
            ></v-autocomplete>
          </v-col>
          </v-row>
        </v-card-title>
        <div class="table_data">
          <div v-html="RowData"></div>
         </div>
      </v-card>
    </div>
  </template>
  
  
  <script>
  
  import { toast } from "vue3-toastify";
  import ConfirmDialog from "../../Components/ConfirmDialog.vue";
  
  import $ from 'jquery';


  export default {
    name: "MonthYearPicker",
    props: {
      startYear: {
        type: Number,
        default: 2000, // Default start year
      },
      endYear: {
        type: Number,
        default: new Date().getFullYear() + 50, // Default end year
      },
    },
    data() {
      return {
        monthItems: [
          { name: "January", value: 1 },
          { name: "February", value: 2 },
          { name: "March", value: 3 },
          { name: "April", value: 4 },
          { name: "May", value: 5 },
          { name: "June", value: 6 },
          { name: "July", value: 7 },
          { name: "August", value: 8 },
          { name: "September", value: 9 },
          { name: "October", value: 10 },
          { name: "November", value: 11 },
          { name: "December", value: 12 },
      ],
      years: [],
      selectedMonth: new Date().getMonth() + 1, // Selected month value
      selectedYear: new Date().getFullYear(), // Selected year value
        search:'',
        selectedDate: '',
        line: null,
        itemsPerPage: 15,
        headers: [
          { title: "No", value: "sr_no", sortable: true },
          { title: "Type", value: "name", sortable: true },
          { title: "Total", value: "mc_sum", sortable: true },
        ],
        units:[],
        unit_id:null,
        floors: [],
        floor_id: null,
        factory_id:null,
        factories: [],
        totalMcSum: 0,
        serverItems: [],

        lines: [],
        loading: true,
        totalItems: 0,
        dates:[],
        RowData : "",
       
      };
    },
    methods: {
      populateYears() {
        for (let year = this.startYear; year <= this.endYear; year++) {
          this.years.push(year);
        }
      },
      async loadItems({month,year,factory,floor,unit  }) {
          this.loading = true;
          try {
              const response = await this.$axios.get("/machine-requisition/machine-change", {
                  params: {
                    month:month || this.selectedMonth,
                    year:year || this.selectedYear,
                    factory:factory || this.factory_id,
                    floor:floor || this.floor_id,
                    unit:unit || this.unit_id
                  }
              });

              //console.log(response.data);
              
              this.RowData = response.data;
          } catch (error) {
              console.log(error);
              toast.error("Failed to load items. Please try again.");
          } finally {
              this.loading = false;
          }
      },
      async getMonth(){
        try {
          const newSelectMonth = this.selectedMonth;
          await this.loadItems({
            month: newSelectMonth, 
          });

        } catch (error) {
          console.error("Error loading items:", error);
        }
      },
      async getYear(){
        try {
          const newSelectYear = this.selectedYear;
          await this.loadItems({
            year: newSelectYear, 
          });

        } catch (error) {
          console.error("Error loading items:", error);
        }
      },
      async onChangeUnit(){
        if (!this.unit_id) {
            return ;
        }
        this.loadItems({ unit:this.unit_id });
      },
      async onChangeFloor(){
        if (!this.floor_id) return;
        try {
          this.loadItems({ floor:this.floor_id });
          const response = await this.$axios.get("/FloorByUnits", {
            params: {
              id: this.floor_id,
            },
          });
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
          this.loadItems({ factory:this.factory_id });

          const response = await this.$axios.get("/factoryByFloors", {
            params: {
              id: this.factory_id,
            },
          });
          this.floors = response.data;
          console.log(this.floors);
          
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
  
    },
    created() {
      this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
    },
    mounted() {
    this.populateYears();
  },
  computed: {
    selectedDate() {
      if (this.selectedMonth && this.selectedYear) {
        const monthName = this.monthItems.find(
          (month) => month.value === this.selectedMonth
        )?.name;
        return {
          month: this.selectedMonth,
          monthName,
          year: this.selectedYear,
        };
      }
      return null;
    },
  },
  };
  </script>
  
  <style scoped>
  .custom-table ::v-deep thead,
  .footerTable {
    background: #d6dce4;
    border-radius: 0;
  }
  </style>
  