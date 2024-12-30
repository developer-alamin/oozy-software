<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Assign to Technician</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
               
                <v-row>
		          <v-col cols="12">
		            <v-autocomplete
		              v-model="preventive_service.mechine_assing_id"
		              :items="machineItems"
		              item-value="id"
		              item-title="machine_code"
		              label="Select Machine"
		              outlined
		              clearable
		              density="comfortable"
		              :rules="[rules.required]"
		              :error-messages="errors.mechine_assing_id ? errors.mechine_assing_id : ''"
		              @update:search="fetchMachine"
                      readonly
		            >
		              <template v-slot:label>
		                Select Machine <span style="color: red">*</span>
		              </template>
		            </v-autocomplete>
		          </v-col>
		        </v-row>

                <v-row>
                  <v-col cols="12">
                    <v-autocomplete
                      v-model="preventive_service.technician_id"
                      :items="technicianLists"
                      item-value="id"
                      item-title="name"
                      label="Select Technician"
                      outlined
                      clearable
                      density="comfortable"
                      :rules="[rules.required]"
                      :error-messages="errors.technician_id ? errors.technician_id : ''"
                      @update:search="fetchTechnician"
                    >
                      <template v-slot:label>
                        Select Technician <span style="color: red">*</span>
                      </template>
                    </v-autocomplete>
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
                            Submit
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
		        technician_id: null,
		    },
      		machineItems: [],
            technicianLists: [],
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchPreventiveService();
        this.fetchTechnician();
    },
    methods: {

    	async fetchPreventiveService() {
            const preventiveServiceId = this.$route.params.uuid;
            try {
                const response = await this.$axios.get(
                    `/preventive-service/${preventiveServiceId}/get-assign-to-technician`
                );
                let service = response.data.PreventiveService
                this.preventive_service.mechine_assing_id = service.mechine_assing_id
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

        async fetchTechnician(search = "", id = 0) {
          try {
            const response = await this.$axios.get("/search_user/"+id, {
              params: {
                search,
              },
            });
            this.technicianLists = response.data;
            console.log(response.data)
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
                        `preventive-service/${serviceId}/save-assign-to-technician`,
                        this.preventive_service
                    );
                    if (response.data.success) {
                        toast.success("Technician Assign saved!");
                        this.$router.push({ name: "PreventiveServiceIndex" });
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update Technician Assign.");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating Technician Assign. Please try again.");
                        this.serverError = "Error updating Technician Assign.";
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
