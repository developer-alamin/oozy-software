<template>
  <div>
    <!-- Machine Type Line Section -->
    <v-card>
      <v-card-title class="pt-5">
        <v-row class="align-items-center">
          <v-col cols="3">
            <span>Machine Type Line Wise </span>
          </v-col>
          <v-col cols="4" class="text-center ms-auto">
            <v-autocomplete
                  v-model="factory"
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
      factory: null,
      itemsPerPage: 15,
      headers: [
        { title: "No", value: "sr_no", sortable: true },
        { title: "Type", value: "name", sortable: true },
        { title: "Total", value: "mc_sum", sortable: true },
      ],
      totalMcSum: 0,
      serverItems: [],
      factories: [],
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
    async loadItems({selectFactory,month,year }) {
        this.loading = true;
        try {
            const response = await this.$axios.get("/machine-requisition/machine-calender", {
                params: {
                    factory: selectFactory || '',
                    month:month || this.selectedMonth,
                    year:year || this.selectedYear
                }
            }); 
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
    async onChangeFactory() {
        if (!this.factory) {
          this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
        }
        try {
            const selectFactory = this.factory;
            
            const page = 1;  // Set the default page or get it from somewhere
            const itemsPerPage = this.itemsPerPage;
            const sortBy = [];  // Set the default sortBy or pass it from elsewhere

            // Call loadItems with the correct arguments
            await this.loadItems({ page, itemsPerPage, sortBy, selectFactory });

        } catch (error) {
            console.log(error);
        }


    },
    async fetchFactories(search) {
      try {
        const response = await this.$axios.get("/get_factories", {
          params: { search:this.search, limit: 10 },
        });
        this.factories = response.data || [];
      } catch (error) {
        toast.error("Failed to fetch lines.");
      }
    },
    formatFactory(factory) {
      if (!factory) return "No Factory Data";
      const FactoryName = factory.name || "No Factory Name";
      const companyName = factory.company?.name || "No Company";
      //this.factory = factory.id;
      return `${FactoryName} -- ${companyName}`;
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
