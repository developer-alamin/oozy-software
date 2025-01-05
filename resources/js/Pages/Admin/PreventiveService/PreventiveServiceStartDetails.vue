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
		        
		          <v-col cols="12" md="6" >
					<div class="d-flex align-items-center">
						<v-btn
							@click="problemBtn"
							color="primary"
							icon
							style="width: 20px; height: 20px;margin-top: 10px;" 
						>
							<v-tooltip location="top" activator="parent">
							<template v-slot:activator="{ props }">
								<v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
							</template>
							<span>Add New Finding</span>
							</v-tooltip>
						</v-btn>
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
				</div>
		          </v-col>
				  <v-col cols="12" md="6">
					<div class="d-flex align-items-center">
						<v-btn
							@click="actionBtn"
							color="primary"
							icon
							style="width: 20px; height: 20px;" 
						>
							<v-tooltip location="top" activator="parent">
							<template v-slot:activator="{ props }">
								<v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
							</template>
							<span>Add New Action</span>
							</v-tooltip>
						</v-btn>
		            <v-autocomplete
		              v-model="preventive_service.action_id"
		              :items="actions"
		              item-value="id"
		              item-title="name"
		              label="Select Action"
		              outlined
		              clearable
		              multiple
		              small-chips
		              density="comfortable"
				      :rules="[rules.required]"
		              :error-messages="errors.action_id ? errors.action_id : ''"
		              @update:search="fetchActions"
		            >
		              <template v-slot:label> Select Action <span style="color: red">*</span> </template>
		            </v-autocomplete>
					</div>
		          </v-col>
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
					          label="Select Spares Parts"
					          outlined
					          clearable
					          density="comfortable"
					          :error-messages="errors.parts_id ? errors.parts_id : ''"
					          @update:search="fetchParts"
					        >
					          <template v-slot:label> Select Spares Parts </template>
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
		            <v-col cols="12" :md="(preventive_service.technician_status == 'Hold') ?'6':''">
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
					<v-col v-if="preventive_service.technician_status == 'Hold'" cols="12" md="6">
						<v-autocomplete
							v-model="preventive_service.technician_id"
							:items="technicians"
							item-value="id"
							item-title="name"
							label="Select Technician"
							outlined
							clearable
							multiple
							small-chips
							density="comfortable"
							:rules="[(preventive_service.technician_status == 'Hold') ? rules.required : []]"
							:error-messages="errors.technician_id ? errors.technician_id : ''"
							@update:search="technicians"
						>
							<template v-slot:label> Select Technician <span style="color: red">*</span> </template>
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
                            Save
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>


		<!-- Problem Dialog -->
		<v-dialog v-model="problem_dialog" max-width="700px">
			<v-card>
				<v-card-title>
				<span class="text-h5">Add Finding </span>
				</v-card-title>
				<v-form ref="problemForm" v-model="ProblemValid" @submit.prevent="addProblem">
					<v-row class="px-5">
						<v-col cols="12">
							<v-text-field
								v-model="problem.note"
								:rules="[rules.required]"
								label="Note"
								outlined
								density="comfortable"
								:error-messages="errors.note ? errors.note : ''"
							>
								<template v-slot:label>
									Note <span style="color: red">*</span>
								</template>
							</v-text-field>
						</v-col>
						<v-col cols="12" md="6">
							<v-autocomplete
							v-model="problem.company_id"
							:items="companies"
							item-value="id"
							item-title="name"
							outlined
							clearable
							density="comfortable"
							:rules="[rules.required]"
							:error-messages="errors.company_id || ''"
							@update:search="fetchCompanies"
							no-filter
							>
							<template v-slot:label>
								Select Company <span style="color: red">*</span>
							</template>
							</v-autocomplete>
						</v-col>
						<v-col cols="12" md="6">
							<v-select
								v-model="action.status"
								:items="actionStatusItems"
								label="Status"
								outlined
								clearable
								density="comfortable"
							></v-select>
						</v-col>
					</v-row>
					<v-row class="pb-3 px-5">
						<v-card-actions class="ms-auto">
							<v-btn color="primary" text @click="problem_dialog = false">Close</v-btn>
							<v-btn
								type="submit"
								color="white"
								class="bg-primary"
								:disabled="!ProblemValid || problemLoading"
								:loading="problemLoading"
							>Add</v-btn>
						</v-card-actions>
					</v-row>
				</v-form>
				
			</v-card>
		</v-dialog>
		<!-- Action Dialog -->
		<v-dialog v-model="action_dialog" max-width="700px">
			<v-card>
				<v-card-title>
				<span class="text-h5">Add Action </span>
				</v-card-title>
				<v-form ref="actionForm" v-model="ActionValid" @submit.prevent="addAction">
					<v-row class="px-5">
						<v-col cols="12">
							<v-text-field
								v-model="action.name"
								:rules="[rules.required]"
								label="Name"
								outlined
								density="comfortable"
								:error-messages="errors.name ? errors.name : ''"
							>
								<template v-slot:label>
									Name <span style="color: red">*</span>
								</template>
							</v-text-field>
						</v-col>
						<v-col cols="12" md="6">
							<v-autocomplete
							v-model="action.company_id"
							:items="companies"
							item-value="id"
							item-title="name"
							outlined
							clearable
							density="comfortable"
							:rules="[rules.required]"
							:error-messages="errors.company_id || ''"
							@update:search="fetchCompanies"
							no-filter
							>
							<template v-slot:label>
								Select Company <span style="color: red">*</span>
							</template>
							</v-autocomplete>
						</v-col>
						<v-col cols="12" md="6">
							<v-select
								v-model="action.status"
								:items="actionStatusItems"
								label="Status"
								outlined
								clearable
								density="comfortable"
							></v-select>
						</v-col>
					</v-row>
					<v-row class="pb-3 px-5">
						<v-card-actions class="ms-auto">
							<v-btn color="primary" text @click="action_dialog = false">Close</v-btn>
							<v-btn
								type="submit"
								color="white"
								class="bg-primary"
								:disabled="!ActionValid || actionLoading"
								:loading="actionLoading"
							>Add</v-btn>
						</v-card-actions>
					</v-row>
				</v-form>
				
			</v-card>
		</v-dialog>

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
			problem_dialog:false,
			action_dialog:false,
            valid: false,
			//search:'',
			ProblemValid:false,
			ActionValid:false,
            loading: false,
			problemLoading:false,
			actionLoading:false,
			actionStatusItems: ["Active", "Inactive"],
      		statusItems: ["Done", "Failed","Hold"],
		    preventive_service: {
				technician_id:null,
				action_id:null,
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
			action:{
				company_id:'',
				name:'',
				status:"Active",
			},
			problem: {
				company_id:null,
				note: "",
				status: "Active", // New property for checkbox
			},
      		machineItems: [],
      		PreventiveServiceDetailData: [],
      		PreventiveServiceData: [],
      		BreakdownProblemNotes: [],
      		parts: [],
			actions:[],
			limit: 5,
            companies:[],
			technicians: [],
            selectedCompany: null,
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
		this.fetchTechnician();
    },
    methods: {
		async fetchTechnician(search = "", id = 0) {
			try {
			const response = await this.$axios.get("/search_user/"+id, {
				params: {
				search,
				},
			});
			this.technicians = response.data;
			} catch (error) {
			console.error("Error fetching machine codes:", error);
			}
		},
		actionBtn(){
			this.action_dialog = true;
		},
		problemBtn(){
			this.problem_dialog = true;
		},
		formatCompany(company) {
        if (company) {
            console.log(company)
            if (typeof company === "number") {
                // Use strict equality (===) and ensure proper assignment in the find function
                company = this.companies.find((item) => item.id === company);
            }
            // Safely return the company name or a fallback if the name is missing
            return company && company.name ? company.name : "No Company Name";
            }
            // Fallback if no company data is provided
            return "No Company Data";
        },
        async fetchCompanies(search) {
            try {
                // Make a GET request to the '/get_companies' endpoint with query parameters
                const response = await this.$axios.get('/get_companies', {
                    params: {
                    search: this.search || '', // Use `this.search` or fallback to an empty string
                    limit: this.limit || 5,   // Use `this.limit` or fallback to default value (5)
                    },
                });
                // Update the companies array with the fetched data
                this.companies = response.data;
                } catch (error) {
                // Log any errors that occur during the request
                console.error('Error fetching companies:', error);
                // Optionally, handle the error (e.g., show an error message to the user)
                this.$toast.error('Failed to fetch companies. Please try again later.');
                }

        },
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
		async fetchActions(search = "") {
			try {
				const response = await this.$axios.get('/get_actions', {
				params: {
					search: search || "", // Use an empty string if no search term is provided
					limit: this.limit,    // Ensure `this.limit` is defined in your component
				},
				});
				// Update the actions data with the response
				this.actions = response.data;
			} catch (error) {
				console.error("Error fetching actions:", error); // Clarify the error message
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
		async addAction() {
			this.errors = {};
            this.serverError = null;
            this.actionLoading = true;

			const actionFormData = new FormData();

			Object.entries(this.action).forEach(([key, value]) => {
				actionFormData.append(key, value);
			});

		try {
			const response = await this.$axios.post("/action", actionFormData);
			if (response.data.success) {
				toast.success("Action created successfully!");
				this.actionResetForm();
				this.action_dialog = false;
			}
		} catch (error) {
			if (error.response && error.response.status === 422) {
			this.errors = error.response.data.errors || {};
			toast.error("Failed to create action.");
			} else {
			toast.error("An error occurred. Please try again.");
			this.serverError = "An error occurred. Please try again.";
			}
		} finally {
			this.actionLoading = false;
		}
        },
		async addProblem() {
			this.errors = {};
            this.serverError = null;
            this.problemLoading= true;

			const problemFormData = new FormData();

			Object.entries(this.problem).forEach(([key, value]) => {
				problemFormData.append(key, value);
			});

			try {
				const response = await this.$axios.post(
					"/breakdown-problem-notes",
					problemFormData
				);	
				if (response.data.success) {
					toast.success("Action created successfully!");
					this.problemResetForm();
					this.problem_dialog = false;
				}
			} catch (error) {
				if (error.response && error.response.status === 422) {
				this.errors = error.response.data.errors || {};
				toast.error("Failed to create action.");
				} else {
				toast.error("An error occurred. Please try again.");
				this.serverError = "An error occurred. Please try again.";
				}
			} finally {
				this.actionLoading = false;
			}
        },
        resetForm() {
            this.fetchMachine(); 
            this.errors = {};
            this.$refs.form.resetValidation();
        },
		actionResetForm(){
			if (this.$refs.actionForm) {
				this.$refs.actionForm.resetValidation();
				this.action = {
					name: "",
					company_id: null,
					status: "Active",
				};
				this.errors = {};
			}
		},
		problemResetForm(){
			if (this.$refs.problemForm) {
				this.$refs.problemForm.resetValidation();
				this.problem = {
					note: "",
					company_id: null,
					status: "Active",
				};
				this.errors = {};
			}
		},

    },
};
</script>
