<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Models List</span></v-col>

                <!-- <v-toolbar-title>Models</v-toolbar-title> -->

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
                    <v-btn @click="createModels" color="primary"
                        >Add Model</v-btn
                    >
                </v-col>
            </v-row>
        </v-card-title>

        <v-data-table
            :headers="headers"
            :items="models"
            :items-per-page="itemsPerPage"
            :search="search"
            :loading="loading"
            loading-text="Loading... Please wait"
            class="elevation-1"
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
                    v-model="pagination.page"
                    :length="totalPages"
                    @input="fetchModels"
                ></v-pagination>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            models: [],
            search: "",
            itemsPerPage: 15,
            pagination: {
                page: 1, // Current page
            },
            totalPages: 0, // Total number of pages
            loading: false,
            sortBy: "name", // Default sorting column
            sortDesc: false, // Default sort direction
            headers: [
                {
                    title: "Model Name",
                    value: "name",
                    sortable: true, // Enable sorting
                    align: "start",
                },
                {
                    title: "Model Number",
                    value: "model_number",
                    sortable: true, // Enable sorting
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
        this.fetchModels();
    },
    methods: {
        async fetchModels() {
            this.loading = true; // Start loading
            try {
                const response = await this.$axios.get("/models", {
                    params: {
                        search: this.search,
                        itemsPerPage: this.itemsPerPage,
                        page: this.pagination.page, // Include current page
                        sortBy: this.sortBy, // Include sortBy
                        sortDesc: this.sortDesc, // Include sort direction
                    },
                });
                console.log(response.data);

                this.models = response.data.models;
                this.totalPages = Math.ceil(
                    response.data.total / this.itemsPerPage
                ); // Calculate total pages
                this.loading = false; // Stop loading
            } catch (error) {
                console.error("Error fetching models:", error);
                this.loading = false; // Stop loading even on error
            }
        },
        updateOptions(options) {
            this.itemsPerPage = options.itemsPerPage;
            this.pagination.page = 1; // Reset to the first page on items per page change
            this.fetchModels(); // Refetch models with updated options
        },
        createModels() {
            this.$router.push({ name: "ModelCreate" });
        },
        editSupplier(id) {
            this.$router.push({ name: "ModelEdit", params: { id } });
        },
        async deleteSupplier(id) {
            const confirmDelete = confirm(
                "Are you sure you want to delete this model?"
            );
            if (confirmDelete) {
                try {
                    await this.$axios.delete(`/models/${id}`);
                    this.fetchModels(); // Refresh the models list
                } catch (error) {
                    console.error("Error deleting models:", error);
                }
            }
        },
        // Method to handle sorting
        sortModels(column) {
            if (this.sortBy === column) {
                this.sortDesc = !this.sortDesc; // Toggle sort direction
            } else {
                this.sortBy = column; // Set new sort column
                this.sortDesc = false; // Reset to ascending
            }
            this.fetchModels(); // Refetch models with the updated sort options
        },
    },
};
</script>

<style scoped>
/* Add custom styles here */
</style>
