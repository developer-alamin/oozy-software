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
          :class="{ show: isMachineRoute }"
          data-bs-target="#license"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-menu-button-wide"></i>
          <span>License</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="license"
          class="nav-content collapse"
          :class="{ show: isMachineRoute }"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link :to="{ name: 'AdminLicenseList' }">
              <i class="bi bi-circle"></i>
              <span>License List</span>
            </router-link>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a
          class="nav-link collapsed"
          :class="{ show: isMachineRoute }"
          data-bs-target="#components-nav"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-menu-button-wide"></i><span>Machines</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="components-nav"
          class="nav-content collapse"
          :class="{ show: isMachineRoute }"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link :to="{ name: 'MechineCreate' }">
              <i class="bi bi-circle"></i>
              <span>Machine Assign</span>
            </router-link>
          </li>
          <li>
            <router-link :to="{ name: 'MechineIndex' }">
              <i class="bi bi-circle"></i>
              <span> Machine Assign List </span>
            </router-link>
          </li>
          <li>
            <router-link :to="{ name: 'MechineTransferList' }">
              <i class="bi bi-circle"></i>
              <span> Machine Transfer List </span>
            </router-link>
          </li>
          <li>
            <router-link :to="{ name: 'MechineHistoryList' }">
              <i class="bi bi-circle"></i><span> Machine History List </span>
            </router-link>
          </li>

          <li>
            <router-link
              :to="{ name: 'UnitIndex' }"
              active-class="active"
              :class="{ active: isUnitRoute }"
            >
              <i class="bi bi-circle"></i><span>Machine Unit</span>
            </router-link>
          </li>

          <li>
            <router-link
              :to="{ name: 'MechineTypeIndex' }"
              active-class="active"
              :class="{ active: isMachineRoute.MechineTypeIndex }"
            >
              <i class="bi bi-circle"></i><span>Mechine Type</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'MechineSourceIndex' }"
              active-class="active"
              :class="{
                active: isMachineRoute.isMechineSourceIndex,
              }"
            >
              <i class="bi bi-circle"></i><span>Mechine Source</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Components Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#forms-nav-company"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isCompanyRouteShow }"
        >
          <i class="bi bi-journal-text"></i><span>Company</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="forms-nav-company"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isCompanyRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'CompanyCreate' }"
              active-class="active"
              :class="{ active: isCompanyCreateActive }"
            >
              <i class="bi bi-circle"></i><span>Add Company</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'AllCompanyIndex' }"
              active-class="active"
              :class="{ active: isCompanyIndexActive }"
            >
              <i class="bi bi-circle"></i><span>All Company</span>
            </router-link>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#forms-nav"
          data-bs-toggle="collapse"
          :class="{ show: isFactoryRouteShow }"
        >
          <i class="bi bi-journal-text"></i><span>Factory</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="forms-nav"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isFactoryRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'FactoryCreate' }"
              active-class="active"
              :class="{ active: isFactoryCreateActive }"
            >
              <i class="bi bi-circle"></i><span>Create Factory</span>
            </router-link>
          </li>

          <li>
            <router-link
              :to="{ name: 'FactoryIndex' }"
              active-class="active"
              :class="{ active: isFactoryIndexActive }"
            >
              <i class="bi bi-circle"></i><span>All Factories</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Forms Nav -->

      <!-- parse -->

      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-parse"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Parse</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-parse"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link
              :to="{ name: 'ParseCreate' }"
              active-class="active"
              :class="{
                active: isParseRouteShow.isParseCreateActive,
              }"
            >
              <i class="bi bi-circle"></i><span>Create Parse</span>
            </router-link>
          </li>

          <li>
            <router-link
              :to="{ name: 'ParseIndex' }"
              active-class="active"
              :class="{
                active: isParseRouteShow.isParseIndexActive,
              }"
            >
              <i class="bi bi-circle"></i><span>All Parse</span>
            </router-link>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Stock</span
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
              <i class="bi bi-circle"></i><span>Stock Alert </span>
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
          :class="{ show: isAdminUserRouteShow }"
        >
          <i class="bi bi-gem"></i><span>User & Permissions</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="icons-nav"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isAdminUserRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'AdminUserIndex' }"
              active-class="active"
              :class="{ active: isAdminUserIndexActive }"
            >
              <i class="bi bi-circle"></i><span>Admin User List</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'AdminUserCreate' }"
              active-class="active"
              :class="{ active: isAdminUserCreateActive }"
            >
              <i class="bi bi-circle"></i><span>Add Admin User</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'AllUserIndex' }"
              active-class="active"
              :class="{ active: isUserIndexActive }"
            >
              <i class="bi bi-circle"></i><span>All User List</span>
            </router-link>
          </li>
          <li>
            <router-link
              active-class="active"
              :class="{ active: isUserCreateActive }"
              :to="{ name: 'UserCreate' }"
            >
              <i class="bi bi-circle"></i><span>User Create</span>
            </router-link>
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
          <i class="bi bi-layout-text-window-reverse"></i><span>Technician</span
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
              <i class="bi bi-circle"></i><span>Add Technician </span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'TechnicianIndex' }"
              active-class="active"
              :class="{ active: isTechnicianIndexActive }"
            >
              <i class="bi bi-circle"></i><span>All Technicians</span>
            </router-link>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-tt"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isOperatorRouteShow }"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Operator</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-tt"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isOperatorRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'OperatorCreate' }"
              active-class="active"
              :class="{ active: isOperatorCreateActive }"
            >
              <i class="bi bi-circle"></i><span>Add Operator </span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'OperatorIndex' }"
              active-class="active"
              :class="{ active: isOperatorIndexActive }"
            >
              <i class="bi bi-circle"></i><span>All Operators</span>
            </router-link>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#rant-nav"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Rant</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="rant-nav"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link
              :to="{ name: 'RentIndex' }"
              active-class="active"
              :class="{ active: isRentRouteShow }"
            >
              <i class="bi bi-circle"></i><span>All Rent </span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'RentCreate' }"
              active-class="active"
              :class="{ active: isRentRouteShow }"
            >
              <i class="bi bi-circle"></i><span>Create Rent</span>
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
          :class="{ show: isSettingRouteShow }"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Settings</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="settings-nav"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isSettingRouteShow }"
        >
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
              :to="{ name: 'SupplierIndex' }"
              active-class="active"
              :class="{ active: isSupplierRoute }"
            >
              <i class="bi bi-circle"></i><span>Supplier</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'ParseUnitIndex' }"
              active-class="active"
              :class="{ active: isParseUnitRoute }"
            >
              <i class="bi bi-circle"></i><span>Parse Unit</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'LineIndex' }"
              active-class="active"
              :class="{ active: isLineRoute }"
            >
              <i class="bi bi-circle"></i><span>Line</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'GroupIndex' }"
              active-class="active"
              :class="{ active: isGroupRoute }"
            >
              <i class="bi bi-circle"></i><span>Group</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'FloorIndex' }"
              active-class="active"
              :class="{ active: isFloorRoute }"
            >
              <i class="bi bi-circle"></i><span>Floor</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Settings Nav -->
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
const isMachineRoute = computed(() => {
  return [
    "UnitIndex",
    "UnitCreate",
    "UnitEdit",
    "UnitTrash",
    "MechineTypeIndex",
    "MechineTypeCreate",
    "MechineTypeEdit",
    "MechineSourceIndex",
    "MechineSourceCreate",
    "MechineSourceEdit",
  ].includes(route.name);
});

// Check if the current route is for a specific model-related route
const isModelRoute = computed(() =>
  ["ModelIndex", "ModelCreate", "ModelEdit", "ModelTrash"].includes(route.name)
);

// Check if the current route is for a specific supplier-related route
const isSupplierRoute = computed(() =>
  ["SupplierIndex", "SupplierCreate", "SupplierEdit", "SupplierTrash"].includes(
    route.name
  )
);
const isCategoryRoute = computed(() =>
  ["CategoryIndex", "CategoryCreate", "CategoryEdit", "CategoryTrash"].includes(
    route.name
  )
);
const isBrandRoute = computed(() =>
  ["BrandIndex", "BrandCreate", "BrandEdit", "BrandTrash"].includes(route.name)
);
const isLineRoute = computed(() =>
  ["LineIndex", "LineCreate", "LineEdit", "LineTrash"].includes(route.name)
);
const isGroupRoute = computed(() =>
  ["GroupIndex", "GroupCreate", "GroupEdit", "GroupTrash"].includes(route.name)
);
const isFloorRoute = computed(() =>
  ["FloorIndex", "FloorCreate", "FloorEdit", "FloorTrash"].includes(route.name)
);
const isUnitRoute = computed(() =>
  ["UnitIndex", "UnitCreate", "UnitEdit", "UnitTrash"].includes(route.name)
);
const isParseUnitRoute = computed(() =>
  [
    "ParseUnitIndex",
    "ParseUnitCreate",
    "ParseUnitEdit",
    "ParseUnitTrash",
  ].includes(route.name)
);
// Company
const isCompanyRouteShow = computed(() => {
  return [
    "AllCompanyIndex",
    "CompanyCreate",
    "CompanyEdit",
    "CompanyTrash",
  ].includes(route.name);
});
// Parse
const isParseRouteShow = computed(() => {
  return ["ParseCreate", "CompanyEdit", "CompanyTrash"].includes(route.name);
});
const isCompanyCreateActive = computed(() => route.name === "CompanyCreate");
const isCompanyIndexActive = computed(() => route.name === "AllCompanyIndex");
const isRentRouteShow = computed(() => route.name === "ParseCreate");
// Company
const isFactoryRouteShow = computed(() => {
  return [
    "FactoryIndex",
    "FactoryCreate",
    "FactoryEdit",
    "FactoryTrash",
  ].includes(route.name);
});
const isFactoryCreateActive = computed(() => route.name === "FactoryCreate");
const isFactoryIndexActive = computed(() => route.name === "FactoryIndex");
//technician
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

//technician
const isOperatorRouteShow = computed(() => {
  return [
    "OperatorIndex",
    "OperatorCreate",
    "OperatorEdit",
    "OperatorTrash",
  ].includes(route.name);
});
const isOperatorCreateActive = computed(() => route.name === "OperatorCreate");
const isOperatorIndexActive = computed(() => route.name === "OperatorIndex");

// Company
const isAdminUserRouteShow = computed(() => {
  return [
    "AdminUserIndex",
    "AdminUserCreate",
    "AdminUserEdit",
    "AdminUserTrash",
    "AllUserIndex",
    "UserCreate",
  ].includes(route.name);
});
const isAdminUserIndexActive = computed(() => route.name === "AdminUserIndex");
const isAdminUserCreateActive = computed(
  () => route.name === "AdminUserCreate"
);
const isAdminUserEditActive = computed(() => route.name === "AdminUserEdit");
const isAdminUserTrashActive = computed(() => route.name === "AdminUserTrash");
const isUserCreateActive = computed(() => route.name === "UserCreate");
const isUserIndexActive = computed(() => route.name === "AllUserIndex");

const isSettingRouteShow = computed(() => {
  return [
    "BrandIndex",
    "BrandCreate",
    "BrandEdit",
    "BrandTrash",
    "ModelIndex",
    "ModelCreate",
    "ModelEdit",
    "ModelTrash",
    "CategoryIndex",
    "CategoryCreate",
    "CategoryEdit",
    "CategoryTrash",
    "SupplierIndex",
    "SupplierCreate",
    "SupplierEdit",
    "SupplierTrash",
    "LineIndex",
    "LineCreate",
    "LineEdit",
    "LineTrash",
    "GroupIndex",
    "GroupEdit",
    "GroupTrash",
    "FloorIndex",
    "FloorEdit",
    "FloorTrash",
    "ParseUnitIndex",
    "ParseUnitCreate",
    "ParseUnitEdit",
    "ParseUnitTrash",
  ].includes(route.name);
});
</script>

<style scoped>
/* Styles for when the router link is active */
.active {
  font-weight: bold;
  color: #007bff; /* Blue color to indicate active link */
}
</style>
