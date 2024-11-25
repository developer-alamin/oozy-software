<script setup lang="ts">
import { reactive, ref } from "vue";

// import { useAuthStore } from "@/stores/generalSettingStore";
import { useAuthStore } from "@/stores/authStore";
import { useAxios } from "@/composables/useAxios";

const authStore = useAuthStore();

const tableData = reactive({
  itemsPerPage: 5,
  headers: [
    {
      title: "Dessert (100g serving)",
      align: "start",
      sortable: false,
      key: "name",
    },
    { title: "Calories", key: "calories", align: "end" },
    { title: "Fat (g)", key: "fat", align: "end" },
    { title: "Carbs (g)", key: "carbs", align: "end" },
    { title: "Protein (g)", key: "protein", align: "end" },
    { title: "Iron (%)", key: "iron", align: "end" },
  ],
  search: "",
  serverItems: [],
  loading: false,
  totalItems: 0,
});
const loadData = async () => {
  const ax = await useAxios("get", "/admin/user/all");
};
</script>
<template>
  <v-card>
    <v-card-title class="pt-5">License</v-card-title>
    <v-data-table-server
      v-model:items-per-page="tableData.itemsPerPage"
      :headers="tableData.headers"
      :items="tableData.serverItems"
      :items-length="tableData.totalItems"
      :loading="tableData.loading"
      :search="tableData.search"
      item-value="name"
      @update:options="loadData"
    ></v-data-table-server>
  </v-card>
</template>
