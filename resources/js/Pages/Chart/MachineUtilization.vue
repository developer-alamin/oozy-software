<template>
    <v-container>
        <v-row>
            <!-- Bar Chart for Machine Utilization Rate -->
            <v-col cols="12" md="12">
                <v-card>
                    <v-card-title
                        >Machine Utilization Rate - Last 10 Days</v-card-title
                    >
                    <v-card-text>
                        <apexchart
                            type="bar"
                            height="350"
                            :options="barChartOptions"
                            :series="barChartSeries"
                        />
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Pie Chart for Utilization vs Non-utilization -->
            <v-col cols="12" md="12">
                <v-card>
                    <v-card-title>Overall Machine Utilization</v-card-title>
                    <v-card-text>
                        <apexchart
                            type="pie"
                            height="350"
                            :options="pieChartOptions"
                            :series="pieChartSeries"
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
        // Machine usage data for the last 10 days
        const machineUsage = [80, 70, 65, 75, 90, 85, 60, 55, 95, 88]; // in hours
        const totalMachineCapacity = 100; // total machine capacity in hours per day

        // Calculate the utilization rates
        const utilizationRates = machineUsage.map(
            (usage) => (usage / totalMachineCapacity) * 100
        );

        // Calculate overall utilization and non-utilization percentages
        const totalUsage = machineUsage.reduce((acc, val) => acc + val, 0);
        const totalCapacity = totalMachineCapacity * 10; // Total capacity for 10 days
        const overallUtilization = (totalUsage / totalCapacity) * 100;
        const overallNonUtilization = 100 - overallUtilization;

        return {
            // Data for the bar chart
            barChartSeries: [
                {
                    name: "Utilization Rate",
                    data: utilizationRates, // Utilization rates for 10 days
                },
            ],
            barChartOptions: {
                chart: {
                    type: "bar",
                },
                title: {
                    text: "Machine Utilization Rate - Last 10 Days",
                    align: "center",
                },
                xaxis: {
                    categories: [
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
                        text: "Days",
                    },
                },
                yaxis: {
                    title: {
                        text: "Utilization Rate (%)",
                    },
                    max: 100,
                },
                colors: ["#008FFB"],
            },

            // Data for the pie chart
            pieChartSeries: [overallUtilization, overallNonUtilization], // Utilized vs. Non-utilized
            pieChartOptions: {
                chart: {
                    type: "pie",
                },
                title: {
                    text: "Overall Machine Utilization",
                    align: "center",
                },
                labels: ["Utilized", "Non-utilized"],
                colors: ["#008FFB", "#FEB019"],
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
