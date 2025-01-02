<template>
	<v-card>
	  <v-card-title class="pt-5">
		<v-row>
		  <v-col cols="6">
			<span>Breakdown Service Trash List</span>
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
			<v-btn
            @click="AllBreakdownService"
            color="primary"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-home</v-icon>
              </template>
              <span>Home</span>
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
		<template v-slot:item.actions="{ item }">
		  <v-icon @click="showRestoreDialog(item.uuid)" color="green">
			mdi-restore
		  </v-icon>
		  <v-icon @click="showConfirmDialog(item.uuid)" color="red">
			mdi-delete
		  </v-icon>
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
		restoreDialogName: "Are you sure you want to restore this Breakdown Service?",
		dialogName: "Are you sure you want to delete this Breakdown Service permanently?",
  
		search: "",
		itemsPerPage: 10,
		headers: [
		  { title: "DateTime", key: "date_time", sortable: true },
		  { title: "Mechine Name", key: "mechine_assing.name", sortable: false },
		  { title: "Mechine Code", key: "mechine_assing.machine_code", sortable: false },
		  { title: "Service Status", key: "service_status", sortable: true },
		  { title: "Actions", key: "actions", sortable: false },
		],
		serverItems: [],
		loading: true,
		totalItems: 0,
		restoreDialog: false,
		deleteDialog: false,
		selectedUuid: null,
	  };
	},
	methods: {
	  async loadItems({ page, itemsPerPage, sortBy }) {
		this.loading = true;
		const sortOrder = sortBy.length ? sortBy[0].order : "desc";
		const sortKey = sortBy.length ? sortBy[0].key : "created_at";
		try {
		  const response = await this.$axios.get("/breakdown-service/trashed", {
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
		this.selectedUuid = uuid;
		this.restoreDialog = true;
	  },
	  showConfirmDialog(uuid) {
		this.selectedUuid = uuid;
		this.deleteDialog = true;
	  },
	  async confirmRestore() {
		this.restoreDialog = false;
		try {
		  await this.$axios.post(`/breakdown-service/${this.selectedUuid}/restore`);
		  this.loadItems({
			page: 1,
			itemsPerPage: this.itemsPerPage,
			sortBy: [],
		  });
		  toast.success("Breakdown Service restored successfully!");
		} catch (error) {
		  console.error("Error restoring Breakdown Service:", error);
		  toast.error("Failed to restore Breakdown Service.");
		}
	  },
	  async confirmDelete() {
		this.deleteDialog = false;
		try {
		  await this.$axios.delete(`/breakdown-service/${this.selectedUuid}/forceDelete`);
		  this.loadItems({
			page: 1,
			itemsPerPage: this.itemsPerPage,
			sortBy: [],
		  });
		  toast.success("Breakdown Service permanently deleted!");
		} catch (error) {
		  console.error("Error deleting Breakdown Service:", error);
		  toast.error("Failed to delete Breakdown Service.");
		}
	  },
	  AllBreakdownService(){
		this.$router.push({ name: "BreakdownServiceIndex" });

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
  
  <style scoped>
  /* Add any specific styles if needed */
  </style>
  