<template>
    <v-container>
        <supplier-create-form ref="SupplierCreateFrom" />
    </v-container>
</template>

<script>
import SupplierCreateFrom from "../../Components/Admin/Supplier/SupplierCreateFrom.vue";

export default {
    components: {
        SupplierCreateFrom,
    },
};
</script>
