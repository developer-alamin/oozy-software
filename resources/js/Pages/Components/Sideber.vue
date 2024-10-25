<template>
    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'AdminDashboard' }">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </router-link>
            </li>
            <!-- End Dashboard Nav -->

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    :class="{ show: isMechineRoute }"
                    data-bs-target="#components-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-menu-button-wide"></i><span>Mechines</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    :class="{ show: isMechineRoute }"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <router-link to="#">
                            <i class="bi bi-circle"></i><span>Add Mechine</span>
                        </router-link>
                    </li>
                    <li>
                        <a href="#">
                            <i class="bi bi-circle"></i
                            ><span>All Mechines </span>
                        </a>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'BrandIndex' }"
                            active-class="active"
                            :class="{ active: isBrandRoute }"
                        >
                            <i class="bi bi-circle"></i><span>Brand</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'CategoryIndex' }"
                            active-class="active"
                            :class="{ active: isCategoryRoute }"
                        >
                            <i class="bi bi-circle"></i><span>Category</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'ModelIndex' }"
                            active-class="active"
                            :class="{ active: isModelRoute }"
                        >
                            <i class="bi bi-circle"></i><span>Model</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'UnitIndex' }"
                            active-class="active"
                            :class="{ active: isUnitRoute }"
                        >
                            <i class="bi bi-circle"></i><span>Unit</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'SupplierIndex' }"
                            active-class="active"
                            :class="{ active: isSupplierRoute }"
                        >
                            <i class="bi bi-circle"></i><span>Supplier</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <!-- End Components Nav -->

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#forms-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-journal-text"></i><span>Factory</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="forms-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <a href="forms-elements.html">
                            <i class="bi bi-circle"></i><span>Add Factory</span>
                        </a>
                    </li>
                    <li>
                        <a href="forms-layouts.html">
                            <i class="bi bi-circle"></i
                            ><span>All Factories </span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- End Forms Nav -->

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#tables-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-layout-text-window-reverse"></i
                    ><span>Stock</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="tables-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <a href="tables-general.html">
                            <i class="bi bi-circle"></i><span>Stock List </span>
                        </a>
                    </li>
                    <li>
                        <a href="tables-data.html">
                            <i class="bi bi-circle"></i
                            ><span>Stock Alert </span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- End Tables Nav -->

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#charts-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-bar-chart"></i><span>Reports</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="charts-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <a href="charts-chartjs.html">
                            <i class="bi bi-circle"></i><span>Chart.js</span>
                        </a>
                    </li>
                    <li>
                        <a href="charts-apexcharts.html">
                            <i class="bi bi-circle"></i><span>ApexCharts</span>
                        </a>
                    </li>
                    <li>
                        <a href="charts-echarts.html">
                            <i class="bi bi-circle"></i><span>ECharts</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- End Charts Nav -->

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#icons-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-gem"></i><span>User & Permissions</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="icons-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <router-link :to="{ name: 'AdminUserIndex' }">
                            <i class="bi bi-circle"></i
                            ><span>Admin User List</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'AdminUserCreate' }">
                            <i class="bi bi-circle"></i
                            ><span>Add Admin User</span>
                        </router-link>
                    </li>
                    <li>
                        <a href="icons-bootstrap.html">
                            <i class="bi bi-circle"></i
                            ><span>All User List</span>
                        </a>
                    </li>
                    <li>
                        <a href="icons-boxicons.html">
                            <i class="bi bi-circle"></i><span>Role</span>
                        </a>
                    </li>
                    <li>
                        <a href="icons-boxicons.html">
                            <i class="bi bi-circle"></i><span>Permission </span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#tables-nav-tt"
                    data-bs-toggle="collapse"
                    href="#"
                    :class="{ show: isTechnicianRouteShow }"
                >
                    <i class="bi bi-layout-text-window-reverse"></i
                    ><span>Technician</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="tables-nav-tt"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                    :class="{ show: isTechnicianRouteShow }"
                >
                    <li>
                        <router-link
                            :to="{ name: 'TechnicianCreate' }"
                            active-class="active"
                            :class="{ active: isTechnicianCreateActive }"
                        >
                            <i class="bi bi-circle"></i
                            ><span>Add Technician </span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'TechnicianIndex' }"
                            active-class="active"
                            :class="{ active: isTechnicianIndexActive }"
                        >
                            <i class="bi bi-circle"></i
                            ><span>All Technicians</span>
                        </router-link>
                    </li>
                </ul>
            </li>
             <!-- Start Settings Nav -->
            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#settings-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-layout-text-window-reverse"></i
                    ><span>Settings</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="settings-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <router-link
                            :to="{ name: 'LineIndex' }"
                            active-class="active"
                            :class="{ active: isLineIndex }"
                        >
                            <i class="bi bi-circle"></i><span>Line</span>
                        </router-link>
                    </li>
                     <li>
                        <router-link
                            :to="{ name: 'GroupIndex' }"
                            active-class="active"
                            :class="{ active: isGroupIndex }"
                        >
                            <i class="bi bi-circle"></i><span>Group</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'RentIndex' }"
                            active-class="active"
                            :class="{ active: isRentIndex }"
                        >
                            <i class="bi bi-circle"></i><span>Rent</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <!-- End Settings Nav -->

            <li class="nav-heading"></li>

            <!-- End Profile Page Nav -->

            <!-- End F.A.Q Page Nav -->

            <!-- End Contact Page Nav -->
            <!--
            <li class="nav-item">
                <a class="nav-link collapsed" href="pages-register.html">
                    <i class="bi bi-card-list"></i>
                    <span>Register</span>
                </a>
            </li> -->
            <!-- End Register Page Nav -->

            <!-- End Login Page Nav -->

            <!-- End Error 404 Page Nav -->

            <!-- End Blank Page Nav -->
        </ul>
    </aside>
    <!-- End Sidebar-->
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

// Access the current route
const route = useRoute();

// Check if the current route is part of the Mechine-related routes
const isMechineRoute = computed(() => {
    return [
        "ModelIndex",
        "ModelCreate",
        "ModelEdit",
        "SupplierIndex",
        "SupplierCreate",
        "SupplierEdit",
        "CategoryIndex",
        "CategoryCreate",
        "CategoryEdit",
        "UnitIndex",
        "UnitCreate",
        "UnitEdit",
        "BrandIndex",
        "BrandCreate",
        "BrandEdit",
        "BrandTrash",
    ].includes(route.name);
});

// Check if the current route is for a specific model-related route
const isModelRoute = computed(() =>
    ["ModelIndex", "ModelCreate", "ModelEdit"].includes(route.name)
);

// Check if the current route is for a specific supplier-related route
const isSupplierRoute = computed(() =>
    ["SupplierIndex", "SupplierCreate", "SupplierEdit"].includes(route.name)
);
const isCategoryRoute = computed(() =>
    ["CategoryIndex", "CategoryCreate", "CategoryEdit"].includes(route.name)
);
const isBrandRoute = computed(() =>
    ["BrandIndex", "BrandCreate", "BrandEdit", "BrandTrash"].includes(
        route.name
    )
);
const isUnitRoute = computed(() =>
    ["UnitIndex", "UnitCreate", "UnitEdit"].includes(route.name)
);
//
const isTechnicianRouteShow = computed(() => {
    return [
        "TechnicianIndex",
        "TechnicianCreate",
        "TechnicianEdit",
        "TechnicianTrash",
    ].includes(route.name);
});
const isTechnicianCreateActive = computed(
    () => route.name === "TechnicianCreate"
);
const isTechnicianIndexActive = computed(
    () => route.name === "TechnicianIndex"
);
</script>

<style scoped>
/* Styles for when the router link is active */
.active {
    font-weight: bold;
    color: #007bff; /* Blue color to indicate active link */
}
</style>

<style></style>
