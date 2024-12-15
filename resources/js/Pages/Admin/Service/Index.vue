<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Breakdown Service List</span></v-col>
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
            @click="createService"
            color="primary"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
              </template>
              <span>Add New a Breakdown Service</span>
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
                <span>View trashed Breakdown Service</span>
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
      <template v-slot:item.status="{ item }">
        <v-chip
          :color="getStatusColor(item.status)"
          class="text-uppercase"
          size="small"
          label
        >
          {{ item.status }}
        </v-chip>
      </template>
      <template v-slot:item.creator_name="{ item }">
        <span>{{ item.creator ? item.creator.name : "Unknown" }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-tooltip location="top" activator="parent">
          <template v-slot:activator="{ props }">
            <v-icon
              v-bind="props"
              style="font-size: 20px"
              @click="acknowledge(item.uuid)"
              color="green"
              class="mr-2"
            >
              mdi-checkbox-marked-circle
            </v-icon>
          </template>
          <span>Acknowledge</span>
        </v-tooltip>
        <!-- <v-icon @click="transferMachine(item.uuid)" color="blue" class="mr-2"
          >mdi-transfer</v-icon
        > -->
        <v-icon @click="editMechine(item.uuid)" class="mr-2">mdi-pencil</v-icon>
        <!-- <v-icon @click="showConfirmDialog(item.id)" color="red"
          >mdi-delete</v-icon
        > -->
        <!-- Acknowledge Action -->

        <!-- <v-tooltip location="top" activator="parent">
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
          </template>
          <span>Add New a Breakdown Service</span>
        </v-tooltip> -->
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
  </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
import ConfirmDialog from "../../Components/ConfirmDialog.vue";

export default {
  components: {
    ConfirmDialog,
  },
  data() {
    return {
      dialogName: "Are you sure you want to delete this Mechine ?",

      search: "",
      itemsPerPage: 10,
      headers: [
        // {
        //   title: "Machine Code",
        //   key: "mechine_assing.machine_code",
        //   sortable: false,
        // },
        {
          title: "Technician",
          key: "technician.name",
          sortable: false,
        },
        // { title: "Company Name", key: "user.name", sortable: false },
        { title: "Location", key: "line.name", sortable: false },
        {
          title: "Technician Status",
          key: "breakdown_service_technician_status",
          sortable: true,
        },

        {
          title: "Service Status",
          key: "breakdown_service_status",
          sortable: true,
        },
        { title: "Creator", key: "creator.name", sortable: false },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      loading: true,
      totalItems: 0,
      dialog: false,
      selectedMechineId: null,
      trashedCount: 0,
    };
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
            search: this.search,
          },
        });
        console.log(response.data.items);

        this.serverItems = response.data.items || [];
        this.totalItems = response.data.total || 0;
        this.fetchTrashedMechinesCount();
      } catch (error) {
        console.error("Error loading items:", error);
      } finally {
        this.loading = false;
      }
    },
    acknowledge(uuid) {
      // Make an API call to update breakdown_service_technician_status
      this.$axios
        .post("/breakdown-service/technician-update-status", {
          uuid: uuid,
        })
        .then((response) => {
          this.$emit("refresh-data"); // Emit event to refresh the table
          this.$toast.success("Technician Update successfully"); // Notify the user
          this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
          });
        })
        .catch((error) => {
          console.error("Error Technician Update:", error);
          this.$toast.error("Failed to Technician Update");
        });
    },
    createService() {
      this.$router.push({ name: "ServiceCreate" });
    },
    viewTrash() {
      this.$router.push({ name: "MechineTrash" });
    },
    editMechine(uuid) {
      this.$router.push({ name: "MechineEdit", params: { uuid } });
    },
    transferMachine(uuid) {
      this.$router.push({ name: "MechineTransfer", params: { uuid } });
    },
    showConfirmDialog(id) {
      this.selectedMechineId = id;
      this.dialog = true;
    },

    async confirmDelete() {
      this.dialog = false; // Close the dialog
      try {
        const response = await this.$axios.delete(
          `/mechine-assing/${this.selectedMechineId}`
        );
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        console.log(response.data);
        toast.success("Mechine deleted successfully!");
      } catch (error) {
        console.error("Error deleting Mechine:", error);
        toast.error("Failed to delete Mechine.");
      }
    },
    async fetchTrashedMechinesCount() {
      try {
        const response = await this.$axios.get("mechine/assing/trashed-count");
        this.trashedCount = response.data.trashedCount
          ? response.data.trashedCount
          : 0;
      } catch (error) {
        console.error("Error fetching trashed Mechine count:", error);
      }
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
    this.fetchTrashedMechinesCount();
  },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
