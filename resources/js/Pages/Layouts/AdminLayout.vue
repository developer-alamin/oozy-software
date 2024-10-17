<!-- src/Layouts/AdminLayout.vue -->
<template>
    <Header />
    <Sidebar :logo="logo" />
    <main id="main" class="main">
        <router-view />
    </main>
    <!-- <Footer /> -->
</template>

<script setup>
import Sidebar from "../Components/Sideber.vue";
import Header from "../Components/Header.vue";
import Footer from "../Components/Footer.vue";
import logo from "../../../../public/assets/imgs/theme/logo.svg";
</script>

<style scoped>
/* Style your layout here */
</style>
