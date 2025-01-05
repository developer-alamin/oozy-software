<template>
  <v-card>
    <v-card-title class="pt-5">
      <v-row>
        <v-col cols="4"><span>Effects List</span></v-col>
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
            @click="effectCreate"
            color="primary"
            icon
            style="width: 40px; height: 40px"
          >
            <v-tooltip location="top" activator="parent">
              <template v-slot:activator="{ props }">
                <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
              </template>
              <span>Add a New Effect</span>
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
                <span>View Trashed Effects</span>
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
        <v-icon @click="editEffect(item.uuid)" color="green" class="mr-2"
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
      dialogName: "Are you sure you want to delete this effect?",
      search: "",
      itemsPerPage: 15,
      headers: [
        { title: "Company", key: "cuase.problem_note.company.name", sortable: false },
        { title: "Problem Note", key: "cuase.problem_note.name", sortable: false },
        { title: "Cause", key: "cuase.name", sortable: false },
        { title: "Name", key: "name", sortable: false },
        { title: "Status", key: "status", sortable: true },
        { title: "Actions", key: "actions", sortable: false },
      ],
      serverItems: [],
      loading: true,
      totalItems: 0,
      dialog: false,
      selectedEffectId: null,
      trashedCount: 0,
    };
  },
  methods: {
    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const sortOrder = sortBy.length ? sortBy[0].order : "desc";
      const sortKey = sortBy.length ? sortBy[0].key : "created_at";
      try {
        const response = await this.$axios.get("/effect", {
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
        this.fetchTrashedEffectsCount();
      } catch (error) {
        console.error("Error loading items:", error);
      } finally {
        this.loading = false;
      }
    },
    effectCreate() {
      this.$router.push({ name: "EffectCreate" });
    },
    viewTrash() {
      this.$router.push({ name: "EffectTrash" });
    },
    editEffect(uuid) {
      this.$router.push({ name: "EffectEdit", params: { uuid } });
    },
    showConfirmDialog(uuid) {
      this.selectedEffectId = uuid;
      this.dialog = true;
    },
    async confirmDelete() {
      this.dialog = false;
      try {
        const response = await this.$axios.delete(`/effect/${this.selectedEffectId}`);
        this.loadItems({
          page: 1,
          itemsPerPage: this.itemsPerPage,
          sortBy: [],
        });
        if (response.data.success) {
          toast.success("Effect deleted successfully!");
        }
      } catch (error) {
        console.error("Error deleting effect:", error);
        toast.error("Failed to delete effect.");
      }
    },
    async fetchTrashedEffectsCount() {
      try {
        const response = await this.$axios.get("/effects/trashed-count");
        this.trashedCount = response.data.trashedCount;
      } catch (error) {
        console.error("Error fetching trashed effects count:", error);
      }
    },
  },
  created() {
    this.loadItems({
      page: 1,
      itemsPerPage: this.itemsPerPage,
      sortBy: [],
    });
    this.fetchTrashedEffectsCount();
  },
};
</script>
