<template>
     <v-card outlined class="mx-auto my-5">
        <v-row align="center" style="padding-top: 10px;">
        <v-col cols="12" md="6">
            
            <v-card-title>Preventive Single Service Details</v-card-title>
        </v-col>
        <v-col cols="12" md="6">
            <div style="text-align-last: end; padding-right: 10px;">
            <v-tooltip :text="signle_service?.user?.name">
                <template v-slot:activator="{ props }">
                    <img 
                         v-bind="props"
                        v-if="signle_service?.user?.photo" 
                        :src="signle_service?.user?.photo" 
                        alt="" 
                        style="width: 130px; margin-left: auto;"
                    > 
                </template>
            </v-tooltip>
            
            </div>
        </v-col>
        </v-row>

        <v-divider></v-divider>
        <v-card-text>
           
            <v-row>
                <v-col>
                    <v-row>
                        <v-col cols="6">
                            <v-row>
                                <v-col cols="6">
                                    <b>Company : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service?.preventive_service?.company?.name }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Machine Code : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service?.preventive_service?.mechine_assing?.machine_code }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            
                            <v-row>
                                <v-col cols="6">
                                    <b>Machine Name : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service?.preventive_service?.mechine_assing?.name }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Status : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service.status }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            
                            <v-row>
                                <v-col cols="6">
                                    <b>Teachnician Name : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ signle_service?.user?.name }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Teachnician Email : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ signle_service?.user?.email }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Teachnician Adress : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ signle_service?.user?.address || "N/A" }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                        </v-col>
                        <v-col cols="6">
                            <v-row>
                                <v-col cols="6">
                                    <b>Technician Status : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ signle_service.technician_status }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Acknowledge Date Time : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service.acknowledge_date_time }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Service Start Date Time : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service.service_start_date_time }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Creator : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ signle_service?.creator?.name }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Create Date : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>{{ formatDate(signle_service.created_at) }}</v-card-subtitle> 
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Teachnician Role : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ signle_service?.user?.role }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Teachnician Phone : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ signle_service?.user?.phone || "N/A" }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row>
                                <v-col cols="6">
                                    <b>Teachnician Create Date : </b>
                                </v-col>
                                <v-col cols="6">
                                    <v-card-subtitle>  {{ formatDate(signle_service?.user?.created_at)}}</v-card-subtitle>
                                </v-col>
                            </v-row>
                            <v-divider></v-divider>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-card-title>Service Spares Parts List</v-card-title>
                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th v-if="signle_service?.parsed_parts_info && signle_service?.parsed_parts_info.length > 0" 
                                        v-for="(value, key) in signle_service?.parsed_parts_info[0]" 
                                            :key="key">
                                            {{ key }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(part, index) in signle_service?.parsed_parts_info" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td v-for="(value, key) in part" :key="key">
                                        {{ value }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                        <v-col cols="12">
                            <v-card-title>Service Finding Note List</v-card-title>
                            <v-table >
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>Finding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <tr v-if="signle_service?.problem_notes" v-for="(item,key) in signle_service?.problem_notes" :key="key">
                                    <td>{{ key+1 }}</td>
                                    <td>{{ item.note }}</td>
                                   </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                        <v-col cols="12">
                            <v-card-title>Service Action List</v-card-title>
                            <v-table >
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <tr v-if="signle_service?.actions" v-for="(item,key) in signle_service?.actions" :key="key">
                                    <td>{{ key+1 }}</td>
                                    <td>{{ item.name }}</td>
                                   </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>

                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
import moment from "moment";
    export default {
        data(){
            return {
                valid: false,
                loading: false,
                signle_service:[]

            }
        },
        created(){
            this.fatchSignleService();
        },
        methods : {
            async fatchSignleService(){
                try {
                    const uuid = this.$route.params.uuid;
                    const response = await this.$axios.get(
                    `/preventive-signle-service/${uuid}/details`);
                    console.log(response);
                    this.signle_service = response.data;

                } catch (error) {
                    
                }
            },
            formatDate(date) {
               return moment(date).format("MMMM Do, YYYY");
            },
        }
};
</script>