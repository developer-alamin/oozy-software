<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Service Details</v-card-title>
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
		              :error-messages="errors.mechine_assing_id ? errors.mechine_assing_id : ''"
		              @update:search="fetchMachine"
		              readonly
		            >
		              <template v-slot:label>
		                Select Machine
		              </template>
		            </v-autocomplete>
		          </v-col>
		        </v-row>

                <v-row>
		          <v-col cols="12">
		            <v-autocomplete
		              v-model="preventive_service.problem_note_id"
		              :items="BreakdownProblemNotes"
		              item-value="id"
		              item-title="note"
		              label="Select Problem Notes"
		              outlined
		              clearable
		              multiple
		              small-chips
		              density="comfortable"
				      :rules="[rules.required]"
		              :error-messages="errors.problem_note_id ? errors.problem_note_id : ''"
		              @update:search="fetchBreakdownProblemNote"
		            >
		              <template v-slot:label> Select Problem Notes <span style="color: red">*</span> </template>
		            </v-autocomplete>
		          </v-col>
		        </v-row>

                <v-row>
		          <v-col cols="12">
			        	<v-textarea
				          v-model="preventive_service.note"
				          :error-messages="errors.note || ''"
				        >
					          <template v-slot:label>
					           Note
					          </template>
				        </v-textarea>
		          </v-col>
		        </v-row>


                <v-row>
		          <v-col cols="12">
		          	 <v-table>
					  <thead>
					    <tr>
					      <th class="text-left">Part Name</th>
					      <th class="text-left">Qty</th>
					      <th class="text-center">Action</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr v-for="(part, index) in preventive_service.parts_info" :key="index">
					      <td>
					        <v-autocomplete
					          v-model="part.parts_id"
					          :items="parts"
					          item-value="id"
					          item-title="name"
					          label="Select Parts"
					          outlined
					          clearable
					          density="comfortable"
					          :error-messages="errors.parts_id ? errors.parts_id : ''"
					          @update:search="fetchParts"
					        >
					          <template v-slot:label> Select Parts </template>
					        </v-autocomplete>
					      </td>
					      <td>
					        <v-text-field
					          v-model="part.qty"
					          label="Qty"
					          type="text"
					          density="comfortable"
					        />
					      </td>
					      <td class="text-center">
					        <v-icon color="green" @click="addNewPart">mdi-plus</v-icon>
					        <template v-if="index > 0">
					        	<v-icon color="red" @click="removePart(index)">mdi-minus</v-icon>
					        </template>
					      </td>
					    </tr>
					  </tbody>
					</v-table>

		          </v-col>
		        </v-row>


		        <v-row>
		            <v-col cols="12">
			            <v-select
			              v-model="preventive_service.technician_status"
			              :items="statusItems"
			              label="Technician Status"
			              outlined
			              clearable
				      	  :rules="[rules.required]"
			              density="comfortable"
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
                            Save
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
      		statusItems: ["Done", "Failed"],
		    preventive_service: {
		        mechine_assing_id: null,
		        problem_note_id: null,
		        technician_status: null,
		        note: null,
		        parts_info: [
		        	{
			        	parts_id: null,
			      		qty: null
			        }
		    	],
		    },
      		machineItems: [],
      		PreventiveServiceDetailData: [],
      		PreventiveServiceData: [],
      		BreakdownProblemNotes: [],
      		parts: [],
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
    	this.PreventiveServiceDetail()
    	this.fetchParts();
    },
    methods: {

    	 // Add a new part entry
	  	addNewPart() {
		    this.preventive_service.parts_info.push({
		      parts_id: null,
		      qty: null
		    });
	  	},
	  
	  	// Remove a part entry by its index
	  	removePart(index) {
	    	this.preventive_service.parts_info.splice(index, 1);
	  	},

        async PreventiveServiceDetail() {
	      try {
	      	const response = await this.$axios.get(
                `preventive-service/${this.$route.params.detail_id}/preventive-service-start-get-details`
            );

	        this.PreventiveServiceDetailData = response.data.PreventiveServiceDetail;
	        this.PreventiveServiceData = response.data.PreventiveService;
	        this.fetchMachine("", this.PreventiveServiceData.mechine_assing_id)
	        this.preventive_service.mechine_assing_id = this.PreventiveServiceData.mechine_assing_id

	      } catch (error) {
	        console.error("Error fetching machine codes:", error);
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

	    async fetchBreakdownProblemNote(search) {
	      try {
	        const response = await this.$axios.get(`/get_breakdown_problem_notes`, {
	          params: {
	            search: search,
	            limit: this.limit,
	          },
	        });
	        // console.log(response.data);
	        this.BreakdownProblemNotes = response.data;
	        console.log(response.data)
	      } catch (error) {
	        console.error("Error fetching problem notes:", error);
	      }
	    },
        async fetchParts(search = "") {
	      try {
	        const response = await this.$axios.get("/get_parts");
	        this.parts = response.data; // Populate parts
	      } catch (error) {
	        console.error("Error fetching parts:", error);
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
                        `preventive-service/${detailId}/preventive-service-start-save-details`,
                        this.preventive_service
                    );
                    if (response.data.success) {
                        toast.success("Data has been saved!");
                        this.$router.push({ name: "PreventiveServiceIndex" });
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to save.");
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
