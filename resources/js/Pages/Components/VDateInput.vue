<template>
    <v-container>
        <v-row>
            <v-col cols="12" md="6">
                <v-date-input
                    v-model="selectedDate"
                    label="Pick a Date"
                    :allowed-dates="allowedDates"
                />
            </v-col>
        </v-row>

        <v-row>
            <v-col>
                <p>Selected Date: {{ selectedDate ? selectedDate : "None" }}</p>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            // Model for v-date-input
            selectedDate: null,

            // Example: Minimum and Maximum allowed dates
            minDate: new Date(2024, 0, 1), // January 1st, 2024
            maxDate: new Date(2024, 11, 31), // December 31st, 2024
        };
    },
    computed: {
        // Function to limit the allowed dates within the min and max date range
        allowedDates() {
            return (date) => {
                const selectedDate = new Date(date);
                return (
                    selectedDate >= this.minDate && selectedDate <= this.maxDate
                );
            };
        },
    },
};
</script>

<style scoped>
/* Optional scoped styles */
</style>
