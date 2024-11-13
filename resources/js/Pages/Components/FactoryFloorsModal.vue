<!-- <template>
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
</style> -->

<template>
    <v-dialog v-model="localVisible" max-width="1200px">
        <v-card>
            <v-card-title class="header-title">
                <span>Factory Name : {{ factory.name }} </span>
                <v-spacer></v-spacer>
                <v-btn icon @click="close" class="close-btn">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-row dense>
                    <v-col
                        v-for="floor in factory.floors"
                        :key="floor.id"
                        cols="3"
                    >
                        <v-card class="floor-card" elevation="2">
                            <v-card-title>
                                <strong
                                    ><v-icon icon="md:home"></v-icon> Floor
                                    No:</strong
                                >
                                <!-- <v-icon class="mr-2">mdi-home-floor</v-icon> -->
                                {{ floor.name }}
                            </v-card-title>
                            <v-card-subtitle class="floor-subtitle">
                                <div>
                                    <strong>Units:</strong>
                                </div>
                                <v-row dense>
                                    <v-col
                                        v-for="unit in floor.units"
                                        :key="unit.id"
                                        cols="12"
                                    >
                                        <v-card class="unit-card" outlined>
                                            <v-card-title>
                                                <v-icon class="mr-2"
                                                    >mdi-domain</v-icon
                                                >
                                                {{ unit.name }}
                                            </v-card-title>
                                            <v-card-subtitle
                                                class="unit-subtitle"
                                            >
                                                <div>
                                                    <strong>Lines:</strong>
                                                </div>
                                                <v-list dense>
                                                    <v-list-item
                                                        v-for="line in unit.lines"
                                                        :key="line.id"
                                                        class="line-item"
                                                    >
                                                        <v-list-item-content>
                                                            <v-icon
                                                                small
                                                                class="mr-2"
                                                            >
                                                                mdi-vector-line
                                                            </v-icon>
                                                            {{ line.name }}
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                    <v-list-item
                                                        v-if="
                                                            unit.lines
                                                                .length === 0
                                                        "
                                                    >
                                                        No Lines
                                                    </v-list-item>
                                                </v-list>
                                            </v-card-subtitle>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-subtitle>
                        </v-card>
                    </v-col>
                </v-row>
                <div v-if="factory.floors.length === 0" class="no-floors">
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
            default: () => ({
                floors: [],
            }),
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
            this.localVisible = false;
        },
    },
};
</script>

<style scoped>
.header-title {
    background-color: #8154f1; /* Green background */
    /* color: white; */
    font-weight: bold;
    padding: 16px;
    text-align: center;
}

.floor-card {
    border: 1px solid #3f51b5;
    background-color: #e3f2fd;
    color: #1e88e5;
}

.floor-subtitle,
.unit-subtitle {
    padding: 8px;
}

.unit-card {
    background-color: #bbdefb;
    color: #0d47a1;
}

.line-item {
    color: #1976d2;
}

.no-floors {
    color: #ff5252;
    font-weight: bold;
    text-align: center;
    margin-top: 16px;
}
.close-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    color: rgb(17, 16, 16);
}
</style>
