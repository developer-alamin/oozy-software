<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Service Start</v-card-title>
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
		            >
		              <template v-slot:label>
		                Select Machine <span style="color: red">*</span>
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
                            Service Start
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
		    preventive_service: {
		        mechine_assing_id: null,
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
    },
    methods: {

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
            const detailId = this.$route.params.detail_id;
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `preventive-service/${detailId}/preventive-service-start`,
                        this.preventive_service
                    );
                    if (response.data.success) {
                        toast.success("Technician Assign saved!");
                        this.$router.push({ name: "PreventiveServiceIndex" });
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to service start.");
                        this.errors = error.response.data.errors || {};
                    }else if (error.response && error.response.status === 400) {
                        toast.error("You have select wrong mechine");
                        this.serverError = "You have select wrong mechine";
                    }else {
                        toast.error(error);
                        this.serverError = error;
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

    },
};
</script>
