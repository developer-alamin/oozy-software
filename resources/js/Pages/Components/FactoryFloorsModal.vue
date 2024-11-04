<template>
    <v-dialog v-model="localVisible" max-width="600px">
        <v-card>
            <v-card-title>
                <span>{{ factory.name }} - Floors</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-list>
                    <v-list-item-group>
                        <v-list-item
                            v-for="floor in factory.floors"
                            :key="floor.id"
                        >
                            <v-list-item-content>
                                <v-list-item-title>{{
                                    floor.name
                                }}</v-list-item-title>
                                <v-list-item-subtitle>
                                    Units:
                                    <span v-if="floor.units.length"
                                        >{{ floor.units.length }}
                                    </span>
                                    <span v-else>No Units</span>
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-item-group>
                </v-list>
                <div v-if="factory.floors.length === 0">
                    No floors available for this factory.
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        factory: {
            type: Object,
            default: () => ({}),
        },
    },
    computed: {
        localVisible: {
            get() {
                return this.visible;
            },
            set(value) {
                this.$emit("update:visible", value);
            },
        },
    },
    methods: {
        close() {
            this.localVisible = false; // This will emit update:visible event
        },
    },
};
</script>

<style scoped>
/* Optional: Add styles for the modal */
</style>
