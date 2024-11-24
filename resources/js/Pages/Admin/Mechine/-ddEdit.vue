<template>
    <v-card outlined class="mx-auto my-5" max-width="">
        <v-card-title>Parse Transfer</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="submit">
                <v-row>
                    <v-col cols="4">
                        <v-text-field
                            v-model="parse.name"
                            :rules="[rules.required]"
                            label="Parse Name"
                            outlined
                            density="comfortable"
                            :error-messages="errors.name ? errors.name : ''"
                        >
                            <template v-slot:label>
                                Parse Name <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>

                    <v-col cols="4">
                        <v-autocomplete
                            v-model="parse.company_id"
                            :items="companys"
                            item-value="id"
                            item-title="name"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.company_id ? errors.company_id : ''
                            "
                            @update:search="fetchCompanys"
                        >
                            <template v-slot:label>
                                Select Company <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="parse.category_id"
                            :items="categories"
                            item-value="id"
                            item-title="name"
                            label="Select Category"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.category_id ? errors.category_id : ''
                            "
                            @update:search="fetchCategories"
                        >
                            <template v-slot:label>
                                Select Category
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="parse.supplier_id"
                            :items="suppliers"
                            item-value="id"
                            item-title="name"
                            label="Select Supplier"
                            density="comfortable"
                            clearable
                            :error-messages="
                                errors.supplier_id ? errors.supplier_id : ''
                            "
                            @update:search="fetchSuppliers"
                        >
                            <!-- <template v-slot:label>
                        Select Supplier
                        <span style="color: red">*</span>
                    </template> -->
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="4">
                        <v-autocomplete
                            v-model="parse.brand_id"
                            :items="brands"
                            item-value="id"
                            item-title="name"
                            label="Select  Brand"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.brand_id ? errors.brand_id : ''
                            "
                            @update:search="fetchBrands"
                        >
                            <template v-slot:label>
                                Select Brand
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <!-- <v-col cols="4">
                        <v-autocomplete
                            v-model="parse.model_id"
                            :items="models"
                            item-value="id"
                            item-title="name"
                            label="Select  Model"
                            density="comfortable"
                            clearable
                            :rules="[rules.required]"
                            :error-messages="
                                errors.model_id ? errors.model_id : ''
                            "
                            @update:search="fetchModels"
                        >
                            <template v-slot:label>
                                Select Model
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col> -->
                    <v-row>
                        <v-col cols="6">
                            <v-autocomplete
                                v-model="parse.model_id"
                                :items="models"
                                item-value="id"
                                item-title="name"
                                label="Select parse Model"
                                outlined
                                clearable
                                density="comfortable"
                                :loading="loadingModels"
                                :rules="[rules.required]"
                                :error-messages="
                                    errors.model_id ? errors.model_id : ''
                                "
                                @update:search="fetchModels"
                                @update:model-value="onModelChange"
                            >
                                <template v-slot:label>
                                    Select parse Model
                                    <span style="color: red">*</span>
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="6">
                            <v-autocomplete
                                v-model="parse.brand_id"
                                :items="brands"
                                item-value="id"
                                item-title="name"
                                label="Select parse Brand"
                                outlined
                                clearable
                                density="comfortable"
                                :loading="loadingBrands"
                                :rules="[rules.required]"
                                :error-messages="
                                    errors.brand_id ? errors.brand_id : ''
                                "
                            >
                                <template v-slot:label>
                                    Select parse Brand
                                    <span style="color: red">*</span>
                                </template>
                            </v-autocomplete>
                        </v-col>
                    </v-row>
                </v-row>

                <v-row>
                    <v-col cols="3">
                        <v-date-input
                            v-model="parse.purchase_date"
                            label="Purchase Date"
                            density="comfortable"
                            :error-messages="
                                errors.purchase_date ? errors.purchase_date : ''
                            "
                        />
                    </v-col>
                    <v-col cols="3">
                        <v-autocomplete
                            v-model="parse.parse_unit_id"
                            :items="units"
                            item-value="id"
                            item-title="name"
                            label="Select Units"
                            outlined
                            clearable
                            density="comfortable"
                            :rules="[rules.required]"
                            :error-messages="
                                errors.parse_unit_id ? errors.parse_unit_id : ''
                            "
                            @update:search="fetchParseUnit"
                        >
                            <template v-slot:label>
                                Select Units
                                <span style="color: red">*</span>
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="3">
                        <v-text-field
                            v-model="parse.quantity"
                            label="Quantity"
                            :rules="[rules.required]"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.quantity ? errors.quantity : ''
                            "
                        >
                            <template v-slot:label>
                                Quantity <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>

                    <v-col cols="3">
                        <v-text-field
                            v-model="parse.purchace_price"
                            label="Purchase Price"
                            outlined
                            density="comfortable"
                            :error-messages="
                                errors.purchace_price
                                    ? errors.purchace_price
                                    : ''
                            "
                        >
                            <template v-slot:label> Purchase Price </template>
                        </v-text-field>
                    </v-col>
                </v-row>

                <v-textarea
                    v-model="parse.note"
                    label="Note"
                    density="comfortable"
                    :error-messages="errors.note ? errors.note : ''"
                />
                <v-select
                    v-model="parse.status"
                    :items="statusItems"
                    label="Mechine Status"
                    clearable
                    density="comfortable"
                ></v-select>
                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <!-- Submit Button -->

                    <!-- Reset Button -->
                    <v-col cols="12" class="text-right">
                        <v-btn
                            type="button"
                            color="secondary"
                            @click="resetForm"
                            class="mr-3"
                        >
                            Reset Form
                        </v-btn>

                        <v-btn
                            type="submit"
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Parse Update
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>

    <!-- Server Error Message -->
    <v-alert v-if="serverError" type="error" class="my-4">
        {{ serverError }}
    </v-alert>
</template>
<script>
import { ref } from "vue";
import { toast } from "vue3-toastify";
// import VDateInput from "../../Components/VDateInput.vue";

export default {
    // components: {
    //     VDateInput,
    // },
    data() {
        return {
            valid: false,
            loading: false, // Controls loading state of the button
            statusItems: ["Active", "Inactive"],
            parse: {
                rent_date: null,
                purchase_date: null,
                purchace_price: 0,
                name: "",
                date: null,
                company_id: null,
                category_id: null,
                brand_id: null,
                model_id: null,
                parse_unit_id: null,
                supplier_id: null,
                quantity: 0,
                note: "",
                status: "Active",
            },
            errors: {}, // Stores validation errors
            serverError: null, // Stores server-side error messages
            limit: 5,
            companys: [], // Array to store Company data
            factories: [], // Array to store factories data
            brands: [], // Array to store brands data
            models: [], // Array to store models data
            types: [], // Array to store types data
            sources: [], // Array to store sources data
            suppliers: [], // Array to store suppliers data
            rents: [], // Array to store rents data
            selectedCompany: null, // Bound to selected Company in v-autocomplete

            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
                confirm_password: (value) =>
                    value === this.company.password || "Passwords must match.", // Confirms password matches
                phone: (value) =>
                    /^\d{11}$/.test(value) || "Phone number must be valid.",
            },
            visible: false,
            confirm_visible: false,
        };
    },
    computed: {
        // Function to limit the allowed dates within the min and max date range
        allowedDates() {
            return (date) => {
                return date >= new Date();
            };
        },
    },
    created() {
        this.fetchCompanys().then(() => {
            this.fetchParse();
        });
        this.fetchCategories().then(() => {
            this.fetchParse();
        });
        this.fetchBrands().then(() => {
            this.fetchParse();
        });
        this.fetchModels().then(() => {
            this.fetchParse();
        });
        this.fetchSuppliers().then(() => {
            this.fetchParse();
        });
        this.fetchParseUnit().then(() => {
            this.fetchParse();
        });
    },
    methods: {
        async fetchParse() {
            const technicianId = this.$route.params.uuid;
            try {
                const response = await this.$axios.get(
                    `/mechine/assing/${technicianId}/edit`
                );
                this.parse = response.data.mechineAssing;
                this.parse.status = this.statusItems.includes(this.parse.status)
                    ? this.parse.status
                    : "";

                // Set the selected company based on the company_id
                const selectedCompany = this.companys.find(
                    (c) => c.id === this.parse.company_id
                );
                if (selectedCompany) {
                    this.parse.company_id = selectedCompany.id; // Set the company_id for v-autocomplete
                }
                // factories
                const selectedCategory = this.factories.find(
                    (c) => c.id === this.parse.category_id
                );
                if (selectedCategory) {
                    this.parse.category_id = selectedCategory.id; // Set the company_id for v-autocomplete
                }
                // Brands
                const selectedBrand = this.factories.find(
                    (c) => c.id === this.parse.brand_id
                );
                if (selectedBrand) {
                    this.parse.brand_id = selectedBrand.id; // Set the company_id for v-autocomplete
                }

                // Models
                const selectedModel = this.factories.find(
                    (c) => c.id === this.parse.model_id
                );
                if (selectedModel) {
                    this.parse.model_id = selectedModel.id; // Set the company_id for v-autocomplete
                }

                // Type
                const selectedType = this.factories.find(
                    (c) => c.id === this.parse.mechine_type_id
                );
                if (selectedType) {
                    this.parse.mechine_type_id = selectedType.id; // Set the company_id for v-autocomplete
                }

                // Source
                const selectedSource = this.factories.find(
                    (c) => c.id === this.parse.mechine_source_id
                );
                if (selectedSource) {
                    this.parse.mechine_source_id = selectedSource.id; // Set the company_id for v-autocomplete
                }
                // Supplier
                const selectedSupplier = this.factories.find(
                    (c) => c.id === this.parse.supplier_id
                );
                if (selectedSupplier) {
                    this.parse.supplier_id = selectedSupplier.id; // Set the company_id for v-autocomplete
                }
                // Rent
                const selectedParseUnit = this.factories.find(
                    (c) => c.id === this.parse.parse_unit_id
                );
                if (selectedParseUnit) {
                    this.parse.parse_unit_id = selectedParseUnit.id; // Set the company_id for v-autocomplete
                }
            } catch (error) {
                this.serverError = "Error fetching technician data.";
            }
        },

        async submit() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const parseId = this.$route.params.uuid; // Assuming type ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `parse/${parseId}`,
                        this.parse
                    );
                    // console.log(response.data);
                    if (response.data.success) {
                        toast.success("parse update successfully!");
                        this.$router.push({ name: "ParseIndex" }); // Redirect to type list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        toast.error("Failed to update parse .");
                        this.errors = error.response.data.errors || {};
                    } else {
                        toast.error("Error updating parse . Please try again.");
                        this.serverError = "Error parse .";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        async fetchBrands(search) {
            try {
                if (!this.machine.model_id) {
                    this.brands = [];
                    return;
                }

                this.loadingBrands = true;
                const response = await this.$axios.get("/get_brands", {
                    params: {
                        search,
                        model_id: this.machine.model_id,
                        limit: 5,
                    },
                });
                this.brands = response.data;
            } catch (error) {
                console.error("Error fetching brands:", error);
            } finally {
                this.loadingBrands = false;
            }
        },
        onModelChange() {
            this.machine.brand_id = null; // Reset brand selection when model changes
            this.fetchBrands("");
        },

        resetForm() {
            this.parse = {
                company_id: "",
                name: "",
                email: "",
                phone: "",
                factory_code: "",
                location: "",
                status: "Preventive", // New property for checkbox
            };
            this.errors = {}; // Reset errors on form reset
            if (this.$refs.form) {
                this.$refs.form.reset(); // Reset the form via its ref if necessary
            }
        },

        async fetchCompanys(search) {
            try {
                const response = await this.$axios.get(`/get_companys`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.companys = response.data;
            } catch (error) {
                console.error("Error fetching companys:", error);
            }
        },
        async fetchCategories(search) {
            try {
                const response = await this.$axios.get(`/parse/get_category`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },

        async fetchBrands(search) {
            try {
                const response = await this.$axios.get(`/get_brands`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.brands = response.data;
            } catch (error) {
                console.error("Error fetching brands:", error);
            }
        },
        async fetchModels(search) {
            try {
                const response = await this.$axios.get(`/get_models`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.models = response.data;
            } catch (error) {
                console.error("Error fetching models:", error);
            }
        },

        async fetchSuppliers(search) {
            try {
                const response = await this.$axios.get(`/parse/get_suppliers`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.suppliers = response.data;
            } catch (error) {
                console.error("Error fetching suppliers:", error);
            }
        },
        async fetchParseUnit(search) {
            try {
                const response = await this.$axios.get(`/parse/units`, {
                    params: {
                        search: search,
                        limit: this.limit,
                    },
                });
                // console.log(response.data);
                this.units = response.data;
            } catch (error) {
                console.error("Error fetching unit:", error);
            }
        },
    },
};
</script>
