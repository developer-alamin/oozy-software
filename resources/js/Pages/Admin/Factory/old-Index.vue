<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="4"><span>Factory List</span></v-col>
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
                    <v-text-field
                        v-model="factoryCode"
                        density="compact"
                        label="Search by Factory Code"
                        prepend-inner-icon="mdi-barcode-scan"
                        variant="solo-filled"
                        class="mx-4"
                        flat
                        hide-details
                        solo
                        single-line
                        clearable
                    ></v-text-field>
                    <v-btn @click="searchByFactoryCode" color="primary"
                        >Search</v-btn
                    >
                    <v-btn
                        @click="FactoryCreate"
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
                            <span>Add a new Factory</span>
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
                                <span>View Trashed Factories</span>
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
            <template v-slot:item.creator_name="{ item }">
                <span>{{ item.creator ? item.creator : "Unknown" }}</span>
            </template>
            <template v-slot:item.factory_name="{ item }">
                <span
                    @click="showFloors(item)"
                    style="cursor: pointer; color: blue"
                >
                    {{ item.name }}
                </span>
            </template>
            <template v-slot:item.factory_code="{ item }">
                <span>{{ item.factory_code || "" }}</span>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon @click="editFactory(item.uuid)" class="mr-2"
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
import ConfirmDialog from "../../Components/ConfirmDialog.vue";

export default {
    components: {
        ConfirmDialog,
    },
    data() {
        return {
            dialogName: "Are you sure you want to delete this Factory ?",
            search: "",
            factoryCode: "",
            itemsPerPage: 15,
            headers: [
                { title: "Company Name", key: "user.name", sortable: true },
                { title: "Factory Name", key: "name", sortable: true },
                { title: "Factory Code", key: "factory_code", sortable: true },
                { title: "Email", key: "email", sortable: true },
                { title: "Phone", key: "phone", sortable: false },
                { title: "Creator", key: "creator.name", sortable: false },
                { title: "Actions", key: "actions", sortable: false },
            ],
            serverItems: [],
            loading: true,
            totalItems: 0,
            dialog: false,
            selectedFactoryId: null,
            trashedCount: 0,
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/factory", {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortKey,
                        sortOrder,
                        search: this.search,
                        factory_code: this.factoryCode,
                    },
                });
                // console.log(response.data.items);
                this.serverItems = response.data.items || [];
                this.totalItems = response.data.total || 0;
                this.fetchTrashedFactorysCount();
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        searchByFactoryCode() {
            // Reset other search filters if desired
            this.search = "";
            this.loadItems({
                page: 1,
                itemsPerPage: this.itemsPerPage,
                sortBy: [],
            });
        },
        FactoryCreate() {
            this.$router.push({ name: "FactoryCreate" });
        },
        viewTrash() {
            this.$router.push({ name: "FactoryTrash" });
        },
        editFactory(uuid) {
            this.$router.push({ name: "FactoryEdit", params: { uuid } });
        },
        showConfirmDialog(uuid) {
            this.selectedFactoryId = uuid;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                await this.$axios.delete(`/factory/${this.selectedFactoryId}`);
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Factory deleted successfully!");
            } catch (error) {
                toast.error("Failed to delete factory.");
            }
        },
        async fetchTrashedFactorysCount() {
            try {
                const response = await this.$axios.get(
                    "/factory/trashed-count"
                );
                this.trashedCount = response.data.trashedCount;
            } catch (error) {
                console.error("Error fetching trashed factory count:", error);
            }
        },
    },

    created() {
        this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
        });
        this.fetchTrashedFactorysCount();
    },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
