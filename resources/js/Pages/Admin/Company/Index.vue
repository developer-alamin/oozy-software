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
                            <span>Add a new user</span>
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
                                <span>View trashed Users</span>
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
                <v-icon @click="editUser(item.id)" class="mr-2"
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
                { title: "Email", key: "email", sortable: true },
                { title: "Phone", key: "phone", sortable: true },
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
            dialog: false,
            selectedUserId: null,
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
                    "/admin/company/user/all",
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
                console.log(response.data.items);
                this.serverItems = response.data.items || [];
                // console.log(this.serverItems);

                this.totalItems = response.data.total || 0;
                this.fetchTrashedUsersCount();
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        createUser() {
            this.$router.push({ name: "UserCreate" });
        },
        viewTrash() {
            this.$router.push({ name: "AdminUserTrash" });
        },
        editUser(id) {
            this.$router.push({ name: "AdminUserEdit", params: { id } });
        },
        showConfirmDialog(id) {
            this.selectedUserId = id;
            this.dialog = true;
        },
        async confirmDelete() {
            this.dialog = false; // Close the dialog
            try {
                await this.$axios.delete(`/user/${this.selectedUserId}`);
                this.loadItems({
                    page: 1,
                    itemsPerPage: this.itemsPerPage,
                    sortBy: [],
                });
                toast.success("User deleted successfully!");
            } catch (error) {
                console.error("Error deleting user:", error);
                toast.error("Failed to delete user.");
            }
        },
        async fetchTrashedUsersCount() {
            try {
                const response = await this.$axios.get("/user/trashed-count");
                this.trashedCount = response.data.trashedCount
                    ? response.data.trashedCount
                    : 0;
            } catch (error) {
                console.error("Error fetching trashed Users count:", error);
            }
        },
        async fetchUserInfo() {
            try {
                const response = await this.$axios.get("/user/role/auth"); // Adjust to your API
                this.user = response.data; // Ensure you set user role data here
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
        this.fetchTrashedUsersCount();
    },
};
</script>

<style scoped>
/* Optional: Add styles for the main component */
</style>
