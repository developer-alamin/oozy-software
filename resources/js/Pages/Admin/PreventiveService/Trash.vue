<template>
	<v-card>
	  <v-card-title class="pt-5">
		<v-row>
		  <v-col cols="6"><span>Preventive Service Trash List</span></v-col>
		  <v-col cols="6" class="d-flex justify-end">
			<v-date-input
				v-model="dateRange"
				label="Select Date range"
				multiple="range"
				prepend-icon=""
				style="padding-right: 10px;"
			></v-date-input>
			<v-btn
				@click="PreventiveServiceIndex"
				class="primary-color"
				icon
				style="width: 40px; height: 40px"
			>
				<v-tooltip location="top" activator="parent">
					<template v-slot:activator="{ props }">
						<v-icon v-bind="props" style="font-size: 20px"
							>mdi-home</v-icon
						>
					</template>
					<span>Mechine List</span>
				</v-tooltip>
			</v-btn>
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
		<!-- Status Column -->
		<template v-slot:item.service_status="{ item }">
			<v-chip
				:color="getStatusColor(item.service_status)"
				class="text-uppercase"
				size="small"
				label
			>
				{{ item.service_status }}
			</v-chip>
		</template>
		<template v-slot:item.actions="{ item }">
		  <v-icon @click="showRestoreDialog(item.uuid)" color="green"
			>mdi-restore</v-icon
		  >
		  <v-icon @click="showConfirmDialog(item.uuid)" color="red"
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
		restoreDialogName: "Are you sure you want to restore this Preventive Service?",
		dialogName: "Are you sure you want to permanently delete this Preventive Service?",
  
		dateRange: null,
		itemsPerPage: 10,
		headers: [
		  {
			title: "DateTime",
			key: "date_time",
			sortable: true,
		  },
		  {
			title: "Mechine Name",
			key: "mechine_assing.name",
			sortable: false,
		  },
		  {
			title: "Mechine Code",
			key: "mechine_assing.machine_code",
			sortable: false,
		  },
		  {
			title: "Service Status",
			key: "service_status",
			sortable: true,
		  },
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
		  const response = await this.$axios.get("/preventive-service/trashed", {
			params: {
			  page,
			  itemsPerPage,
			  sortBy: sortKey,
			  sortOrder,
			  dateRange: this.formattedDateRangeString,
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
		 const response = await this.$axios.post(`preventive-service/${this.selectedUuid}/restore`);
		  this.loadItems({
			page: 1,
			itemsPerPage: this.itemsPerPage,
			sortBy: [],
		  });
		  toast.success("Preventive Service restored successfully!");
		} catch (error) {
		  console.error("Error restoring Preventive Service:", error);
		  toast.error("Failed to restore Preventive Service.");
		}
	  },
	  async confirmDelete() {
		this.deleteDialog = false;
		try {
		  await this.$axios.delete(`/preventive-service/${this.selectedUuid}/force-delete`);
		  this.loadItems({
			page: 1,
			itemsPerPage: this.itemsPerPage,
			sortBy: [],
		  });
		  toast.success("Preventive Service deleted permanently!");
		} catch (error) {
		  console.error("Error deleting Preventive Service:", error);
		  toast.error("Failed to delete Preventive Service.");
		}
	  },
	  getStatusColor(status) {
			switch (status) {
				case 'Processing':
				return 'orange'; // Color for Processing status
				case 'Done':
				return 'green'; // Color for Done status
				case 'Cancel':
				return 'red'; // Color for Cancel status
				default:
				return 'grey'; // Default color for unknown status
			}
	  },
	  PreventiveServiceIndex(){
		this.$router.push({ name: "PreventiveServiceIndex" });

	  }
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
	  this.loadItems({
		page: 1,
		itemsPerPage: this.itemsPerPage,
		sortBy: [],
	  });
	},
  };
  </script>
  
  <style scoped>
  /* Optional: Add styles for the trash page */
  </style>
  