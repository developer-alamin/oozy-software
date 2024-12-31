<template>
    <v-card>
      <v-card-title class="pt-5">
        <v-row>
          <v-col cols="4"><span>Trashed Machines</span></v-col>
          <v-col cols="8" class="d-flex justify-end">
            <v-text-field
              v-model="search"
              density="compact"
              label="Search"
              prepend-inner-icon="mdi-magnify"
              variant="solo-filled"
              class="mx-4"
              flat
              hide-details
              solo
              single-line
              clearable
            ></v-text-field>
  
            <v-btn
              @click="MechineIndex"
              color="primary"
              icon
              style="width: 40px; height: 40px"
            >
              <v-tooltip location="top" activator="parent">
                <template v-slot:activator="{ props }">
                  <v-icon v-bind="props" style="font-size: 20px">mdi-home</v-icon>
                </template>
                <span>Machine List</span>
              </v-tooltip>
            </v-btn>
          </v-col>
        </v-row>
      </v-card-title>
  
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :search="search"
        :items="serverItems"
        :items-length="totalItems"
        :loading="loading"
        item-value="created_at"
        loading-text="Loading... Please wait"
        @update:options="loadItems"
      >
        <template v-slot:item.model="{ item }">
          <span>{{ item.model || 'N/A' }}</span>
        </template>
  
        <template v-slot:item.location="{ item }">
          <span>{{ item.location_status || 'N/A' }}</span>
        </template>
  
        <template v-slot:item.actions="{ item }">
          <div class="action-icons">
            <v-icon @click="showRestoreDialog(item.uuid)" color="green" class="mr-2">mdi-restore</v-icon>
            <v-icon @click="showConfirmDialog(item.uuid)" color="red">mdi-delete</v-icon>
          </div>
        </template>
      </v-data-table-server>
  
      <!-- Restore Confirmation Dialog -->
      <RestoreConfirmDialog
        :restoreDialogName="restoreDialogName"
        v-model:modelValue="restoreDialog"
        :onConfirm="confirmRestore"
        :onCancel="() => (restoreDialog = false)"
      />
      
      <!-- Delete Confirmation Dialog -->
      <ConfirmDialog
        :dialogName="dialogName"
        v-model:modelValue="dialog"
        :onConfirm="confirmDelete"
        :onCancel="() => (dialog = false)"
      />
    </v-card>
  </template>
  
  <script>
  import { toast } from "vue3-toastify";
  import RestoreConfirmDialog from "../../Components/RestoreConfirmDialog.vue";
  import ConfirmDialog from "../../Components/ConfirmDialog.vue";
  
  export default {
    components: {
      RestoreConfirmDialog,
      ConfirmDialog,
    },
    data() {
      return {
        restoreDialogName: "Are you sure to restore this Data?",
        dialogName: "Are you sure you want to delete this machine permanently?",
        search: "",
        itemsPerPage: 10,
        headers: [
          { title: "Machine", key: "name", sortable: true },
          { title: "Company", key: "factory.company.name", sortable: false },
          { title: "Factory", key: "factory.name", sortable: false },
          { title: "Floor", key: "line.unit.floor.name", sortable: false },
          { title: "Unit", key: "line.unit.name", sortable: false },
          { title: "Line", key: "line.name", sortable: false },
          { title: "Location", key: "location_status", sortable: false },
          { title: "Model", key: "model", sortable: false }, 
          { title: "Status", key: "machine_status.name", sortable: false },
          { title: "Actions", key: "actions", sortable: false },
        ],
        serverItems: [],
        loading: true,
        totalItems: 0,
        dialog: false,
        restoreDialog: false, 
        selectedMachineId: null,
      };
    },
    methods: {
      // Load items from server
      async loadItems({ page, itemsPerPage, sortBy }) {
        this.loading = true;
        const sortOrder = sortBy.length ? sortBy[0].order : "desc";
        const sortKey = sortBy.length ? sortBy[0].key : "created_at";
        try {
          const response = await this.$axios.get("/mechine/assing/trashed", {
            params: {
              page,
              itemsPerPage,
              sortBy: sortKey,
              sortOrder,
              search: this.search,
            },
          });
          this.serverItems = response.data.items || [];
          this.totalItems = response.data.total || 0;
          this.fetchTrashedMachinesCount();
        } catch (error) {
          console.error("Error loading trashed machines:", error);
        } finally {
          this.loading = false;
        }
      },
  
      // View machine details
      viewMachineDetail(uuid) {
        this.$router.push({ name: "MachineShow", params: { uuid } });
      },
  
      // Show Restore Dialog
      showRestoreDialog(uuid) {
        this.selectedMachineId = uuid;
        this.restoreDialog = true;
      },
  
      // Show Delete Confirmation Dialog
      showConfirmDialog(uuid) {
        this.selectedMachineId = uuid;
        this.dialog = true;
      },
  
      // Restore machine from trash
      async confirmRestore() {
        this.restoreDialog = false;
        try {
          await this.$axios.post(`/mechine/assing/${this.selectedMachineId}/restore`);
          this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
          toast.success("Machine restored successfully!");
          bus.updateCount(bus.trashedMachinesCount.value - 1); // Update trashed machine count
        } catch (error) {
          console.error("Error restoring machine:", error);
          toast.error("Failed to restore machine.");
        }
      },
  
      // Delete machine permanently
      async confirmDelete() {
        this.dialog = false;
        try {
          await this.$axios.delete(`/mechine/assing/${this.selectedMachineId}/forceDelete`);
          this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
          toast.success("Machine deleted successfully!");
        } catch (error) {
          console.error("Error deleting machine:", error);
          toast.error("Failed to delete machine.");
        }
      },
  
      // Get the status color for machines
      getStatusColor(status) {
        switch (status) {
          case "Preventive":
            return "blue";
          case "Production":
            return "green";
          case "Breakdown":
            return "red";
          case "Under Maintenance":
            return "orange";
          case "Loan":
            return "purple";
          case "Idol":
            return "grey";
          case "AsFactory":
            return "cyan";
          case "Scraped":
            return "brown";
          default:
            return "black";
        }
      },
  
      // Navigate to Machine List page
      async MechineIndex() {
        this.$router.push({ name: "MechineIndex" });
      },
    },
  
    // Fetch data on page load
    created() {
      this.loadItems({
        page: 1,
        itemsPerPage: this.itemsPerPage,
        sortBy: [],
      });
    },
  };
  </script>
  
  <style scoped>
  .action-icons {
    display: flex;
    align-items: center;
  }
  </style>
  