<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>ProblemNotes List</span></v-col>
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
            @click="problemNoteCreate"
            color="primary"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
              </template>
              <span>Add a New ProblemNote</span>
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
                <span>View Trashed ProblemNotes</span>
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
          {{ item.status }}
        </v-chip>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-icon @click="editProblemNote(item.uuid)" color="green" class="mr-2"
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
      dialogName: "Are you sure you want to delete this ProblemNote?",
      search: "",
      itemsPerPage: 15,
      headers: [
        { title: "Company", key: "company.name", sortable: false },
        { title: "Name", key: "name", sortable: false },
        { title: "Status", key: "status", sortable: true },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      loading: true,
      totalItems: 0,
      dialog: false,
      selectedProblemNoteId: null,
      trashedCount: 0,
    };
  },
  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        const response = await this.$axios.get("/problemnote", {
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
        this.fetchTrashedProblemNotesCount();
      } catch (error) {
        console.error("Error loading items:", error);
      } finally {
        this.loading = false;
      }
    },
    problemNoteCreate() {
      this.$router.push({ name: "ProblemNoteCreate" });
    },
    viewTrash() {
      this.$router.push({ name: "ProblemNoteTrash" });
    },
    editProblemNote(uuid) {
      this.$router.push({ name: "ProblemNoteEdit", params: { uuid } });
    },
    showConfirmDialog(uuid) {
      this.selectedProblemNoteId = uuid;
      this.dialog = true;
    },
    async confirmDelete() {
      this.dialog = false;
      try {
        const response = await this.$axios.delete(`/problemnote/${this.selectedProblemNoteId}`);
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        if (response.data.success) {
          toast.success("ProblemNote deleted successfully!");
        }
      } catch (error) {
        console.error("Error deleting ProblemNote:", error);
        toast.error("Failed to delete ProblemNote.");
      }
    },
    async fetchTrashedProblemNotesCount() {
      try {
        const response = await this.$axios.get("/problemnotes/trashed-count");
        this.trashedCount = response.data.trashedCount;
      } catch (error) {
        console.error("Error fetching trashed ProblemNotes count:", error);
      }
    },
  },
  created() {
    this.loadItems({
      page: 1,
      itemsPerPage: this.itemsPerPage,
      sortBy: [],
    });
    this.fetchTrashedProblemNotesCount();
  },
};
</script>
