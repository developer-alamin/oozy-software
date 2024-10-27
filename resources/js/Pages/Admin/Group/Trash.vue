<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Group Trash List</span></v-col>
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
                    <v-btn
                        @click="GroupIndex"
                        color="primary"
                        icon
                        style="width: 40px; height: 40px"
                    >
                        <v-tooltip location="top" activator="parent">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props" style="font-size: 20px"
                                    >mdi-home</v-icon
                                >
                            </template>
                            <span>Group List</span>
                        </v-tooltip>
                    </v-btn>
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
import bus from "./eventBus";

export default {
    components: {
        RestoreConfirmDialog,
        ConfirmDialog,
    },
    data() {
        return {
            restroreDialogName:"Are you sure you want to restore this Group?",
            dialogName:"Are you sure you want to delete this Group ?",
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Group Name", key: "name", sortable: true },
                { title: "Description", key: "description", sortable: false },
                {
                    title: "Create",
                    key: "create",
                    value: "created_at",
                    sortable: true,
                },
                { title: "Actions", key: "actions", sortable: false },
            ],
            serverItems: [],
            loading: true,
            totalItems: 0,
            restoreDialog: false, // Separate state for restore dialog
            deleteDialog: false, // Separate state for delete dialog
            selectedGroupId: null,
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/groups/trashed", {
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
            this.selectedGroupId = id;
            this.restoreDialog = true; // Open restore dialog
        },
        showConfirmDialog(id) {
            this.selectedGroupId = id;
            this.deleteDialog = true; // Open delete dialog
        },
        async confirmRestore() {
            this.restoreDialog = false; // Close the restore dialog
            try {
                await this.$axios.post(
                    `/groups/${this.selectedGroupId}/restore`
                );
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Groups restored successfully!");
                bus.updateCount(bus.trashedGroupsCount.value - 1);
            } catch (error) {
                console.error("Error restoring group:", error);
                toast.error("Failed to restore group.");
            }
        },
        async confirmDelete() {
            this.deleteDialog = false; // Close the delete dialog
            try {
                await this.$axios.delete(
                    `/groups/${this.selectedGroupId}/forceDelete`
                );
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Group deleted successfully!");
            } catch (error) {
                console.error("Error deleting brand:", error);
                toast.error("Failed to delete brand.");
            }
        },
         async GroupIndex (){
            this.$router.push({ name: "GroupIndex" });
        }
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
