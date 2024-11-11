<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Category Trash List</span></v-col>
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
                <v-icon @click="showRestoreDialog(item.id)" color="green"
                    >mdi-restore</v-icon
                >
                <v-icon @click="showConfirmDialog(item.id)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table-server>

        <!-- Restore Confirmation Dialog -->
        <RestoreConfirmDialog
            :restroreDialogName="restroreDialogName"
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
            dialogName:
                "Are you sure you want to permanently delete this Category ?",
            restroreDialogName: "Are you sure you want to restore this Rent?",
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Category Name", key: "name", sortable: true },
                { title: "Description", key: "description", sortable: false },
                {
                    title: "Status",
                    key: "status",
                    value: "status",
                    sortable: true,
                },
                { title: "Actions", key: "actions", sortable: false },
            ],
            serverItems: [],
            loading: true,
            totalItems: 0,
            restoreDialog: false, // Separate state for restore dialog
            deleteDialog: false, // Separate state for delete dialog
            selectedCategoryId: null,
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/category/trashed", {
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
            } finally {
                this.loading = false;
            }
        },
        showRestoreDialog(id) {
            this.selectedCategoryId = id;
            this.restoreDialog = true; // Open restore dialog
        },
        showConfirmDialog(id) {
            this.selectedCategoryId = id;
            this.deleteDialog = true; // Open delete dialog
        },
        async confirmRestore() {
            this.restoreDialog = false; // Close the restore dialog
            try {
                await this.$axios.post(
                    `/category/${this.selectedCategoryId}/restore`
                );
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Category restored successfully!");
            } catch (error) {
                console.error("Error restoring category:", error);
                toast.error("Failed to restore category.");
            }
        },
        async confirmDelete() {
            this.deleteDialog = false; // Close the delete dialog
            try {
                await this.$axios.delete(
                    `/category/${this.selectedCategoryId}/force-delete`
                );
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Category deleted successfully!");
            } catch (error) {
                console.error("Error deleting category:", error);
                toast.error("Failed to delete category.");
            }
        },
        editCategory(id) {
            this.$router.push({ name: "CategoryEdit", params: { id } });
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
