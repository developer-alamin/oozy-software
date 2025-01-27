<template>
    <v-card outlined class="mx-auto my-5">
        <v-card-title>
            <v-row>
                <v-col cols="12" md="6"> Edit Rent </v-col>
                <!-- <v-col cols="12" md="6">
                    <v-img
                      :width="50"
                      aspect-ratio="16/9"
                      cover
                      :src="newImg" 
                      style="margin-left:auto"
                      v-if="newImg"
                      ></v-img>
                </v-col> -->
            </v-row>
        </v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <v-row>
                    <!-- <v-col cols="12" md="6">
                        <v-file-input
                            accept="image/png, image/jpeg, image/bmp"
                            label="Photo"
                            placeholder="Pick an avatar"
                            prepend-icon="mdi-camera"
                            @change="onFilePicked($event)"
                        >
                        </v-file-input>
                    </v-col> -->
                    <v-col cols="12" md="6">
                        <!-- Name Field -->
                        <v-text-field
                            v-model="rent.name"
                            :rules="[rules.required]"
                            label="Name"
                            outlined
                            :error-messages="errors.name ? errors.name : ''"
                        >
                            <template v-slot:label>
                                Name <span style="color: red">*</span>
                            </template>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="rent.email" label="E-mail">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="rent.phone" label="Phone">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="rent.address" label="Address">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <!-- Description Field -->
                        <v-textarea
                            v-model="rent.description"
                            label="Description"
                        />
                    </v-col>
                </v-row>
                <!-- Action Buttons -->

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
                           class="primary-color"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update Rent
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>

        <!-- Server Error Message -->
        <v-alert v-if="serverError" type="error" class="my-4">
            {{ serverError }}
        </v-alert>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            newImg: "",
            valid: false,
            loading: false,
            rent: {
                id: "",
                name: "",
                description: "",
                oldImg: "",
                email: "",
                phone: "",
                address: "",
                imageFile: "",
            },
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchBrand();
    },
    methods: {
        async onFilePicked(e) {
            const files = e.target.files;
            if (files[0] !== undefined) {
                const fr = new FileReader();
                fr.readAsDataURL(files[0]);
                fr.addEventListener("load", () => {
                    this.newImg = fr.result;
                    this.rent.imageFile = files[0];
                });
            }
        },
        async fetchBrand() {
            // Fetch the rent data to populate the form
            const rentId = this.$route.params.uuid; // Assuming the brand ID is passed in the route params
            try {
                const response = await this.$axios.get(`/rent/${rentId}/edit`);
                this.rent = response.data.rent;
                this.rent.id = this.rent.id;
                this.newImg = this.rent.photo;
                this.rent.oldImg = this.rent.photo;

                // Populate form with the existing rent data
            } catch (error) {
                this.serverError = "Error fetching rent data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const formData = new FormData();
            Object.entries(this.rent).forEach(([key, value]) => {
                formData.append(key, value);
                formData.append("_method", "PUT");
            });

            const rentId = this.$route.params.uuid; // Assuming brand ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.post(
                        "/rent/" + rentId,
                        formData
                    );

                    if (response.data.success) {
                        this.$router.push({ name: "RentIndex" }); // Redirect to rent list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating brand.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchBrand(); // Reset the form with existing brand data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
