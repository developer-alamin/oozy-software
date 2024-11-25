<script setup lang="ts">
import BarChart from "@/Pages/Chart/BarChart.vue";
import PieChart from "@/Pages/Chart/PieChart.vue";
import MonochromePieChart from "@/Pages/Chart/MonochromePieChart.vue";
import MachineUtilization from "@/Pages/Chart/MachineUtilization.vue";
import MachineAvailability from "@/Pages/Chart/MachineAvailability.vue";
import MachineUptime from "../Chart/MachineUptime.vue";
import { useAxios } from "@/composables/useAxios";

const { makeRequest, isLoading } = useAxios();
</script>
<template>
    <div>
        <!-- <h2>Admin Dashboard</h2> -->
        <h5 class="text-center">Welcome to the admin dashboard!</h5>
        <BarChart />
        <!-- <BarChart /> -->
        <!-- <MonochromePieChart /> -->
        <MachineUtilization />
        <MachineAvailability />
        <MachineUptime />
    </div>
</template>

<!-- <script>
import PieChart from "../Chart/BarChart.vue";
import BarChart from "../Chart/PieChart.vue";
import MonochromePieChart from "../Chart/MonochromePieChart.vue";
import MachineUtilization from "../Chart/MachineUtilization.vue";
import MachineAvailability from "../Chart/MachineAvailability.vue";
import MachineUptime from "../Chart/MachineUptime.vue";

export default {
    components: {
        PieChart,
        BarChart,
        MonochromePieChart,
        MachineUtilization,
        MachineAvailability,
        MachineUptime,
    },
};
</script> -->
