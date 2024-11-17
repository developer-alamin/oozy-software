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
                    <v-btn
                        @click="searchByFactoryCode"
                        color="primary"
                        class="mr-2"
                    >
                        <v-icon left>mdi-magnify</v-icon>
                        <!-- Adds the magnifying glass icon -->
                        <!-- Search -->
                    </v-btn>
                    <v-btn
                        @click="FactoryCreate"
                        color="primary "
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
                                <span>View trashed brands</span>
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
            <template v-slot:item.name="{ item }">
                <span
                    @click="showFloors(item)"
                    style="cursor: pointer; color: blue"
                >
                    {{ item.name }}
                </span>
            </template>
            <template v-slot:item.actions="{ item }">
                <!-- <v-icon @click="editFactory(item.uuid)" class="mr-2"
                    >mdi-pencil</v-icon
                > -->
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
        <FactoryFloorsModal
            :visible="showModal"
            :factory="selectedFactory"
            @update:visible="showModal = $event"
            @close="showModal = false"
        />
    </v-card>
</template>

<script>
import FactoryFloorsModal from "../../Components/FactoryFloorsModal.vue";

export default {
    components: {
        FactoryFloorsModal,
    },
    data() {
        return {
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
            trashedCount: 0,
            totalItems: 0,
            showModal: false,
            selectedFactory: {},
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
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        showFloors(factory) {
            this.selectedFactory = factory; // Set the selected factory
            this.showModal = true; // Show the modal
        },
        searchByFactoryCode() {
            this.search = ""; // Reset other search filters if desired
            this.loadItems({
                page: 1,
                itemsPerPage: this.itemsPerPage,
                sortBy: [],
            });
        },
        FactoryCreate() {
            this.$router.push({ name: "FactoryCreate" });
        },
        editFactory(uuid) {
            this.$router.push({ name: "FactoryEdit", params: { uuid } });
        },
        showConfirmDialog(id) {
            this.selectedFactoryId = id;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                await this.$axios.delete(`/brand/${this.selectedFactoryId}`);
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Brand deleted successfully!");
            } catch (error) {
                console.error("Error deleting brand:", error);
                toast.error("Failed to delete brand.");
            }
        },
        async fetchTrashedFactoriesCount() {
            try {
                const response = await this.$axios.get(
                    "/factory/trashed-count"
                );
                this.trashedCount = response.data.trashedCount;
            } catch (error) {
                console.error("Error fetching trashed factories count:", error);
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

<style scoped>
/* Optional: Add styles for the main component */
</style>
