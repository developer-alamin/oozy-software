<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Preventive Service List</span></v-col>
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
            @click="createPreventiveService"
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
      :search="search"
      :items="serverItems"
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
      :onCancel="
        () => {
          dialog = false;
        }
      "
    />

    <ConfirmDialogAcknowledged
      :dialogName="dialogNameAcknowledged"
      v-model:modelValue="dialog_acknowledged"
      :onConfirm="TechnicianPreventiveServiceAcknowledge"
      :onCancel="
        () => {
          dialog_acknowledged = false;
        }
      "
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
      dialogName: "Are you sure you want to delete this Service ?",
      dialogNameAcknowledged: "Are you sure you want to Acknowledge?",

      search: "",
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
      dialog: false,
      selectedId: null,
      trashedCount: 0,
      dialog_acknowledged: false,
      selectedDetialId: null,
    };
  },
  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        const response = await this.$axios.get("/preventive-service", {
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
        this.fetchTrashedPreventiveServiceCount();
      } catch (error) {
        console.error("Error loading items:", error);
      } finally {
        this.loading = false;
      }
    },

    createPreventiveService() {
      this.$router.push({ name: "PreventiveServiceCreate" });
    },
    detailsList(uuid){

      this.$router.push({ name: "PreventiveServiceDetailsList" ,params: { uuid }});
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
      this.dialog = false; // Close the dialog
      try {
        const response = await this.$axios.delete(
          `/preventive-service/${this.selectedId}`
        );
        
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        toast.success("Preventive Service deleted successfully!");
      } catch (error) {
        console.error("Error deleting Preventive Service:", error);
        toast.error("Failed to delete Preventive Service.");
      }
    },
    async TechnicianPreventiveServiceAcknowledge() {
      this.dialog_acknowledged = false; // Close the dialog
      try {
        const response = await this.$axios.put(
          `/preventive-service/${this.selectedDetialId}/technician-preventive-service-acknowledge`
        );
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        toast.success("I am Acknowledged!");
      } catch (error) {
        console.error("Error:", error);
        toast.error("Failed to Acknowledged.");
      }
    },
    async fetchTrashedPreventiveServiceCount() {
      try {
        const response = await this.$axios.get("preventive-service/trashed-count");
        this.trashedCount = response.data.trashedCount
          ? response.data.trashedCount
          : 0;
      } catch (error) {
        console.error("Error fetching trashed Preventive Service count:", error);
      }
    },

  },

  created() {
    this.loadItems({
      page: 1,
      itemsPerPage: this.itemsPerPage,
      sortBy: [],
    });
    this.fetchTrashedPreventiveServiceCount();
  },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
