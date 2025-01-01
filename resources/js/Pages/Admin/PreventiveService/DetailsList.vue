<template>
    <v-card>
        <v-card-title class="pt-5">
            <v-row>
                <v-col cols="4"><span>Preventive Service Details</span></v-col>
                <v-col cols="8" class="d-flex justify-end">
                    <!-- Search Field -->
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
                </v-col>
            </v-row>
        </v-card-title>

        <!-- Data Table -->
        <v-data-table-server
            v-model:items-per-page="itemsPerPage"
            :headers="headers"
            :search="search"
            :items="serverItems"
            :items-length="totalItems"
            :loading="loading"
            item-value="service_start_date_time"
            loading-text="Loading... Please wait"
            @update:options="loadItems"
        >
            <!-- Status Column -->
            <template v-slot:item.status="{ item }">
                <v-chip
                    :color="getStatusColor(item.status)"
                    class="text-uppercase"
                    size="small"
                    label
                >
                    {{ item.status }}
                </v-chip>
            </template>

            <!-- Technician Status Column -->
            <template v-slot:item.technician_status="{ item }">
                <v-chip
                    :color="getTechnicianStatusColor(item.technician_status)"
                    class="text-uppercase"
                    size="small"
                    label
                >
                    {{ item.technician_status }}
                </v-chip>
            </template>

            <!-- Date Columns -->
            <template v-slot:item.acknowledge_date_time="{ item }">
                <span>{{ item.acknowledge_date_time || 'Not Available' }}</span>
            </template>
            <template v-slot:item.service_start_date_time="{ item }">
                <span>{{ item.service_start_date_time || 'Not Available' }}</span>
            </template>
            <template v-slot:item.service_end_date_time="{ item }">
                <span>{{ item.service_end_date_time || 'Not Available' }}</span>
            </template>

            <!-- Additional Columns -->
            <template v-slot:item.problem_note_id="{ item }">
                <span>{{ item.problem_note_id || 'Not Provided' }}</span>
            </template>
            <template v-slot:item.note="{ item }">
                <span>{{ item.note || 'No Note' }}</span>
            </template>
            <template v-slot:item.parts_info="{ item }">
                <span>{{ item.parts_info || 'Not Available' }}</span>
            </template>

            <!-- Actions Column -->
            <template v-slot:item.actions="{ item }">
                <v-icon @click="signleService(item.uuid)" color="green" class="mr-2">
                    mdi-eye
                </v-icon>
            </template>
        </v-data-table-server>
    </v-card>
</template>

<script>
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            search: "",
            itemsPerPage: 15,
            headers: [
                { title: 'Status', key: 'status', sortable: true },
                { title: 'Technician Status', key: 'technician_status', sortable: false },
                { title: 'Acknowledge Date', key: 'acknowledge_date_time', sortable: false },
                { title: 'Service Start Date', key: 'service_start_date_time', sortable: false },
                { title: 'Service End Date', key: 'service_end_date_time', sortable: false },
                { title: 'Note', key: 'note', sortable: false },
                { title: 'Actions', key: 'actions', sortable: false },
            ],
            serverItems: [],
            loading: true,
            totalItems: 0,
            uuid: this.$route.params.uuid, // Fetch UUID from route params
        };
    },
    methods: {
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.loading = true;
            const sortOrder = sortBy.length ? sortBy[0].order : "desc";
            const sortKey = sortBy.length ? sortBy[0].key : "service_start_date_time";

            try {
                const response = await this.$axios.get(`/preventive-service/${this.uuid}/details-list`, {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortKey,
                        sortOrder,
                        search: this.search,
                    },
                });                
                this.serverItems = response.data.items || [];
                this.totalItems = response.data.total || 0;
            } catch (error) {
                console.error("Error loading items:", error);
            } finally {
                this.loading = false;
            }
        },
        signleService(uuid) {
            // Navigate to the edit page with the selected serviceId
            this.$router.push({ name:"SignleServiceDetails",params:{uuid}});
        },
        getStatusColor(status) {
        switch (status) {
            case 'Processing':
            return 'orange'; // Color for Processing status
            case 'Done':
            return 'green'; // Color for Done status
            case 'Cancel':
            return 'red'; // Color for Cancel status
            default:
            return 'grey'; // Default color for unknown status
        }
        },
         // Function to dynamically return color based on technician status
        getTechnicianStatusColor(status) {
        switch (status) {
            case 'Acknowledge':
            return 'blue'; // Color for Acknowledge status
            case 'Acknowledged':
            return 'lightblue'; // Color for Acknowledged status
            case 'Start Service':
            return 'yellow'; // Color for Start Service status
            case 'Done':
            return 'green'; // Color for Done status
            case 'Failed':
            return 'red'; // Color for Failed status
            default:
            return 'grey'; // Default color for unknown status
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
