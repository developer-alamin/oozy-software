<template>
    <v-container class="mt-5">
        <!-- <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="text-center">
                        <apexchart
                            type="radialBar"
                            :options="gaugeOptions"
                            :series="gaugeSeries"
                        />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="text-center">
                        <apexchart
                            type="donut"
                            :options="donutOptions"
                            :series="donutSeries"
                        />
                    </div>
                </div>
            </div>
        </div> -->
        <v-row>
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>Machine Uptime (Last 10 Days)</v-card-title>
                    <v-card-text>
                        <div class="text-center">
                            <!-- ApexCharts Gauge Chart for Machine Uptime -->
                            <apexchart
                                type="radialBar"
                                :options="gaugeOptions"
                                :series="gaugeSeries"
                            />
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title
                        >Machine Uptime Breakdown (Last 10 Days)</v-card-title
                    >
                    <v-card-text>
                        <div class="text-center">
                            <apexchart
                                type="donut"
                                :options="donutOptions"
                                :series="donutSeries"
                            />
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Refresh Button -->
        <v-row justify="center">
            <v-col cols="12" class="text-center">
                <v-btn color="primary" @click="refreshData">Refresh Data</v-btn>
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
    max-width: 480px;
    max-height: 380px;
    margin: auto;
}
</style>
