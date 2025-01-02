<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Breakdown Service List</span></v-col>
        <v-col cols="8" class="d-flex justify-end">
          <v-date-input
            v-model="dateRange"
            label="Select Date range"
            multiple="range"
            prepend-icon=""
            style="padding-right: 10px;"
          ></v-date-input>
          <v-btn
            @click="createBreakdownService"
            color="primary"
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
    </v-card-title>

    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :dateRange="formattedDateRangeString"
      :items="serverItems"
      :items-length="totalItems"
      :loading="loading"
      item-value="created_at"
      loading-text="Loading... Please wait"
      @update:options="loadItems"
    >
    <template v-slot:item.actions="{ item }">
          <template v-if="item.technician_status && item.technician_status == 'Acknowledge'">
              <v-icon @click="showConfirmDTechnicianBreakdownServiceAcknowledge(item.detail_id)" class="mr-2"
                >mdi-check-outline</v-icon
            >
          </template>
          <template v-else-if="item.technician_status && item.technician_status == 'Acknowledged'">
              <v-icon @click="BreakdownServiceStart(item.detail_id)" class="mr-2"
                >mdi-clock-start</v-icon
            >
          </template>
          <template v-else-if="item.technician_status && item.technician_status == 'Start Service'">
              <v-icon @click="BreakdownServiceStartDetails(item.detail_id)" class="mr-2"
                >mdi-note-text-outline</v-icon
            >
          </template>
          <template v-else-if="item.technician_status && item.technician_status == 'Done'">
              <v-icon class="mr-2">empty</v-icon>
          </template>
          <template v-else-if="item.technician_status && item.technician_status == 'Cancel'">
            <v-icon @click="AssignToTechnicianBreakdownService(item.uuid)" class="mr-2"
                >mdi-account-outline</v-icon
            >
          </template>
          <template v-else>
            <v-icon @click="AssignToTechnicianBreakdownService(item.uuid)" class="mr-2"
                >mdi-account-outline</v-icon
            >
          </template>
            
            <v-icon @click="editBreakdownService(item.uuid)" color="green" class="mr-2"
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
      :onCancel="() => { dialog = false; }"
    />

    <ConfirmDialogAcknowledged
      :dialogName="dialogNameAcknowledged"
      v-model:modelValue="dialog_acknowledged"
      :onConfirm="TechnicianBreakdownServiceAcknowledge"
      :onCancel="() => { dialog_acknowledged = false; }"
    />
  </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
import ConfirmDialog from "../../Components/ConfirmDialog.vue";
import ConfirmDialogAcknowledged from "../../Components/ConfirmDialogAcknowledged.vue";

export default {
  components: {
    ConfirmDialog,
    ConfirmDialogAcknowledged,
  },
  data() {
    return {
      dialogName: "Are you sure you want to delete this Service?",
      dialogNameAcknowledged: "Are you sure you want to Acknowledge?",
      dateRange: null,
      itemsPerPage: 10,
      headers: [
        { title: "DateTime", key: "date_time", sortable: true },
        { title: "Machine Name", key: "mechine_assing.name", sortable: false },
        { title: "Machine Code", key: "mechine_assing.machine_code", sortable: false },
        { title: "Service Status", key: "service_status", sortable: true },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      loading: true,
      totalItems: 0,
      dialog: false,
      selectedId: null,
      trashedCount: 0,
      dialog_acknowledged: false,
      selectedDetialId: null,
    };
  },
  computed: {
    formattedDateRangeString() {
      if (Array.isArray(this.dateRange) && this.dateRange.length > 0) {
        // Loop through the date range array and format each date
        const formattedDates = this.dateRange.map(date => new Date(date).toISOString());
        
        // Join the dates into a comma-separated string
        return formattedDates.join(',');
      }
      return ""; // Return an empty string if no date range is selected
    },
  },
  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";

      try {
        const response = await this.$axios.get("/breakdown-service", {
          params: {
            page,
            itemsPerPage,
            sortBy: sortKey,
            sortOrder,
            dateRange: this.formattedDateRangeString, // Pass formatted date range as search parameter
          },
        });
        console.log(response)
        
        this.serverItems = response.data.items || [];
        this.totalItems = response.data.total || 0;
        this.fetchTrashedBreakdownServiceCount();
      } catch (error) {
        console.error("Error loading items:", error.response?.data || error.message);
      } finally {
        this.loading = false;
      }
    },
    createBreakdownService() {
      this.$router.push({ name: "BreakdownServiceCreate" });
    },
    viewTrash() {
      this.$router.push({ name: "BreakdownServiceTrash" });
    },
    editBreakdownService(uuid) {
      this.$router.push({ name: "BreakdownServiceEdit", params: { uuid } });
    },
    AssignToTechnicianBreakdownService(uuid) {
      this.$router.push({ name: "AssignToTechnicianBreakdownService", params: { uuid } });
    },
    BreakdownServiceStart(detail_id) {
      this.$router.push({ name: "BreakdownServiceStart", params: { detail_id } });
    },
    BreakdownServiceStartDetails(detail_id) {
      this.$router.push({ name: "BreakdownServiceStartDetails", params: { detail_id } });
    },
    showConfirmDialog(id) {
      this.selectedId = id;
      this.dialog = true;
    },
    showConfirmDTechnicianBreakdownServiceAcknowledge(id) {
      this.selectedDetialId = id;
      this.dialog_acknowledged = true;
    },
    async confirmDelete() {
      this.dialog = false;
      try {
        await this.$axios.delete(`/breakdown-service/${this.selectedId}`);
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
        toast.success("Breakdown Service deleted successfully!");
      } catch (error) {
        toast.error("Failed to delete Breakdown Service.");
      }
    },
    async TechnicianBreakdownServiceAcknowledge() {
      this.dialog_acknowledged = false;
      try {
        await this.$axios.put(`/breakdown-service/${this.selectedDetialId}/technician-breakdown-service-acknowledge`);
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
        toast.success("Acknowledged successfully!");
      } catch (error) {
        toast.error("Failed to acknowledge.");
      }
    },
    async fetchTrashedBreakdownServiceCount() {
      try {
        const response = await this.$axios.get("/breakdown-service/trashed-count");
        this.trashedCount = response.data.trashedCount || 0;
      } catch (error) {
        console.error("Error fetching trashed count:", error.response?.data || error.message);
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
    this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
    this.fetchTrashedBreakdownServiceCount();
  },
};
</script>

<style scoped>
/* Optional styles */
</style>
