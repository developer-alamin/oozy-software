<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Supplier List</span></v-col>

                <v-col cols="6" class="d-flex">
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
                        @click="createSupplier"
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
                            <span>Add a new Supplier</span>
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
                                <span>View Trashed Rents</span>
                            </v-tooltip>
                        </v-btn>
                    </v-badge>
                </v-col>
            </v-row>
        </v-card-title>

        <v-data-table
            :headers="headers"
            :items="suppliers"
            :items-per-page="itemsPerPage"
            :search="search"
            :loading="loading"
            class="elevation-1"
            @update:options="updateOptions"
        >
            <template v-slot:item.creator_name="{ item }">
                <span>{{ item.creator ? item.creator : "Unknown" }}</span>
            </template>
            <template v-slot:item.photo="{ item }">
                <img class="rentsImg" :src="item.photo" alt="" />
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon @click="editSupplier(item.uuid)" class="mr-2"
                    >mdi-pencil</v-icon
                >
                <v-icon @click="showConfirmDialog(item.id)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table>
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
import ConfirmDialog from "../../Components/ConfirmDialog.vue";
export default {
    components: {
        ConfirmDialog,
    },
    data() {
        return {
            dialogName: "Are you sure you want to delete this suppliers ?",
            dialog: false,
            suppliers: [],
            search: "",
            itemsPerPage: 15,
            pagination: {
                page: 1, // Current page
            },
            totalPages: 0, // Total number of pages
            loading: false,
            sortBy: "name", // Default sorting column
            sortDesc: false, // Default sort direction
            trashedCount: 0,
            headers: [
                { title: "Type", key: "type", sortable: false },
                {
                    title: "Name",
                    value: "name",
                    sortable: true, // Enable sorting
                    align: "start",
                },
                {
                    title: "Email",
                    value: "email",
                    sortable: true, // Enable sorting
                    align: "start",
                },
                {
                    title: "Phone",
                    value: "phone",
                    sortable: true, // Enable sorting
                    align: "start",
                },
                {
                    title: "Contact Person",
                    value: "contact_person",
                    sortable: true, // Enable sorting
                    align: "start",
                },
                { title: "Creator", key: "creator.name", sortable: false },
                { title: "Photo", key: "photo", sortable: false },
                { title: "Actions", value: "actions", sortable: false },
            ],
        };
    },
    created() {
        this.fetchSuppliers();
        this.fetchTrashedSupplierCount();
    },
    methods: {
        async fetchSuppliers() {
            this.loading = true; // Start loading
            try {
                const response = await this.$axios.get("/suppliers", {
                    params: {
                        search: this.search,
                        itemsPerPage: this.itemsPerPage,
                        page: this.pagination.page, // Include current page
                        sortBy: this.sortBy, // Include sortBy
                        sortDesc: this.sortDesc, // Include sort direction
                    },
                });
                this.fetchTrashedSupplierCount();
                this.suppliers = response.data.suppliers;
                this.loading = false; // Stop loading
            } catch (error) {
                console.error("Error fetching suppliers:", error);
                this.loading = false; // Stop loading even on error
            }
        },
        updateOptions(options) {
            // Ensure sortBy is a string and sortDesc is a boolean
            this.itemsPerPage = options.itemsPerPage;
            this.pagination.page = 1;
            this.fetchSuppliers(); // Refetch suppliers with updated options
            this.fetchTrashedSupplierCount();
        },
        viewTrash() {
            this.$router.push({ name: "SupplierTrash" });
        },
        createSupplier() {
            this.$router.push({ name: "SupplierCreate" });
        },
        editSupplier(uuid) {
            this.$router.push({ name: "SupplierEdit", params: { uuid } });
        },
        async showConfirmDialog(id) {
            this.selectedSupplierId = id;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                const response = await this.$axios.delete(
                    `/suppliers/${this.selectedSupplierId}`
                );
                this.fetchSuppliers({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                this.fetchTrashedSupplierCount();
                toast.success("suppliers deleted successfully!");
            } catch (error) {
                console.error("Error deleting Rent:", error);
                toast.error("Failed to delete Rent.");
            }
        },
        async fetchTrashedSupplierCount() {
            try {
                const response = await this.$axios.get(
                    "/supplier/trashed-count"
                );
                this.trashedCount = response.data.trashedCount;
            } catch (error) {
                console.error("Error fetching trashed supplier count:", error);
            }
        },

        // Method to handle sorting
        sortSuppliers(column) {
            if (this.sortBy === column) {
                this.sortDesc = !this.sortDesc; // Toggle sort direction
            } else {
                this.sortBy = column; // Set new sort column
                this.sortDesc = false; // Reset to ascending
            }
            this.fetchSuppliers(); // Refetch units with the updated sort options
            this.fetchTrashedSupplierCount();
        },
    },
};
</script>

<style scoped>
/* Add custom styles here */
</style>
