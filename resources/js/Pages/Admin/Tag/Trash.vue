<template>
    <v-card>
      <v-card-title class="pt-5">
        <v-row>
          <v-col cols="6">
            <span>Machine Tag Trash List</span>
          </v-col>
          <v-col cols="6" class="d-flex justify-end">
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
        <template v-slot:item.status="{ item }">
          <v-chip
            :color="item.status === 'Active' ? 'green' : 'red'"
            class="text-uppercase"
            size="small"
            label
          >
            {{ item.status === "Active" ? "Active" : "Inactive" }}
          </v-chip>
        </template>
  
        <template v-slot:item.actions="{ item }">
          <v-icon @click="showRestoreDialog(item.uuid)" color="green">mdi-restore</v-icon>
          <v-icon @click="showConfirmDialog(item.uuid)" color="red">mdi-delete</v-icon>
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
        v-model:modelValue="deleteDialog"
        :onConfirm="confirmDelete"
        :onCancel="() => (deleteDialog = false)"
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
        restoreDialogName: "Are you sure you want to restore this machine tag?",
        dialogName: "Are you sure you want to delete this machine tag?",
        search: "",
        itemsPerPage: 15,
        headers: [
         { title: "Company", key: "company.name", sortable: false },
          { title: "Tag Name", key: "name", sortable: true },
          { title: "Description", key: "note", sortable: false },
          { title: "Status", key: "status", value: "status", sortable: true },
          { title: "Actions", key: "actions", sortable: false },
        ],
        serverItems: [],
        loading: true,
        totalItems: 0,
        restoreDialog: false, // State for restore dialog
        deleteDialog: false, // State for delete dialog
        selectedTagId: null,
      };
    },
    methods: {
      async loadItems({ page, itemsPerPage, sortBy }) {
        this.loading = true;
        const sortOrder = sortBy.length ? sortBy[0].order : "desc";
        const sortKey = sortBy.length ? sortBy[0].key : "created_at";
        try {
          const response = await this.$axios.get("/machine-tag/trashed", {
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
        } catch (error) {
          console.error("Error loading items:", error);
        } finally {
          this.loading = false;
        }
      },
      showRestoreDialog(uuid) {
        this.selectedTagId = uuid;
        this.restoreDialog = true; // Open restore dialog
      },
      showConfirmDialog(uuid) {
        this.selectedTagId = uuid;
        this.deleteDialog = true; // Open delete dialog
      },
      async confirmRestore() {
        this.restoreDialog = false; // Close the restore dialog
        try {
          await this.$axios.post(`/machine-tag/${this.selectedTagId}/restore`);
          this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
          });
          toast.success("Machine tag restored successfully!");
        } catch (error) {
          console.error("Error restoring machine tag:", error);
          toast.error("Failed to restore machine tag.");
        }
      },
      async confirmDelete() {
        this.deleteDialog = false; // Close the delete dialog
        try {
          await this.$axios.delete(`/machine-tag/${this.selectedTagId}/force-delete`);
          this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
          });
          toast.success("Machine tag deleted successfully!");
        } catch (error) {
          console.error("Error deleting machine tag:", error);
          toast.error("Failed to delete machine tag.");
        }
      }
    },
    created() {
      this.loadItems({
        page: 1,
        itemsPerPage: this.itemsPerPage,
        sortBy: [],
      });
    },
  };
  </script>
  