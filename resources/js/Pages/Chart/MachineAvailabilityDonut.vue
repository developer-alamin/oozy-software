<template>
    <v-card>
        <v-card-title class="text-h6">Machine Availability</v-card-title>
        <v-card-text>
            <apexchart type="donut" :options="chartOptions" :series="series" />
        </v-card-text>
    </v-card>
</template>

<script>
import { defineComponent } from "vue";
import VueApexCharts from "vue3-apexcharts";

export default defineComponent({
    components: {
        apexchart: VueApexCharts,
    },
    data() {
        return {
            totalAvailableMachine: 120, // Replace with actual data
            totalRequiredMachine: 200, // Replace with actual data
        };
    },
    computed: {
        availabilityPercentage() {
            return Math.min(
                100,
                (this.totalAvailableMachine / this.totalRequiredMachine) * 100
            );
        },
        series() {
            return [
                this.availabilityPercentage,
                100 - this.availabilityPercentage,
            ]; // To show the remaining percentage
        },
        chartOptions() {
            return {
                chart: {
                    type: "donut",
                },
                labels: ["Available", "Unavailable"],
                colors: ["#4caf50", "#f44336"], // Customize colors as needed
                dataLabels: {
                    enabled: true,
                    formatter: (val) => {
                        return `${val.toFixed(2)}%`;
                    },
                },
                legend: {
                    position: "bottom",
                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                    },
                },
            };
        },
    },
});
</script>

<style scoped>
/* Add your styles here */
</style>
