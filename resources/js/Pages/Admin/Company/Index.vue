<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="4"><span>Company List</span></v-col>
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
                    <v-btn
                        v-if="isAuthorized"
                        @click="createUser"
                        class="primary-color"
                        icon
                        style="width: 40px; height: 40px"
                    >
                        <v-tooltip location="top" activator="parent">
                            <template v-slot:activator="{ props }">
                                <v-icon v-bind="props" style="font-size: 20px"
                                    >mdi-plus</v-icon
                                >
                            </template>
                            <span>Add New</span>
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
                                <span>View In Trash</span>
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
        <template v-slot:item.user.name="{ item }">
            {{ item.user && item.user.name ? item.user.name : "N\\A" }}
        </template>
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
                <v-icon @click="editCompany(item.id)" class="mr-2"
                    >mdi-pencil</v-icon
                >
                <v-icon @click="showConfirmDialog(item.id)" color="red"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table-server>

        <ConfirmDialog
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
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: "Name", key: "name", sortable: true },
                { title: "User's Name", key: "user.name", sortable: true },
                { title: "Status", key: "status", sortable: true },
                { title: "Actions", key: "actions", sortable: false },
            ],

            serverItems: [],
            loading: true,
            totalItems: 0,
            dialog: false,
            selectedCompanyId: null,
            trashedCount: 0,
            user: {},
        };
    },
    computed: {
        // Check if the user is authorized to create Users
        isAuthorized() {
            // You can customize the logic based on your needs
            return (
                this.user.superadmin === true ||
                this.user.admin === true ||
                this.user.user_role === true
            );
        },
    },

    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "created_at";
            try {
                const response = await this.$axios.get(
                    "/company",
                    {
                        params: {
                            page,
                            itemsPerPage,
                            sortBy: sortKey,
                            sortOrder,
                            search: this.search,
                        },
                    }
                );
                this.serverItems = response.data.items || [];
                this.totalItems = response.data.total || 0;
                this.fetchTrashedCompanyCount();
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        createUser() {
            this.$router.push({ name: "CompanyCreate" });
        },
        viewTrash() {
            this.$router.push({ name: "CompanyTrash" });
        },
        editCompany(id) {
            this.$router.push({ name: "CompanyEdit", params: { id } });
        },
        showConfirmDialog(id) {
            this.selectedCompanyId = id;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                await this.$axios.delete(`/company/${this.selectedCompanyId}`);
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("Company deleted successfully!");
            } catch (error) {
                console.error("Error deleting user:", error);
                toast.error("Failed to delete user.");
            }
        },
        async fetchTrashedCompanyCount() {
            try {
                const response = await this.$axios.get("/company/trashed-count");
                this.trashedCount = response.data.trashedCount
                    ? response.data.trashedCount
                    : 0;
            } catch (error) {
                console.error("Error fetching trashed Users count:", error);
            }
        },
        async fetchUserInfo() {
            try {
                const response = await this.$axios.get("/user/role/auth");
                this.user = response.data;
            } catch (error) {
                console.error("Error fetching user info:", error);
            }
        },
    },

    created() {
        this.loadItems({
            page: 1,
            itemsPerPage: this.itemsPerPage,
            sortBy: [],
        });
        this.fetchUserInfo();
        this.fetchTrashedCompanyCount();
    },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
