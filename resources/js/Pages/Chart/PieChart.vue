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
                labels: ["Apple", "Mango", "Orange", "Banana"],
                title: {
                    text: "Fruit Distribution",
                    align: "center",
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
            series: [44, 55, 13, 33],
        };
    },
};
</script>

<style scoped>
/* Add any styles for your pie chart here */
</style>
