<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Preventive Service List</span></v-col>
        <v-col cols="8" class="d-flex justify-end">
          <v-btn
            @click="createPreventiveService"
            class="primary-color"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
              </template>
              <span>Add New</span>
            </v-tooltip>
          </v-btn>

          <v-badge :content="trashedCount" color="red" overlap>
            <v-btn
              @click="viewTrash"
              color="red"
              icon
              class="ml-2"
              style="width: 40px; height: 40px"
            >
              <v-tooltip location="top" activator="parent">
                <template v-slot:activator="{ props }">
                  <v-icon v-bind="props" style="font-size: 20px">
                    mdi-trash-can-outline
                  </v-icon>
                </template>
                <span>View Trashed</span>
              </v-tooltip>
            </v-btn>
          </v-badge>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
            <div class="calendar-header">
              <v-btn
               @click="changeMonth(-1)"
                class="primary-color"
                icon
                style="width: 40px; height: 40px"
              >
                <v-tooltip location="top" activator="parent">
                  <template v-slot:activator="{ props }">
                    <v-icon v-bind="props" style="font-size: 20px">mdi-arrow-left</v-icon>
                  </template>
                  <span>Previous Month And Year</span>
                </v-tooltip>
              </v-btn>
              <h3>{{ currentMonthName }} {{ currentYear }}</h3>
              <v-btn
                @click="changeMonth(1)"
                class="primary-color"
                icon
                style="width: 40px; height: 40px"
              >
                <v-tooltip location="top" activator="parent">
                  <template v-slot:activator="{ props }">
                    <v-icon v-bind="props" style="font-size: 20px">mdi-arrow-right</v-icon>
                  </template>
                  <span>Next Month And Year</span>
                </v-tooltip>
              </v-btn>
            </div>
            <table>
              <thead>
                <tr>
                  <th>Sun</th>
                  <th>Mon</th>
                  <th>Tue</th>
                  <th>Wed</th>
                  <th>Thu</th>
                  <th>Fri</th>
                  <th>Sat</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(week, weekIndex) in calendarDays" :key="weekIndex">
                  <td v-for="(day, dayIndex) in week" :key="dayIndex"
                      :class="['date-cell', { 
                        'empty': !day, 
                        'selected': day && isSelected(day),
                        'active': day && isActiveDate(day),
                        'disabled': day && !isPreselectedDate(day)  // Disabled if not preselected
                      }]"
                      @click="selectDate(day)"
                      :disabled="!isPreselectedDate(day)">  <!-- Disabled click if not preselected -->
                    <span v-if="day">{{ day }}</span>
                    <div v-if="day && getEventCountForDay(day) > 0" class="value-badge-count">
                      {{ getEventCountForDay(day) }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <div id="selectedDate">{{ selectedDateMessage }}</div>
         
        </v-col>
      </v-row>
    </v-card-title>

    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :items="serverItems"
      :dateRange="dateRange"
      :items-length="totalItems"
      :loading="loading"
      item-value="created_at"
      loading-text="Loading... Please wait"
      @update:options="loadItems"
    >
      <template v-slot:item.actions="{ item }">
        <template v-if="item.technician_status && item.technician_status == 'Acknowledge'">
          <v-icon @click="showConfirmDTechnicianPreventiveServiceAcknowledge(item.detail_id)" class="mr-2"
            >mdi-check-outline</v-icon
          >
        </template>
        <template v-else-if="item.technician_status && item.technician_status == 'Acknowledged'">
          <v-icon @click="PreventiveServiceStart(item.detail_id)" class="mr-2"
            >mdi-clock-start</v-icon
          >
        </template>
        <template v-else-if="item.technician_status && item.technician_status == 'Start Service'">
          <v-icon @click="PreventiveServiceStartDetails(item.detail_id)" class="mr-2"
            >mdi-note-text-outline</v-icon
          >
        </template>
        <template v-else-if="item.technician_status && item.technician_status == 'Done'">
          <v-icon class="mr-2">empty</v-icon>
        </template>
        <template v-else-if="item.technician_status && item.technician_status == 'Cancel'">
          <v-icon @click="AssignToTechnicianPreventiveService(item.uuid)" class="mr-2"
            >mdi-account-outline</v-icon
          >
        </template>
        <template v-else>
          <v-icon @click="AssignToTechnicianPreventiveService(item.uuid)" class="mr-2"
            >mdi-account-outline</v-icon
          >
        </template>
        <v-icon @click="detailsList(item.uuid)" color="blue" class="mr-2"
          >mdi-eye</v-icon
        >
        <v-icon @click="editPreventiveService(item.uuid)" color="green" class="mr-2"
          >mdi-pencil</v-icon
        >
        <v-icon @click="showConfirmDialog(item.uuid)" color="red"
          >mdi-delete</v-icon
        >
      </template>
    </v-data-table-server>

    <ConfirmDialog
      :dialogName="dialogName"
      v-model:modelValue="dialog"
      :onConfirm="confirmDelete"
      :onCancel="() => (dialog = false)"
    />

    <ConfirmDialogAcknowledged
      :dialogName="dialogNameAcknowledged"
      v-model:modelValue="dialog_acknowledged"
      :onConfirm="TechnicianPreventiveServiceAcknowledge"
      :onCancel="() => (dialog_acknowledged = false)"
    />
  </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
import ConfirmDialog from "../../Components/ConfirmDialog.vue";
import ConfirmDialogAcknowledged from "../../Components/ConfirmDialogAcknowledged.vue";

export default {
  components: { ConfirmDialog, ConfirmDialogAcknowledged },
  data() {
    return {
      currentDate: new Date(),  // Initial current date
      preselectedData: '',
      selectedDate: null,
      selectedDates: [], 
      clickedDate: null,
      dialogName: "Are you sure you want to delete this Service?",
      dialogNameAcknowledged: "Are you sure you want to Acknowledge?",
      itemsPerPage: 10,
      headers: [
        { title: "DateTime", key: "date_time", sortable: true },
        { title: "Machine Name", key: "mechine_assing.name", sortable: false },
        { title: "Machine Code", key: "mechine_assing.machine_code", sortable: false },
        { title: "Service Status", key: "service_status", sortable: true },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      totalItems: 0,
      dialog: false,
      trashedCount: 0,
      loading: true,
      dialog_acknowledged: false,
      selectedId: null,
      selectedDetialId: null,
      dateRange: null,
      arrayEvents: [],
    };
  },
  computed: {
      currentYear() {
        return this.currentDate.getFullYear();  // Current year
      },
      currentMonth() {
        return this.currentDate.getMonth();  // Current month (0-11)
      },
      currentMonthName() {
        return this.currentDate.toLocaleString('default', { month: 'long' });  // Full month name (e.g., "January")
      },
      calendarDays() {
        const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay(); // Start day of the month (0-6)
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();  // Total days in the month
        const weeks = [];
        let week = [];
        let dayCount = 1;
  
        // Fill the calendar with days of the month, starting from the correct start day
        for (let i = 0; i < 6; i++) {
          week = [];
          for (let j = 0; j < 7; j++) {
            if (i === 0 && j < startDay || dayCount > daysInMonth) {
              week.push(null);  // Empty cell for days before the start of the month or after the end
            } else {
              week.push(dayCount);  // Add the actual day number
              dayCount++;
            }
          }
          weeks.push(week);  // Add the filled week to the calendar
          if (dayCount > daysInMonth) break;
        }
        return weeks;
      },
      selectedDateMessage() {
        if (!this.selectedDate) {
          return;
        }
        const formattedDate = this.formatDate(this.selectedDate);
      }
  },
  methods: {
    changeMonth(direction) {
      this.currentDate = new Date(this.currentYear, this.currentMonth + direction, 1); 
    },
    formatDate(day) {
      const date = new Date(this.currentYear, this.currentMonth, day);
      return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    },
    selectDate(day) {
      if (!day || !this.isPreselectedDate(day)) return;  
      this.selectedDate = day; 
      this.dateRange = this.formatDate(this.selectedDate);   
    },
    isSelected(day) {
      return this.selectedDate === day;  // Check if the day is selected
    },
    isActiveDate(day) {
      const formattedDate = this.formatDate(day);
      return this.preselectedData[formattedDate] !== undefined;  // Check if the date is in preselectedData
    },
    isPreselectedDate(day) {
      const formattedDate = this.formatDate(day);
      return this.preselectedData[formattedDate] !== undefined;  // Check if the date is preselected
    },
    getEventCountForDay(day) {
      const formattedDate = this.formatDate(day);
      return this.preselectedData[formattedDate] || 0;
    },
    async fatchDate(){
      try {
        const response = await this.$axios.get("/get_preventivedates");
        this.preselectedData = response.data;
      } catch (error) {
        console.error("Error fetching trashed count:", error);
      }
    },
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        const response = await this.$axios.get("/preventive-service", {
          params: { page, itemsPerPage, sortBy: sortKey, sortOrder, dateRange: this.dateRange || '' },
        });
        console.log(response.data);
        
        this.serverItems = response.data.items || [];
        this.totalItems = response.data.total || 0;
        this.fetchTrashedPreventiveServiceCount();
      } catch (error) {
        console.error("Error loading items:", error);
      } finally {
        this.loading = false;
      }
    },
    async fetchTrashedPreventiveServiceCount() {
      try {
        const response = await this.$axios.get("/preventive-service/trashed-count");
        this.trashedCount = response.data.trashedCount || 0;
      } catch (error) {
        console.error("Error fetching trashed count:", error);
      }
    },
    createPreventiveService() {
      this.$router.push({ name: "PreventiveServiceCreate" });
    },
    detailsList(uuid) {
      this.$router.push({ name: "PreventiveServiceDetailsList", params: { uuid } });
    },
    viewTrash() {
      this.$router.push({ name: "PreventiveServiceTrash" });
    },
    editPreventiveService(uuid) {
      this.$router.push({ name: "PreventiveServiceEdit", params: { uuid } });
    },
    AssignToTechnicianPreventiveService(uuid) {
      this.$router.push({ name: "AssignToTechnicianPreventiveService", params: { uuid } });
    },
    PreventiveServiceStart(detail_id) {
      this.$router.push({ name: "PreventiveServiceStart", params: { detail_id } });
    },
    PreventiveServiceStartDetails(detail_id) {
      this.$router.push({ name: "PreventiveServiceStartDetails", params: { detail_id } });
    },
    showConfirmDialog(id) {
      this.selectedId = id;
      this.dialog = true;
    },
    showConfirmDTechnicianPreventiveServiceAcknowledge(id) {
      this.selectedDetialId = id;
      this.dialog_acknowledged = true;
    },
    async confirmDelete() {
      this.dialog = false;
      try {
        await this.$axios.delete(`/preventive-service/${this.selectedId}`);
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
        toast.success("Preventive Service deleted successfully!");
      } catch (error) {
        console.error("Error deleting service:", error);
        toast.error("Failed to delete Preventive Service.");
      }
    },
    async TechnicianPreventiveServiceAcknowledge() {
      this.dialog_acknowledged = false;
      try {
        await this.$axios.put(`/preventive-service/${this.selectedDetialId}/technician-preventive-service-acknowledge`);
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
        toast.success("Service acknowledged successfully!");
      } catch (error) {
        console.error("Error acknowledging service:", error);
        toast.error("Failed to acknowledge service.");
      }
    },
  },
  watch: {
    dateRange: {
      handler() {
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
      },
      deep: true,
    },
  },
  created() {
    this.fatchDate();
    this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
    this.fetchTrashedPreventiveServiceCount();
  },
};
</script>

<style scoped>

  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 20px;
  }
  .calendar-header button {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 5px;
  }
  .calendar-header h3 {
    margin: 0;
    font-size: 20px;
  }
  table {
    border-collapse: collapse;
    width: 100%;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    position: relative;
  }
  th {
    background-color: #f4f4f4;
  }
  td {
    height: 80px;
  }
  td.selected {
    background-color: #4caf50;
    color: white;
    font-weight: bold;
  }
  td.active {
    background-color: #ff9800; /* Active date color */
    color: white;
    font-weight: bold;
  }
  td:hover {
    background-color: #e0f7fa;
  }
  td.disabled {
    background-color: #f9f9f9;
    cursor: not-allowed;
  }
  .empty {
    background-color: #f9f9f9;
    cursor: default;
  }
  .value-badges {
    position: absolute;
    bottom: 5px;
    right: 5px;
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
  }
  .value-badge {
    font-size: 10px;
    background-color: #ff9800;
    color: white;
    padding: 2px 5px;
    border-radius: 5px;
    white-space: nowrap;
  }
  #selectedDate {
    margin-top: 20px;
    font-size: 18px;
    color: #333;
  }
  .value-badge-count {
    font-size: 14px;
    background: #7e8bff;
    border-radius: 6px;
    font-weight: 400;
}
  </style>
  