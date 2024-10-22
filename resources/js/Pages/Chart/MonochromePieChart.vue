<template>
    <div>
        <apexchart type="pie" :options="chartOptions" :series="series" />
    </div>
</template>

<script>
export default {
    data() {
        return {
            chartOptions: {
                title: {
                    text: "Monochrome Fruit Distribution",
                    align: "center",
                },
                colors: ["#1976D2"], // Set a single color for all slices
                legend: {
                    position: "bottom",
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200,
                            },
                            legend: {
                                position: "bottom",
                            },
                        },
                    },
                ],
            },
            series: [44, 55, 13, 33], // Data for the pie chart
        };
    },
};
</script>

<style scoped>
/* Add any styles for your pie chart here */
</style>
