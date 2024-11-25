<script setup>
const props = defineProps({
  route: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    required: false,
    default: "bi bi-circle",
  },
});
</script>

<template>
  <li>
    <router-link :to="{ name: route }">
      <i :class="icon"></i>
      <span>{{ label }}</span>
    </router-link>
  </li>
</template>
