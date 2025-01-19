<template>
  <div>
    <!-- Table with Calendar Data -->
    <table class="table table-bordered">
      <!-- Header -->
      <tr class="text-center table_header">
        <th colspan="60" :style="{ backgroundColor: colors.lightBlue }">Nov-24</th>
      </tr>
      <!-- Date Row -->
      <tr class="text-center">
        <th v-for="(date, index) in dates" :key="index" :colspan="2" :style="{ backgroundColor: date.color }">
          {{ date.label }}
        </th>
      </tr>
      <!-- Dynamic Data Rows -->
      <tr v-for="(row, rowIndex) in rowData" :key="rowIndex" class="text-center">
        <td v-for="(cell, cellIndex) in row" :key="cellIndex" :style="{ backgroundColor: cell.color || 'transparent' }">
          {{ cell.value !== undefined && cell.value !== null ? cell.value : "--" }}
        </td>
      </tr>

      <!-- Summary Row (Sum of Each Column) -->
      <tr class="text-center font-weight-bold">
        <td v-for="(sum, index) in columnSums" :key="index" :style="{ backgroundColor: 'lightgray' }">
          {{ sum }}
        </td>
      </tr>
    </table>

    <!-- Machine Type Line Section -->
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
          <v-col cols="4">
            <v-date-input
              v-model="datePicker"
              label="Select a date"
              max-width="368"
            ></v-date-input>
          </v-col>
        </v-row>
        
      </v-card-title>

      <!-- Data Table Section -->
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items="serverItems"
        :datePicker="datePicker" 
        :items-length="totalItems"
        :loading="loading"
        item-value="created_at"
        loading-text="Loading... Please wait"
        @update:options="loadItems"
        class="custom-table"
      >
        <template v-slot:body="{ items }">
          <tr v-for="(item, idx) in items" :key="idx">
            <td>{{ idx + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.mc_sum }}</td>
          </tr>
        </template>
        <template v-slot:body.append>
          <tr class="footerTable">
            <td>Total MC Sum</td>
            <td>{{ totalMcSum }}</td>
            <td colspan="4"></td>
          </tr>
        </template>
      </v-data-table-server>
    </v-card>
  </div>
</template>

<script>
import { toast } from "vue3-toastify";
import ConfirmDialog from "../../Components/ConfirmDialog.vue";

export default {
  components: { ConfirmDialog },
  data() {
    return {
      datePicker: null,
      colors: {
        lightBlue: "#d6dce4",
        lightApricot: "#f7caac",
      },
      line: null,
      itemsPerPage: 15,
      headers: [
        { title: "No", value: "sr_no", sortable: true },
        { title: "Type", value: "name", sortable: true },
        { title: "Total", value: "mc_sum", sortable: true },
      ],
      totalMcSum: 0,
      serverItems: [],
      lines: [],
      loading: true,
      totalItems: 0,
      dates:[],
      rowData: [
        [
          { value: 128, color: "#f7caac" },
          { value: -19, color: "#f7caac" },
          { value: 106 },
          { value: 3 },
          { value: 89 },
          { value: -20 },
          { value: 91 },
          { value: 18 },
          { value: 129, color: "#9cc2e5" },
          { value: -20, color: "#9cc2e5" },
          { value: 97 },
          { value: 12 },
          { value: 130 },
          { value: -21 },
          { value: 103, color: "#f7caac" },
          { value: 6, color: "#f7caac" },
          { value: 126 },
          { value: -17 },
          { value: 118 },
          { value: -9 },
          { value: 122 },
          { value: -13 },
          { value: 119 },
          { value: -10 },
          { value: 125 },
          { value: -16 },
          { value: 122 },
          { value: -13 },
          { value: 111, color: "#f7caac" },
          { value: -2, color: "#f7caac" },
          { value: 100 }
        ],
        [
          { value: 100, color: "#f7caac" },
          { value: -15, color: "#f7caac" },
          { value: 90 },
          { value: 2 },
          { value: 80 },
          { value: -10 },
          { value: 85 },
          { value: 5 },
          { value: 110, color: "#9cc2e5" },
          { value: -12, color: "#9cc2e5" },
          { value: 98 },
          { value: 8 },
          { value: 120 },
          { value: -18 },
          { value: 101, color: "#f7caac" },
          { value: 7, color: "#f7caac" },
          { value: 115 },
          { value: -14 },
          { value: 108 },
          { value: -6 },
          { value: 112 },
          { value: -8 },
          { value: 113 },
          { value: -9 },
          { value: 116 },
          { value: -13 },
          { value: 111 },
          { value: -11 },
          { value: 99, color: "#f7caac" },
          { value: -5, color: "#f7caac" },
        ],
      ],
    };
  },
  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        // const response = await this.$axios.get("/machine-requisition/machine-calender", {
        //   params: {
        //     page,
        //     itemsPerPage,
        //     sortBy: sortKey,
        //     sortOrder,
        //     line: this.line,
        //     datePicker:this.datePicker
        //   },
        // });
      const response = await this.$axios.get("/machine-requisition/machine-calender");
          console.log(response.data);
          
        // this.serverItems = response.data.items || [];
        // this.totalItems = response.data.total || 0;
        // this.totalMcSum = response.data.total_sum;
      } catch (error) {
        console.log(error)
        toast.error("Failed to load items. Please try again.");
      } finally {
        this.loading = false;
      }
    },
    async fetchLines(search) {
      try {
        const response = await this.$axios.get("/machine-requisition/lines", {
          params: { search, limit: 10 },
        });
        this.lines = response.data || [];
      } catch (error) {
        toast.error("Failed to fetch lines.");
      }
    },
    formatLine(line) {
      if (!line) return "No Line Data";
      const lineName = line.name || "No Line Name";
      const companyName = line.company?.name || "No Company";
      return `${lineName} -- ${companyName}`;
    },
  },
  watch: {
    datePicker: {
      handler() {
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
      },
      deep: true,
    },
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
  computed: {
    dates() {
      const currentMonth = new Date().getMonth();
      const currentYear = new Date().getFullYear();
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      const dates = [];
      for (let i = 1; i <= daysInMonth; i++) {
        // Ensure `i` is treated as a valid date number
        let color = i % 2 === 0 ? this.colors.lightBlue : this.colors.lightApricot;
        dates.push({ label: i.toString(), color });
      }
      return dates;
  },
    columnSums() {
      const sums = Array(this.rowData[0]?.length || 0).fill(0);
      this.rowData.forEach((row) => {
        row.forEach((cell, index) => {
          sums[index] += (cell.value !== undefined && cell.value !== null) ? cell.value : 0;
        });
      });
      return sums;
    },
  }
};
</script>

<style scoped>
.custom-table ::v-deep thead,
.footerTable {
  background: #d6dce4;
  border-radius: 0;
}
</style>
