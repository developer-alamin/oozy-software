<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="4"><span>Machine model lists</span></v-col>
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
                        @click="createModel"
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
                            <span>Add a new model</span>
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
                                    <v-icon
                                        v-bind="props"
                                        style="font-size: 20px"
                                    >
                                        mdi-trash-can-outline
                                    </v-icon>
                                </template>
                                <span>View trashed product models </span>
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

            <template v-slot:item.creator_name="{ item }">
                <span>{{ item.creator ? item.creator.name : "Unknown" }}</span>
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon @click="editModel(item.uuid)" class="mr-2"
                    >mdi-pencil</v-icon
                >
                <v-icon @click="showConfirmDialog(item.uuid)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table-server>

        <ConfirmDialog
            v-model:modelValue="dialog"
            :dialogName="dialogName"
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
            dialogName: "Are you sure you want to delete this Machine Model ?",
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Model Name", key: "name", sortable: true },
                { title: "Model Number", key: "model_number", sortable: true },
                { title: "Description", key: "description", sortable: false },
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
            selectedModelId: null,
            trashedCount: 0,
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/models", {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortKey,
                        sortOrder,
                        search: this.search,
                    },
                });
                this.serverItems = response.data.items || [];
                console.log(response.data);

                this.totalItems = response.data.total || 0;
                this.fetchTrashedModelsCount();
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        createModel() {
            this.$router.push({ name: "ModelCreate" });
        },
        viewTrash() {
            this.$router.push({ name: "ModelTrash" });
        },
        editModel(uuid) {
            this.$router.push({ name: "ModelEdit", params: { uuid } });
        },
        showConfirmDialog(uuid) {
            this.selectedModelId = uuid;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                await this.$axios.delete(`/models/${this.selectedModelId}`);
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Model deleted successfully!");
            } catch (error) {
                console.error("Error deleting model:", error);
                toast.error("Failed to delete model.");
            }
        },
        async fetchTrashedModelsCount() {
            try {
                const response = await this.$axios.get("/models/trashed-count");
                console.log(response.data);

                this.trashedCount = response.data.trashedCount;
            } catch (error) {
                console.error("Error fetching trashed models count:", error);
            }
        },
    },

    created() {
        this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
        });
        this.fetchTrashedModelsCount();
    },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
