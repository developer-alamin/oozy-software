<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Supervisor List</span></v-col>
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
            v-if="isAuthorized"
            @click="createOperator"
           class="primary-color"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
              </template>
              <span>Add a new operator</span>
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
                <span>View trashed operators</span>
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
          :color="item.status === 'Active' ? 'green' : 'red'"
          class="text-uppercase"
          size="small"
          label
        >
          {{ item.status === "Active" ? "Active" : "Inactive" }}
        </v-chip>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-icon @click="editOperator(item.uuid)" class="mr-2"
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
      dialogName: "Are you sure you want to delete this Operator ?",
      search: "",
      itemsPerPage: 15,
      headers: [
        { title: "Company", key: "user.name", sortable: false },
        { title: "Factory", key: "factory.name", sortable: false },
        { title: "Name", key: "name", sortable: true },
        { title: "Email", key: "email", sortable: true },
        { title: "Phone", key: "phone", sortable: true },
        { title: "Type", key: "type", sortable: true },
        {
          title: "Status",
          key: "status",
          value: "status",
          sortable: true,
        },
        { title: "Creator", key: "creator.name", sortable: false },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      loading: true,
      totalItems: 0,
      dialog: false,
      selectedOperatorId: null,
      trashedCount: 0,
      user: {},
    };
  },
  computed: {
    // Check if the user is authorized to create Operators
    isAuthorized() {
      // You can customize the logic based on your needs
      return (
        this.user.superadmin === true ||
        this.user.admin === true ||
        this.user.user_role === true
      );
    },
  },

  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        const response = await this.$axios.get("/operator", {
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
        this.fetchTrashedOperatorsCount();
      } catch (error) {
        console.error("Error loading items:", error);
      } finally {
        this.loading = false;
      }
    },
    createOperator() {
      this.$router.push({ name: "OperatorCreate" });
    },
    viewTrash() {
      this.$router.push({ name: "OperatorTrash" });
    },
    editOperator(uuid) {
      this.$router.push({ name: "OperatorEdit", params: { uuid } });
    },
    showConfirmDialog(id) {
      this.selectedOperatorId = id;
      this.dialog = true;
    },
    async confirmDelete() {
      this.dialog = false; // Close the dialog
      try {
        await this.$axios.delete(`/operator/${this.selectedOperatorId}`);
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        toast.success("Operator deleted successfully!");
      } catch (error) {
        console.error("Error deleting Operator:", error);
        toast.error("Failed to delete Operator.");
      }
    },
    async fetchTrashedOperatorsCount() {
      try {
        const response = await this.$axios.get("/operator/trashed-count");
        this.trashedCount = response.data.trashedCount
          ? response.data.trashedCount
          : 0;
      } catch (error) {
        console.error("Error fetching trashed operators count:", error);
      }
    },
    async fetchUserInfo() {
      try {
        const response = await this.$axios.get("/user/role/auth"); // Adjust to your API
        this.user = response.data; // Ensure you set user role data here
      } catch (error) {
        console.error("Error fetching user info:", error);
      }
    },
  },

  created() {
    this.loadItems({
      page: 1,
      itemsPerPage: this.itemsPerPage,
      sortBy: [],
    });
    this.fetchUserInfo();
    this.fetchTrashedOperatorsCount();
  },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
