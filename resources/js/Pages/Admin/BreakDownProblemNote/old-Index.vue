<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Categories List</span></v-col>

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
                    <v-btn @click="createCategory" color="primary"
                        >Add Category</v-btn
                    >
                </v-col>
            </v-row>
        </v-card-title>

        <v-data-table
            :headers="headers"
            :items="categories"
            :items-per-page="itemsPerPage"
            :search="search"
            :loading="loading"
            loading-text="Loading... Please wait"
            class="elevation-1"
            :items-length="totalItems"
            @update:options="updateOptions"
        >
            <template v-slot:item.status="{ item }">
                <v-chip
                    :color="
                        item.status == 'true' || item.status == true
                            ? 'green'
                            : 'red'
                    "
                    :text="
                        item.status == 'true' || item.status == true
                            ? 'Active'
                            : 'In-active'
                    "
                    class="text-uppercase"
                    size="small"
                    label
                ></v-chip>
                <!-- <span
                    :class="{
                        'text-green':
                            item.status == 'true' || item.status == true,
                        'text-red':
                            item.status == 'false' || item.status == false,
                    }"
                >
                    {{
                        item.status == "true" || item.status == true
                            ? "Active"
                            : "In-Active"
                    }}
                </span> -->
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon @click="editSupplier(item.id)" class="mr-2"
                    >mdi-pencil</v-icon
                >
                <v-icon @click="deleteSupplier(item.id)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
            <!-- Add pagination controls -->
            <template v-slot:footer>
                <v-pagination
                    v-model:items-per-page="itemsPerPage"
                    :length="totalPages"
                    @input="fetchCategories"
                ></v-pagination>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            categories: [],
            search: "",
            itemsPerPage: 5,
            totalItems: 0,
            pagination: {
                page: 1, // Current page
            },
            totalPages: 0, // Total number of pages
            loading: false,
            sortBy: "name", // Default sorting column
            sortDesc: false, // Default sort direction
            headers: [
                {
                    title: "Category Name",
                    value: "name",
                    sortable: true, // Enable sorting
                    align: "start",
                },

                {
                    title: "Description",
                    value: "description",
                    sortable: false,
                },
                {
                    title: "Status",
                    value: "status",
                    sortable: true, // Enable sorting
                },
                {
                    title: "Actions",
                    value: "actions",
                    sortable: false,
                },
            ],
        };
    },
    created() {
        this.fetchCategories();
    },
    methods: {
        async fetchCategories() {
            this.loading = true; // Start loading
            try {
                const response = await this.$axios.get("/category", {
                    params: {
                        search: this.search,
                        itemsPerPage: this.itemsPerPage,
                        page: this.pagination.page, // Include current page
                        sortBy: this.sortBy, // Include sortBy
                        sortDesc: this.sortDesc, // Include sort direction
                    },
                });
                this.totalItems = response.data.total;
                console.log(this.totalItems);

                this.categories = response.data.categories;
                this.totalPages = Math.ceil(
                    response.data.total / this.itemsPerPage
                ); // Calculate total pages
                this.loading = false; // Stop loading
            } catch (error) {
                console.error("Error fetching categories:", error);
                this.loading = false; // Stop loading even on error
            }
        },
        updateOptions(options) {
            this.itemsPerPage = options.itemsPerPage;
            this.pagination.page = 1; // Reset to the first page on items per page change
            this.fetchCategories(); // Refetch categories with updated options
        },
        createCategory() {
            this.$router.push({ name: "CategoryCreate" });
        },
        editSupplier(id) {
            this.$router.push({ name: "CategoryEdit", params: { id } });
        },
        async deleteSupplier(id) {
            const confirmDelete = confirm(
                "Are you sure you want to delete this category?"
            );
            if (confirmDelete) {
                try {
                    await this.$axios.delete(`/category/${id}`);
                    this.fetchCategories(); // Refresh the categories list
                } catch (error) {
                    console.error("Error deleting categories:", error);
                }
            }
        },
        // Method to handle sorting
        sortCategories(column) {
            if (this.sortBy === column) {
                this.sortDesc = !this.sortDesc; // Toggle sort direction
            } else {
                this.sortBy = column; // Set new sort column
                this.sortDesc = false; // Reset to ascending
            }
            this.fetchCategories(); // Refetch categories with the updated sort options
        },
    },
};
</script>

<style scoped>
/* Add custom styles here */
</style>
