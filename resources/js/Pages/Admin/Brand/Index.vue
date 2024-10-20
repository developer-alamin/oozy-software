<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="6"><span>Brand List</span></v-col>

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
                    <v-btn @click="createBrand" color="primary"
                        >Add Brand</v-btn
                    >
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
                    :color="
                        item.status == 'Active' || item.status === true
                            ? 'green'
                            : 'red'
                    "
                    class="text-uppercase"
                    size="small"
                    label
                >
                    {{
                        item.status == "Active" || item.status === true
                            ? "Active"
                            : "In-active"
                    }}
                </v-chip>
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon @click="editBrand(item.id)" class="mr-2"
                    >mdi-pencil</v-icon
                >
                <v-icon @click="deleteBrand(item.id)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table-server>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Brand Name", key: "name", sortable: true },
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
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get("/brand", {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortKey,
                        sortOrder,
                        search: this.search,
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
        createBrand() {
            this.$router.push({ name: "BrandCreate" });
        },
        editBrand(id) {
            this.$router.push({ name: "BrandEdit", params: { id } });
        },
        deleteBrand(id) {
            // Logic for deleting a brand
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
/* Optional: Add any styles here */
</style>
