<template>
    <v-card outlined class="mx-auto my-5" max-width="900">
        <v-card-title>Edit Group</v-card-title>
        <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="update">
                <!-- Name Field -->
                <v-text-field
                    v-model="group.name"
                    :rules="[rules.required]"
                    label="Name"
                    outlined
                    :error-messages="errors.name ? errors.name : ''"
                >
                    <template v-slot:label>
                        Number <span style="color: red">*</span>
                    </template>
                </v-text-field>

                <!-- Description Field -->
                <v-textarea
                    v-model="group.description"
                    label="Description"
                    outlined
                    :error-messages="
                        errors.description ? errors.description : ''
                    "
                />
                <v-select
                    v-model="group.status"
                    :items="statusItems"
                    label="Group Status"
                    clearable
                    :error-messages="errors.status ? errors.status : ''"
                ></v-select>
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
                            color="primary"
                            :disabled="!valid || loading"
                            :loading="loading"
                        >
                            Update Group
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
            valid: false,
            loading: false,
            statusItems: ["Active", "Inactive"],
            group: {
                name: "",
                description: "",
                status: "Active",
            },
            errors: {},
            serverError: null,
            rules: {
                required: (value) => !!value || "Required.",
            },
        };
    },
    created() {
        this.fetchGroup();
    },
    methods: {
        async fetchGroup() {
            // Fetch the group data to populate the form
            const brandId = this.$route.params.uuid; // Assuming the group ID is passed in the route params
            try {
                const response = await this.$axios.get(
                    `/group/${brandId}/edit`
                );
                // console.log(response.data);
                this.group = response.data.group; // Populate form with the existing group data
                this.group.status =
                    this.group.status === "Active" ? "Active" : "Inactive";
            } catch (error) {
                this.serverError = "Error fetching group data.";
            }
        },
        async update() {
            this.errors = {}; // Reset errors before submission
            this.serverError = null;
            this.loading = true;
            const groupId = this.$route.params.uuid; // Assuming group ID is in route params
            setTimeout(async () => {
                try {
                    const response = await this.$axios.put(
                        `/group/${groupId}`,
                        this.group
                    );

                    if (response.data.success) {
                        this.$router.push({ name: "GroupIndex" }); // Redirect to brand list page
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.serverError = "Error updating group.";
                    }
                } finally {
                    // Stop loading after the request (or simulated time) is done
                    this.loading = false;
                }
            }, 1000);
        },
        resetForm() {
            this.fetchGroup(); // Reset the form with existing group data
            this.errors = {};
            this.$refs.form.resetValidation();
        },
    },
};
</script>
