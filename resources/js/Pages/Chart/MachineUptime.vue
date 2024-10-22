<template>
    <v-container class="mt-5">
        <v-row justify="center">
            <v-col cols="12" md="6">
                <v-card elevation="2">
                    <v-card-title> Machine Uptime (Last 10 Days) </v-card-title>
                    <v-card-subtitle>
                        Percentage of time machines are operational and
                        available.
                    </v-card-subtitle>

                    <v-card-text>
                        <div class="text-center">
                            <!-- ApexCharts Gauge Chart for Machine Uptime -->
                            <apexchart
                                type="radialBar"
                                :options="gaugeOptions"
                                :series="gaugeSeries"
                            ></apexchart>
                        </div>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-text>
                        <div class="text-center">
                            <!-- Donut Chart for the last 10 days -->
                            <apexchart
                                type="donut"
                                :options="donutOptions"
                                :series="donutSeries"
                            ></apexchart>
                        </div>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn color="primary" @click="refreshData"
                            >Refresh Data</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import VueApexCharts from "vue3-apexcharts";
import { ref } from "vue";

// Initial machine uptime data for the last 10 days (in percentages)
const machineUptimeData = ref([85, 90, 78, 95, 88, 92, 80, 91, 89, 94]);

// ApexCharts Gauge Chart Options
const gaugeOptions = {
    chart: {
        type: "radialBar",
    },
    plotOptions: {
        radialBar: {
            dataLabels: {
                total: {
                    show: true,
                    label: "Average Uptime",
                    formatter: function () {
                        const totalUptime = machineUptimeData.value.reduce(
                            (a, b) => a + b,
                            0
                        );
                        const avgUptime =
                            totalUptime / machineUptimeData.value.length;
                        return `${avgUptime.toFixed(2)}%`;
                    },
                },
            },
        },
    },
    labels: ["Uptime"],
};

// Reactive series for Gauge Chart (Last day’s uptime)
const gaugeSeries = ref([
    machineUptimeData.value[machineUptimeData.value.length - 1],
]);

// ApexCharts Donut Chart Options
const donutOptions = {
    chart: {
        type: "donut",
    },
    labels: [
        "Day 1",
        "Day 2",
        "Day 3",
        "Day 4",
        "Day 5",
        "Day 6",
        "Day 7",
        "Day 8",
        "Day 9",
        "Day 10",
    ],
    title: {
        text: "Machine Uptime (Last 10 Days)",
    },
};

// Reactive series for Donut Chart (10 days of uptime)
const donutSeries = ref(machineUptimeData.value);

// Simulate refreshing uptime data (generating random uptime values)
const refreshData = () => {
    // Simulate fetching new data, generate random uptime values between 70% and 100%
    const newUptimeData = Array.from(
        { length: 10 },
        () => Math.floor(Math.random() * 30) + 70
    );

    // Update the reactive data to refresh the charts
    machineUptimeData.value = newUptimeData;
    gaugeSeries.value = [newUptimeData[newUptimeData.length - 1]]; // Last day’s uptime
    donutSeries.value = newUptimeData; // Update the donut chart series
};
</script>

<style scoped>
.v-card {
    max-width: 500px;
    margin: auto;
}
</style>
