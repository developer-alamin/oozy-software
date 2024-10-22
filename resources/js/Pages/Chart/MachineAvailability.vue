<template>
    <v-container>
        <v-row>
            <!-- Gauge Chart for Machine Availability -->
            <v-col cols="12" md="12">
                <v-card>
                    <v-card-title
                        >Machine Availability - Gauge Chart</v-card-title
                    >
                    <v-card-text>
                        <apexchart
                            type="radialBar"
                            height="350"
                            :options="gaugeChartOptions"
                            :series="gaugeChartSeries"
                        />
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Donut Chart for Machine Availability vs Unavailability -->
            <v-col cols="12" md="12">
                <v-card>
                    <v-card-title
                        >Machine Availability - Donut Chart</v-card-title
                    >
                    <v-card-text>
                        <apexchart
                            type="donut"
                            height="350"
                            :options="donutChartOptions"
                            :series="donutChartSeries"
                        />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";

export default {
    components: {
        apexchart: VueApexCharts,
    },
    data() {
        // Total machines available and required for the last 10 days
        const availableMachines = [8, 9, 7, 6, 10, 9, 8, 7, 9, 8]; // available machines for 10 days
        const requiredMachines = 10; // required machines per day

        // Calculate availability for each day using the formula: MIN (100, (Total available / Total required) * 100)
        const availabilityRates = availableMachines.map((available) =>
            Math.min(100, (available / requiredMachines) * 100)
        );

        // Overall availability calculation for the last 10 days
        const totalAvailable = availableMachines.reduce(
            (acc, val) => acc + val,
            0
        );
        const totalRequired = requiredMachines * 10; // 10 days worth of required machines
        const overallAvailability = Math.min(
            100,
            (totalAvailable / totalRequired) * 100
        );
        const overallUnavailability = 100 - overallAvailability;

        return {
            // Data for the gauge chart
            gaugeChartSeries: [overallAvailability], // Overall availability percentage
            gaugeChartOptions: {
                chart: {
                    type: "radialBar",
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            value: {
                                formatter: function (val) {
                                    return val + "%";
                                },
                            },
                        },
                    },
                },
                title: {
                    text: "Overall Machine Availability",
                    align: "center",
                },
                colors: ["#00E396"],
            },

            // Data for the donut chart
            donutChartSeries: [overallAvailability, overallUnavailability], // Available vs Unavailable percentages
            donutChartOptions: {
                chart: {
                    type: "donut",
                },
                title: {
                    text: "Machine Availability vs Unavailability",
                    align: "center",
                },
                labels: ["Available", "Unavailable"],
                colors: ["#00E396", "#FEB019"],
            },
        };
    },
};
</script>

<style scoped>
.chart {
    margin-bottom: 30px;
}
</style>
