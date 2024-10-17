<!-- <template>
    <v-container>
        <supplier-index ref="SupplierIndex" />
    </v-container>
</template>

<script>
import SupplierIndex from "../../Components/Admin/Supplier/SupplierIndex.vue";

export default {
    components: {
        SupplierIndex,
    },
};
</script> -->

<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>
            <span>Supplier List</span>
            <v-spacer></v-spacer>
            <v-btn @click="createSupplier" color="primary">Add Supplier</v-btn>
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
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title>Suppliers</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        label="Search"
                        class="mx-4"
                        solo
                        hide-details
                    ></v-text-field>
                </v-toolbar>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon @click="editSupplier(item.id)" class="mr-2"
                    >mdi-pencil</v-icon
                >
                <v-icon @click="deleteSupplier(item.id)">mdi-delete</v-icon>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            suppliers: [],
            search: "",
            itemsPerPage: 5,
            sortBy: "name", // Default sorting column
            sortDesc: false, // Default sort direction
            loading: false,
            headers: [
                { title: "Name", value: "name" },
                { title: "Email", value: "email" },
                { title: "Phone", value: "phone" },
                { title: "Contact Person", value: "contact_person" },
                { title: "Actions", value: "actions", sortable: false },
            ],
        };
    },
    created() {
        this.fetchSuppliers();
    },
    methods: {
        async fetchSuppliers() {
            this.loading = true; // Start loading
            try {
                const response = await this.$axios.get("/suppliers", {
                    params: {
                        search: this.search,
                        itemsPerPage: this.itemsPerPage,
                        sortBy: this.sortBy,
                        sortDesc: this.sortDesc,
                    },
                });
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
            this.sortBy = options.sortBy; // Should be a string
            this.sortDesc = options.sortDesc; // Should be a boolean
            this.fetchSuppliers(); // Refetch suppliers with updated options
        },
        createSupplier() {
            this.$router.push({ name: "SupplierCreate" });
        },
        editSupplier(id) {
            this.$router.push({ name: "SupplierEdit", params: { id } });
        },
        async deleteSupplier(id) {
            const confirmDelete = confirm(
                "Are you sure you want to delete this supplier?"
            );
            if (confirmDelete) {
                try {
                    await this.$axios.delete(`/suppliers/${id}`);
                    this.fetchSuppliers(); // Refresh the supplier list
                } catch (error) {
                    console.error("Error deleting supplier:", error);
                }
            }
        },
    },
};
</script>

<style scoped>
/* Add custom styles here */
</style>
