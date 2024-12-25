<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Parse Trash List</span></v-col>
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
            @click="ParseIndex"
            color="primary"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-home</v-icon>
              </template>
              <span>Parse List</span>
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
        <v-icon @click="showRestoreDialog(item.id)" color="green"
          >mdi-restore</v-icon
        >
        <v-icon @click="showConfirmDialog(item.id)" color="red"
          >mdi-delete</v-icon
        >
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
      restoreDialogName: "Are you sure you want to restore this Parse  ?",
      dialogName: "Are you sure you want to delete this Parse  ?",
      search: "",
      itemsPerPage: 15,
      headers: [
        { title: "Company ", key: "user.name", sortable: false },
        { title: "Factory ", key: "factory.name", sortable: false },
        { title: "Parse Name", key: "name", sortable: true },
        {
          title: "Purchase Date",
          key: "purchase_date",
          sortable: false,
        },
        {
          title: "Purchase Price",
          key: "purchase_price",
          sortable: false,
        },
        {
          title: "Quantity",
          key: "quantity",
          sortable: false,
        },

        { title: "Status", key: "status", sortable: true },
        { title: "Creator", key: "creator.name", sortable: false },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      loading: true,
      totalItems: 0,
      restoreDialog: false, // Separate state for restore dialog
      deleteDialog: false, // Separate state for delete dialog
      selectedParseAsssingId: null,
    };
  },
  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        const response = await this.$axios.get("/parse/trashed", {
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
    showRestoreDialog(id) {
      this.selectedParseAsssingId = id;
      this.restoreDialog = true; // Open restore dialog
    },
    showConfirmDialog(id) {
      this.selectedParseAsssingId = id;
      this.deleteDialog = true; // Open delete dialog
    },
    async confirmRestore() {
      this.restoreDialog = false; // Close the restore dialog
      try {
        await this.$axios.post(`/parse/${this.selectedParseAsssingId}/restore`);
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        toast.success("Parse  restored successfully!");
      } catch (error) {
        console.error("Error restoring parse:", error);
        toast.error("Failed to restore parse.");
      }
    },
    async confirmDelete() {
      this.deleteDialog = false; // Close the delete dialog
      try {
        await this.$axios.delete(
          `/parse/${this.selectedParseAsssingId}/force-delete`
        );
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        toast.success("Parse deleted successfully!");
      } catch (error) {
        console.error("Error deleting parse:", error);
        toast.error("Failed to delete parse.");
      }
    },
    async ParseIndex() {
      this.$router.push({ name: "ParseIndex" });
    },
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
          return "black"; // Default color if status doesn't match
      }
    },
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
