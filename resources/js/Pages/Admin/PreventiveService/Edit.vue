<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Preventive Service</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
               
                <v-row>
		          <v-col cols="12">
		            <v-autocomplete
		              v-model="preventive_service.mechine_assing_id"
		              :items="machineItems"
		              item-value="id"
		              item-title="machine_code"
		              label="Select Machine Code"
		              outlined
		              clearable
		              density="comfortable"
		              :rules="[rules.required]"
		              :error-messages="errors.mechine_assing_id ? errors.mechine_assing_id : ''"
		              @update:search="fetchMachine"
		            >
		              <template v-slot:label>
		                Select Machine Code <span style="color: red">*</span>
		              </template>
		            </v-autocomplete>
		          </v-col>
		        </v-row>

		         <v-row>
		          <v-col cols="6">
		            <v-date-input
		              v-model="preventive_service.service_date"
		              label="Service Date"
		              density="comfortable"
		              :error-messages="errors.service_date ? errors.service_date : ''"
		              readonly
		            />
		          </v-col>
		          <v-col cols="6">
		            <v-text-field
		              v-model="preventive_service.service_time"
		              label="Service Time"
		              type="time"
		              density="comfortable"
		              :error-messages="errors.service_time ? errors.service_time : ''"
		              readonly
		            />
		          </v-col>
		        </v-row>

		        <v-row>
		            <v-col cols="12">
			            <v-select
			              v-model="preventive_service.service_status"
			              :items="statusItems"
			              label="Service Status"
			              outlined
			              clearable
			              density="comfortable"
			              :disabled="!preventive_service.mechine_assing_id"
			            ></v-select>
		          </v-col>
		        </v-row>

                <v-row class="mt-4">
                    <v-col cols="12" class="text-right">
                        <v-btn
                            type="button"
                            color="secondary"
                            class="mr-3"
                            @click="resetForm"
                        >
                            Reset Form
                        </v-btn>
                        <v-btn
                            type="submit"
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update Service
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <!-- Server Error Message -->
        <v-alert v-if="serverError" source="error" class="my-4">
            {{ serverError }}
        </v-alert>
    </v-card>
</template>

<script>
import { toast } from "vue3-toastify";
export default {
    data() {
        return {
            valid: false,
            loading: false,
            statusItems: ["Pending", "Processing", "Done", "Cancel"],
		    preventive_service: {
		        mechine_assing_id: null,
		        service_date: null,
		        service_time: null,
		        service_status: "Pending",
		    },
      		machineItems: [],
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchPreventiveService();
        //this.fetchMachine();
    },
    methods: {

    	async fetchPreventiveService() {
            const preventiveServiceId = this.$route.params.uuid;
            try {
                const response = await this.$axios.get(
                    `/preventive-service/${preventiveServiceId}/edit`
                );
                let service = response.data.PreventiveService
                let [service_date, service_time] = service.date_time.split(' ');

                this.preventive_service.mechine_assing_id = service.mechine_assing_id
                this.preventive_service.service_date = service_date
                this.preventive_service.service_time = service_time
                this.preventive_service.service_status = service.service_status
                this.fetchMachine("", service.mechine_assing_id);
            } catch (error) {
                this.serverError = "Error fetching source data.";
            }
        },


        async fetchMachine(search = "", id = 0) {
	      try {
	        const response = await this.$axios.get("/search_machine/"+id, {
	          params: {
	            search,
	          },
	        });
	        this.machineItems = response.data;
	      } catch (error) {
	        console.error("Error fetching machine codes:", error);
	      }
	    },

        async update() {
            this.errors = {};
            this.serverError = null;
            this.loading = true;
            const serviceId = this.$route.params.uuid;
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `preventive-service/${serviceId}`,
                        this.preventive_service
                    );
                    if (response.data.success) {
                        toast.success("Preventive Service update successfully!");
                        this.$router.push({ name: "PreventiveServiceIndex" });
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update Preventive Service.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating Preventive Service. Please try again.");
                        this.serverError = "Error updating Preventive Service.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchMachine(); 
            this.errors = {};
            this.$refs.form.resetValidation();
        },

        getCurrentDate() {
	      const currentDate = new Date();
	      return currentDate.toISOString().split("T")[0]; // Format YYYY-MM-DD
	    },
	    getCurrentTime() {
	      const currentTime = new Date();
	      return currentTime.toTimeString().split(" ")[0].slice(0, 5); // Format HH:MM
	    },
    },
};
</script>
