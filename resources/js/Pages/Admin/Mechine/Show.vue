<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12">
        <v-card class="pa-4" outlined>
          <v-card-title class="text-h6 font-weight-bold">
            Machine Details
          </v-card-title>
          <v-divider></v-divider>

          <v-row>
            <v-col
              v-for="(levelItem, index) in levels"
              :key="index"
              cols="12"
              class="border-bottom"
              md="6"
            >
              <v-row align="center">
                <!-- Level and Value aligned horizontally -->
                <v-col cols="6">
                  <v-card-subtitle>{{ levelItem.title }} :</v-card-subtitle>
                </v-col>
                <v-col cols="6">
                  <!-- Dynamically access the nested data using the 'key' -->
                  <v-card-text>{{ getNestedValue(levelItem.key) }}</v-card-text>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>

    <!-- <v-card class="mt-5">
      <v-card-title class="pt-5">
        <v-row>
          <v-col cols="4"><span>Machine History </span></v-col>
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
              @click="createMechine"
              color="primary"
              icon
              style="width: 40px; height: 40px"
            >
              <v-tooltip location="top" activator="parent">
                  <template v-slot:activator="{ props }">
                      <v-icon v-bind="props" style="font-size: 20px"
                          >mdi-plus</v-icon
                      >
                  </template>
                  <span>Add New a Mechine</span>
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
                  <span>View trashed Machines</span>
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
        item-value="created_at"
        loading-text="Loading... Please wait"
        @update:options="fetchMechine"
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
         <v-icon
                    @click="transferMachine(item.uuid)"
                    color="blue"
                    class="mr-2"
                    >mdi-transfer</v-icon
                > 
           <v-icon @click="editMechine(item.uuid)" class="mr-2"
                    >mdi-pencil</v-icon
                > 
          <v-icon @click="showConfirmDialog(item.id)" color="red"
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
    </v-card> -->
  </v-container>
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
       levels :[
        // Basic Machine Details
        { title: "Machine Code", key: "machine_code", sortable: true },
        { title: "Machine Name", key: "name", sortable: true },
        { title: "Machine Brand", key: "brand.name", sortable: false },
        { title: "Machine Model", key: "product_model.name", sortable: false },
        { title: "Machine Type", key: "mechine_type.name", sortable: false },
        // Maintenance Details
        { title: "Partial Maintenance", key: "partial_maintenance_day", sortable: false },
        { title: "Full Maintenance", key: "full_maintenance_day", sortable: false },
        // Purchase and Rent Details
        { title: "Supplier", key: "supplier.name", sortable: false },
        { title: "Purchase Date", key: "purchase_date", sortable: false },
        { title: "Purchase Price", key: "purchase_price", sortable: false },
        { title: "Rent Date", key: "rent_date", sortable: false },
        { title: "Rent Name", key: "rent_name", sortable: false },
        { title: "Rent Price", key: "rent_price", sortable: false },
        { title: "Rent Amount Type", key: "rent_amount_type", sortable: false },

        // Ownership and Warranty
        { title: "Serial Number", key: "serial_number", sortable: false },
        { title: "Commission Date", key: "commission_date", sortable: false },
        { title: "Warranty Period", key: "warranty_period", sortable: false },
        { title: "Ownership", key: "ownership", sortable: false },

        // Technical Specifications
        { title: "Power Requirements", key: "power_requirements", sortable: false },
        { title: "Capacity", key: "capacity", sortable: false },
        { title: "Dimensions", key: "dimensions", sortable: false },
        { title: "Machine Weight", key: "machine_weight", sortable: false },
        { title: "Material Compatibility", key: "material_compatibility", sortable: false },
        { title: "Maximum Speed", key: "maximum_speed", sortable: false },
        { title: "Optimum Speed", key: "optimum_speed", sortable: false },
        { title: "Operating Temperature Range", key: "operating_temperature_range", sortable: false },
        // Location Details
        { title: "Company", key: "factory.comapny.name", sortable: false },
        { title: "Factory", key: "factory.name", sortable: false },
        { title: "Floor", key: "line.unit.floor.name", sortable: false },
        { title: "Unit", key: "line.unit.name", sortable: false },
        { title: "Line", key: "line.name", sortable: false },
        { title: "Location", key: "location_status", sortable: false },
        // Additional Information
        { title: "Tags", key: "tag", sortable: false },
        { title: "Status", key: "machine_status.name", sortable: false },
      ],
      machine: {}, // This will hold the fetched machine data
      serverError: null,

      dialogName: "Are you sure you want to delete this Mechine ?",

      search: "",
      itemsPerPage: 10,
      headers : [
        // Primary Information
        { title: "Machine", key: "name", sortable: true },
        // Company and Location Details
        { title: "Company", key: "factory.company.name", sortable: false },
        { title: "Factory", key: "factory.name", sortable: false },
        { title: "Floor", key: "line.unit.floor.name", sortable: false },
        { title: "Unit", key: "line.unit.name", sortable: false },
        { title: "Line", key: "line.name", sortable: false },
        // Additional Information
        { title: "Location", key: "location_status", sortable: false },
        { title: "Status", key: "machine_status.name", sortable: false },
        // Other Details
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
  created() {
    this.fetchMechine();
  },
  methods: {
    async fetchMechine() {
      this.loading = true;
      const machineAssignId = this.$route.params.uuid;

      try {
        const response = await this.$axios.get(
          `/machine-assing/${machineAssignId}`
        );
        
        this.machine = response.data.machineAssign;

        // this.serverItems = response.data.items || [];
        // this.totalItems = response.data.total || 0;
      } catch (error) {
        this.serverError = "Error fetching technician data.";
        console.error(error);
      }
    },
    // Method to dynamically access nested data based on key
    getNestedValue(key) {
      const keys = key.split(".");
      let value = this.machine;
      console.log(keys);
      
      keys.forEach((k) => {
        value = value ? value[k] : null;
      });
      return value || "-"; // Return a fallback value if the data is not found
    },

    createMechine() {
      this.$router.push({ name: "MechineCreate" });
    },
    viewTrash() {
      this.$router.push({ name: "MechineTrash" });
    },
    editMechine(uuid) {
      this.$router.push({ name: "MechineEdit", params: { uuid } });
    },
    // transferMachine(uuid) {
    //   this.$router.push({ name: "MechineTransfer", params: { uuid } });
    // },
    showConfirmDialog(id) {
      this.selectedMechineId = id;
      this.dialog = true;
    },
    async confirmDelete() {
      // this.dialog = false; // Close the dialog
      // try {
      //   const response = await this.$axios.delete(
      //     `/mechine-assing/${this.selectedMechineId}`
      //   );
      //   this.loadItems({
      //     page: 1,
      //     itemsPerPage: this.itemsPerPage,
      //     sortBy: [],
      //   });
      //   console.log(response.data);
      //   toast.success("Mechine deleted successfully!");
      // } catch (error) {
      //   console.error("Error deleting Mechine:", error);
      //   toast.error("Failed to delete Mechine.");
      // }
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
};
</script>

<style scoped>
.v-card {
  border: 1px solid #ccc; /* Add border around the card */
  border-radius: 8px; /* Rounded corners */
}

.v-card-subtitle {
  font-weight: bold;
}

.v-card-text {
  font-size: 14px;
}

.border-bottom {
  border-bottom: 1px solid #ccc; /* Adds a bottom border to each column */
}
</style>
