<template>
    <v-container>
        <v-card>
            <v-card-title>
                <v-row>
                    <v-col cols="12" md="6"> Edit Supplier </v-col>
                    <v-col cols="12" md="6">
                        <v-img
                            :width="50"
                            aspect-ratio="16/9"
                            cover
                            :src="newImg"
                            style="margin-left: auto"
                            v-if="newImg"
                        ></v-img>
                    </v-col>
                </v-row>
            </v-card-title>

            <v-card-text>
                <v-form
                    ref="form"
                    v-model="valid"
                    @submit.prevent="updateSupplier"
                >
                    <v-file-input
                        accept="image/png, image/jpeg, image/bmp"
                        label="Photo"
                        placeholder="Pick an avatar"
                        prepend-icon="mdi-camera"
                        @change="onFilePicked($event)"
                    >
                    </v-file-input>
                    <v-text-field
                        label="Name"
                        v-model="supplier.name"
                        :rules="[rules.required]"
                        :error-messages="errors.name ? errors.name : ''"
                        required
                    ></v-text-field>

                    <v-text-field
                        label="Email"
                        v-model="supplier.email"
                        :rules="[rules.required, rules.email]"
                        :error-messages="errors.email ? errors.email : ''"
                        required
                    ></v-text-field>

                    <v-text-field
                        label="Phone"
                        v-model="supplier.phone"
                        :rules="[rules.required]"
                        :error-messages="errors.phone ? errors.phone : ''"
                        required
                    ></v-text-field>

                    <v-text-field
                        label="Contact Person"
                        v-model="supplier.contact_person"
                    ></v-text-field>

                    <v-textarea
                        label="Address"
                        v-model="supplier.address"
                    ></v-textarea>

                    <v-textarea
                        label="Description"
                        v-model="supplier.description"
                    ></v-textarea>

                    <v-alert v-if="serverError" type="error" class="mt-4">
                        {{ serverError }}
                    </v-alert>
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
                                Update Supplier
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <!-- <v-card-actions>
                <v-btn
                    color="primary"
                    @click="updateSupplier"
                    :disabled="!valid"
                    >Update Supplier</v-btn
                >
                <v-btn text @click="$router.push({ name: 'SupplierIndex' })"
                    >Cancel</v-btn
                >
            </v-card-actions> -->
        </v-card>
    </v-container>
</template>

<script>
export default {
    props: ["uuid"], // Capture the :id from the route
    data() {
        return {
            newImg: "",
            supplier: {
                name: "",
                email: "",
                phone: "",
                oldImg: "",
                contact_person: "",
                address: "",
                description: "",
                imageFile: "",
            },
            valid: false,
            loading: false,
            errors: {}, // Initialize errors as an empty object
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
            },
        };
    },

    methods: {
        async onFilePicked(e) {
            const files = e.target.files;
            if (files[0] !== undefined) {
                const fr = new FileReader();
                fr.readAsDataURL(files[0]);
                fr.addEventListener("load", () => {
                    this.newImg = fr.result;
                    this.supplier.imageFile = files[0];
                });
            }
        },
        async loadSupplier() {
            try {
                const response = await this.$axios.get(
                    `/suppliers/${this.uuid}/edit`
                );
                this.supplier = response.data.supplier;
                console.log(response.data.supplier);

                this.newImg = this.supplier.photo ? this.supplier.photo : "";
                this.supplier.oldImg = this.supplier.photo;
            } catch (error) {
                console.error("Failed to load supplier:", error);
            }
        },
        async updateSupplier() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null; // Reset server error
            this.loading = true;
            const formData = new FormData();
            Object.entries(this.supplier).forEach(([key, value]) => {
                formData.append(key, value);
                formData.append("_method", "PUT");
            });

            // Perform client-side validation
            if (this.$refs.form.validate()) {
                setTimeout(async () => {
                    try {
                        const response = await this.$axios.post(
                            `/suppliers/${this.uuid}`,
                            formData
                        );
                        console.log(response.data);
                        this.$router.push({ name: "SupplierIndex" }); // Redirect after update
                    } catch (error) {
                        if (error.response && error.response.status === 422) {
                            // Handle validation errors from the backend
                            this.errors = error.response.data.errors || {};
                        } else {
                            // Handle other types of errors
                            this.serverError =
                                "An error occurred. Please try again.";
                        }
                    } finally {
                        // Stop loading after the request (or simulated time) is done
                        this.loading = false;
                    }
                }, 1000);
            }
        },
    },
    created() {
        this.loadSupplier();
    },
};
</script>
