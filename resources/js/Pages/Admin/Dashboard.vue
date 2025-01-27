<script setup lang="ts">
import BarChart from "@/Pages/Chart/BarChart.vue";
import PieChart from "@/Pages/Chart/PieChart.vue";
import MonochromePieChart from "@/Pages/Chart/MonochromePieChart.vue";
import MachineUtilization from "@/Pages/Chart/MachineUtilization.vue";
import MachineAvailability from "@/Pages/Chart/MachineAvailability.vue";
import MachineUptime from "../Chart/MachineUptime.vue";
import { useAxios } from "@/composables/useAxios";

</script>
<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #CFEDFF;">
                    <div class="card-body">
                        <h5 class="card-title">Line Numbers </h5>
                        <strong>721K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #F3E8FF;">
                    <div class="card-body">
                        <h5 class="card-title">Machine Numbers</h5>
                        <strong>500K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #DCFCE7;">
                    <div class="card-body">
                        <h5 class="card-title">MTTR</h5>
                        <strong>300K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #F3E8FF;">
                    <div class="card-body">
                        <h5 class="card-title">MTBF</h5>
                        <strong>400K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #FFF4DE;">
                    <div class="card-body">
                        <h5 class="card-title">FTFR</h5>
                        <strong>600K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #D4EDFF;">
                    <div class="card-body">
                        <h5 class="card-title">Assigned jobs</h5>
                        <strong>100K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #CFFFD2;">
                    <div class="card-body">
                        <h5 class="card-title">Job completion rate</h5>
                        <strong>100K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #EBEBFF;">
                    <div class="card-body">
                        <h5 class="card-title">Utilization rate</h5>
                        <strong>100K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #E0F2FF;">
                    <div class="card-body">
                        <h5 class="card-title">Active jobs</h5>
                        <strong>100K</strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="card" style="background-color: #FFE2E5;">
                    <div class="card-body">
                        <h5 class="card-title">Overdue tasks</h5>
                        <strong>100K</strong>
                    </div>
                </div>
            </div>
        </div>
        <BarChart />
        <!-- <BarChart /> -->
        <!-- <MonochromePieChart /> -->
         <v-row>
            <v-col cols="12" md="9">
                <MachineUtilization />
            </v-col>
            <v-col cols="12" md="3">
                <div class="card">
                    <div class="card-body">
                       
                    </div>
                </div>
            </v-col>
         </v-row>
        
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
<style scoped>
    .row{
        --bs-gutter-x:10px;
    }
    .card{
        border-radius: 16px;
        box-shadow: none;
    }
    
    .card .card-body{
        padding: 16px;
    }
    .card .card-body .card-title{
        font-family: "Inter", serif;
        color: #1C1C1C;
        font-size: 14px;
        font-weight: 600;
        padding: 5px 0px 5px;
    }
    .card .card-body strong{
        font-size: 24px;
        line-height: 36px;
        font-family: "Inter", serif;
        font-weight: 700;
        color: #1C1C1C;
    }
    
    

</style>