<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6">
                    <span>Breakdown Problem Trash List</span>
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
                        @input="onSearch"
                    ></v-text-field>
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
                <v-icon @click="showRestoreDialog(item.id)" color="green">
                    mdi-restore
                </v-icon>
                <v-icon @click="showConfirmDialog(item.id)" color="red">
                    mdi-delete
                </v-icon>
            </template>
        </v-data-table-server>

        <!-- Restore Confirmation Dialog -->
        <RestoreConfirmDialog
            :dialogName="restoreDialogName"
            v-model:modelValue="restoreDialog"
            :onConfirm="confirmRestore"
            :onCancel="closeRestoreDialog"
        />
        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :dialogName="deleteDialogName"
            v-model:modelValue="deleteDialog"
            :onConfirm="confirmDelete"
            :onCancel="closeDeleteDialog"
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
            restoreDialogName: "Are you sure you want to restore this breakdown problem?",
            deleteDialogName: "Are you sure you want to permanently delete this breakdown problem?",
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Company", key: "company.name", sortable: true },
                { title: "Problem Note", key: "note", sortable: false },
                { title: "Status", key: "status", value: "status", sortable: true },
                { title: "Actions", key: "actions", sortable: false },
            ],
            serverItems: [],
            loading: false,
            totalItems: 0,
            restoreDialog: false,
            deleteDialog: false,
            selectedUuid: null,
        };
    },
    methods: {
        async loadItems({ page = 1, itemsPerPage = 15, sortBy = [] }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";

            try {
                const response = await this.$axios.get("/breakdown-problems/trashed", {
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
                toast.error("Failed to load items.");
            } finally {
                this.loading = false;
            }
        },
        onSearch() {
            this.loadItems({
                page: 1,
                itemsPerPage: this.itemsPerPage,
                sortBy: [],
            });
        },
        showRestoreDialog(uuid) {
            this.selectedUuid = uuid;
            this.restoreDialog = true;
        },
        closeRestoreDialog() {
            this.restoreDialog = false;
        },
        async confirmRestore() {
            this.restoreDialog = false;
            try {
                await this.$axios.post(`/breakdown-problems/${this.selectedUuid}/restore`);
                toast.success("Breakdown problem restored successfully!");
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
            } catch (error) {
                console.error("Error restoring breakdown problem:", error);
                toast.error("Failed to restore breakdown problem.");
            }
        },
        showConfirmDialog(uuid) {
            this.selectedUuid = uuid;
            this.deleteDialog = true;
        },
        closeDeleteDialog() {
            this.deleteDialog = false;
        },
        async confirmDelete() {
            this.deleteDialog = false;
            try {
                await this.$axios.delete(`/breakdown-problems/${this.selectedUuid}/force-delete`);
                toast.success("Breakdown problem deleted successfully!");
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
            } catch (error) {
                console.error("Error deleting breakdown problem:", error);
                toast.error("Failed to delete breakdown problem.");
            }
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
