<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="4"><span>Mechine Source List</span></v-col>
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
                        @click="createMechineSource"
                     class="primary-color"
                        icon
                        style="width: 40px; height: 40px"
                    >
                        <v-tooltip location="top" activator="parent">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props" style="font-size: 20px"
                                    >mdi-plus</v-icon
                                >
                            </template>
                            <span>Add a new Source</span>
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
                                <span>View trashed Source</span>
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
                <v-icon @click="editMechineSource(item.uuid)" class="mr-2"
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
    </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
import ConfirmDialog from "../../../Components/ConfirmDialog.vue";
import bus from "./eventBus";

export default {
    components: {
        ConfirmDialog,
    },
    data() {
        return {
            dialogName:"Are you sure you want to delete this Source ?",
            search: "", 
            itemsPerPage: 15,
            headers: [
                { title: "Name", key: "name", sortable: true },
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
            selectedSourceId: null,
            trashedCount: 0,
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/mechine/source", {
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
                this.fetchTrashedSourcesCount();
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        createMechineSource() {
            this.$router.push({ name: "MechineSourceCreate" });
        },
        viewTrash() {
            this.$router.push({ name: "MechineSourceTrash" });
        },
        editMechineSource(uuid) {
            this.$router.push({ name: "MechineSourceEdit", params: { uuid } });
        },
        showConfirmDialog(uuid) {
            this.selectedSourceId = uuid;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                await this.$axios.delete(`mechine/source/${this.selectedSourceId}`);
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Source deleted successfully!");
            } catch (error) {
                console.error("Error deleting Source:", error);
                toast.error("Failed to delete Source.");
            }
        },
        async fetchTrashedSourcesCount() {
            try {
                const response = await this.$axios.get("/mechine/sources/trashed-count");
                this.trashedCount = response.data.trashedCount;
            } catch (error) {
                console.error("Error fetching trashed Sources count:", error);
            }
        },
    },

    created() {
        this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
        });
        this.fetchTrashedSourcesCount();
    },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
