<template>
    <v-container>
        <v-card>
            <v-card-title>Edit Supplier</v-card-title>

            <v-card-text>
                <v-form
                    ref="form"
                    v-model="valid"
                    @submit.prevent="updateSupplier"
                >
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
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-btn
                    color="primary"
                    @click="updateSupplier"
                    :disabled="!valid"
                    >Update Supplier</v-btn
                >
                <v-btn text @click="$router.push({ name: 'SupplierIndex' })"
                    >Cancel</v-btn
                >
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
export default {
    props: ["id"], // Capture the :id from the route
    data() {
        return {
            supplier: {
                name: "",
                email: "",
                phone: "",
                contact_person: "",
                address: "",
                description: "",
            },
            valid: false,
            errors: {}, // Initialize errors as an empty object
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
                email: (value) =>
                    /.+@.+\..+/.test(value) || "E-mail must be valid.",
            },
        };
    },
    created() {
        this.loadSupplier();
    },
    methods: {
        async loadSupplier() {
            try {
                const response = await this.$axios.get(`/suppliers/${this.id}`);
                this.supplier = response.data.supplier;
            } catch (error) {
                console.error("Failed to load supplier:", error);
            }
        },
        async updateSupplier() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null; // Reset server error

            // Perform client-side validation
            if (this.$refs.form.validate()) {
                try {
                    await this.$axios.put(
                        `/suppliers/${this.id}`,
                        this.supplier
                    );
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
                }
            }
        },
    },
};
</script>
