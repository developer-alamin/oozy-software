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
            :items-per-page="5"
            :search="search"
            class="elevation-1"
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
            <template v-slot:item.photo="{ item }">
                <v-img
                    :src="item.photo ? item.photo : 'default-image-path.jpg'"
                    max-width="50"
                    max-height="50"
                    class="mr-2"
                ></v-img>
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
            headers: [
                { text: "Photo", value: "photo", sortable: false },
                { text: "Name", value: "name" },
                { text: "Email", value: "email" },
                { text: "Phone", value: "phone" },
                { text: "Contact Person", value: "contact_person" },
                { text: "Actions", value: "actions", sortable: false },
            ],
        };
    },
    created() {
        this.fetchSuppliers();
    },
    methods: {
        async fetchSuppliers() {
            try {
                const response = await this.$axios.get("/api/suppliers");
                this.suppliers = response.data.suppliers;
            } catch (error) {
                console.error("Error fetching suppliers:", error);
            }
        },
        createSupplier() {
            this.$router.push({ name: "supplier.create" });
        },
        editSupplier(id) {
            this.$router.push({ name: "supplier.edit", params: { id } });
        },
        async deleteSupplier(id) {
            const confirmDelete = confirm(
                "Are you sure you want to delete this supplier?"
            );
            if (confirmDelete) {
                try {
                    await this.$axios.delete(`/api/suppliers/${id}`);
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
