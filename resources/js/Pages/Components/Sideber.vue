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
          data-bs-target="#license-license"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-menu-button-wide"></i>
          <span>License</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="license-license"
          class="nav-content collapse"
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
          :class="{ show: isMechineRouteShow }"
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
          :class="{ show: isMechineRouteShow }"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link
              :to="{ name: 'MechineCreate' }"
              active-class="active"
              :class="{ active: isMachineCreateRoute }"
            >
              <i class="bi bi-circle"></i><span>Machine Add</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'MechineIndex' }"
              active-class="active"
              :class="{ active: isMachineRoute }"
            >
              <i class="bi bi-circle"></i><span> Machine All </span>
            </router-link>
          </li>
          <!-- <li>
            <router-link
              :to="{ name: 'MachineMovement' }"
              active-class="active"
              :class="{ active: isMachineMovementRoute }"
            >
              <i class="bi bi-circle"></i><span> Machine Movement </span>
            </router-link>
          </li> -->
          <!-- <li>
            <router-link
              :to="{ name: 'MachineLocation' }"
              active-class="active"
              :class="{ active: isMachineLocationRoute }"
            >
              <i class="bi bi-circle"></i
              ><span> Machine Movement Location List </span>
            </router-link>
          </li> -->

          <!-- <li>
            <router-link
              :to="{ name: 'MechineHistoryList' }"
              active-class="active"
              :class="{ active: isMechineHistoryListRoute }"
            >
              <i class="bi bi-circle"></i
              ><span> All Movement History List </span>
            </router-link>
          </li> -->
        </ul>
      </li>
      <!-- End Components Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#forms-nav-company"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isOrganizationRouteShow }"
        >
          <i class="bi bi-journal-text"></i><span>Organization</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="forms-nav-company"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isOrganizationRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'AllCompanyIndex' }"
              active-class="active"
              :class="{ active: isCompanyRoute }"
            >
              <i class="bi bi-circle"></i><span>Company</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'FactoryIndex' }"
              active-class="active"
              :class="{ active: isFactoryRoute }"
            >
              <i class="bi bi-circle"></i><span>Factory</span>
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
          <li>
            <router-link
              :to="{ name: 'UnitIndex' }"
              active-class="active"
              :class="{ active: isUnitRoute }"
            >
              <i class="bi bi-circle"></i><span>Mechine Unit</span>
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
        </ul>
      </li>

      <!-- <li class="nav-item">
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
                            <i class="bi bi-circle"></i
                            ><span>Create Factory</span>
                        </router-link>
                    </li>
                </ul>
            </li> -->
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
                //active: isParseRouteShow?.isParseCreateActive,
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
                //active: isParseRouteShow.isParseIndexActive,
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
          data-bs-target="#tables-nav-Service"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Service</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-Service"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link :to="{ name: 'ServiceCreate' }" active-class="active">
              <i class="bi bi-circle"></i><span>Create Service</span>
            </router-link>
          </li>

          <li>
            <router-link :to="{ name: 'ServiceIndex' }" active-class="active">
              <i class="bi bi-circle"></i><span>All Service</span>
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
          :class="{ show: isGroupRouteShow }"
        >
          <i class="bi bi-layout-text-window-reverse"></i><span>Groups</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-tt"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isGroupRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'GroupCreate' }"
              active-class="active"
              :class="{ active: isGroupCreateActive }"
            >
              <i class="bi bi-circle"></i><span>Add Group </span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'GroupIndex' }"
              active-class="active"
              :class="{ active: isGroupIndexActive }"
            >
              <i class="bi bi-circle"></i><span>All Group</span>
            </router-link>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-pp"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isMachineManageSettingsRouteShow }"
        >
          <i class="bi bi-layout-text-window-reverse"></i
          ><span>Machine Settings</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-pp"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isMachineManageSettingsRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'MechineTypeIndex' }"
              active-class="active"
              :class="{ active: MechineTypeRoute }"
            >
              <i class="bi bi-circle"></i><span>Type</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'MechineSourceIndex' }"
              active-class="active"
              :class="{ active: isMechineSourceRoute }"
            >
              <i class="bi bi-circle"></i><span>Source</span>
            </router-link>
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
              :to="{ name: 'MachineStatusIndex' }"
              active-class="active"
              :class="{ active: isMachineStatusRoute }"
            >
              <i class="bi bi-circle"></i><span>Machine Status</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#rant-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-layout-text-window-reverse"></i
                    ><span>Rant</span><i class="bi bi-chevron-down ms-auto"></i>
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
                            :class="{ active: isRentIndex }"
                        >
                            <i class="bi bi-circle"></i><span>All Rent </span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            :to="{ name: 'RentCreate' }"
                            active-class="active"
                            :class="{ active: isRentCreate }"
                        >
                            <i class="bi bi-circle"></i><span>Create Rent</span>
                        </router-link>
                    </li>
                </ul>
            </li> -->

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
              :to="{ name: 'CategoryIndex' }"
              active-class="active"
              :class="{ active: isCategoryRoute }"
            >
              <i class="bi bi-circle"></i><span>Parse Category</span>
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
              :to="{ name: 'TechnicianIndex' }"
              active-class="active"
              :class="{ active: isTechnicianRoute }"
            >
              <i class="bi bi-circle"></i><span>Technician</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'OperatorIndex' }"
              active-class="active"
              :class="{ active: isOperatorRoute }"
            >
              <i class="bi bi-circle"></i><span>Supervisor</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'BreakDownNoteIndex' }"
              active-class="active"
              :class="{ active: isBreakDownNoteRoute }"
            >
              <i class="bi bi-circle"></i><span>Breakdown Problem Note</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'TagIndex' }"
              active-class="active"
              :class="{ active: isTagRoute }"
            >
              <i class="bi bi-circle"></i><span>Machine Tags</span>
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
const isMechineRouteShow = computed(() => {
  return [
    "MechineIndex",
    "MechineCreate",
    "MechineTrash",
    "MachineMovement",
    "MachineLocation",
    "MechineHistoryList",
    "MechineTransfer",
    "MachineShow",
  ].includes(route.name);
});

const isMachineRoute = computed(() =>
  [
    "MechineIndex",
    "MechineEdit",
    "MechineTrash",
    "MechineTransfer",
    "MachineShow",
  ].includes(route.name)
);
const isMachineCreateRoute = computed(() =>
  ["MechineCreate"].includes(route.name)
);
const isMachineMovementRoute = computed(() =>
  ["MachineMovement"].includes(route.name)
);
const isMachineLocationRoute = computed(() =>
  ["MachineLocation"].includes(route.name)
);
const isMechineHistoryListRoute = computed(() =>
  ["MechineHistoryList"].includes(route.name)
);

// organization
const isOrganizationRouteShow = computed(() => {
  return [
    "AllCompanyIndex",
    "CompanyCreate",
    "CompanyEdit",
    "CompanyTrash",
    "FactoryIndex",
    "FactoryCreate",
    "FactoryEdit",
    "FactoryTrash",
    "FloorIndex",
    "FloorCreate",
    "FloorEdit",
    "FloorTrash",
    "UnitIndex",
    "UnitCreate",
    "UnitEdit",
    "UnitTrash",
    "LineIndex",
    "LineCreate",
    "LineEdit",
    "LineTrash",
  ].includes(route.name);
});

const isCompanyRoute = computed(() =>
  ["AllCompanyIndex", "CompanyCreate", "CompanyEdit", "CompanyTrash"].includes(
    route.name
  )
);
const isFactoryRoute = computed(() =>
  ["FactoryIndex", "FactoryCreate", "FactoryEdit", "FactoryTrash"].includes(
    route.name
  )
);
const isFloorRoute = computed(() =>
  ["FloorIndex", "FloorCreate", "FloorEdit", "FloorTrash"].includes(route.name)
);
const isUnitRoute = computed(() =>
  ["UnitIndex", "UnitCreate", "UnitEdit", "UnitTrash"].includes(route.name)
);
const isLineRoute = computed(() =>
  ["LineIndex", "LineCreate", "LineEdit", "LineTrash"].includes(route.name)
);

const isCategoryRoute = computed(() =>
  ["CategoryIndex", "CategoryCreate", "CategoryEdit", "CategoryTrash"].includes(
    route.name
  )
);
const isBreakDownNoteRoute = computed(() =>
  [
    "BreakDownNoteIndex",
    "BreakDownNoteCreate",
    "BreakDownNoteEdit",
    "BreakDownNoteTrash",
  ].includes(route.name)
);

// const isGroupRoute = computed(() =>
//     ["GroupIndex", "GroupCreate", "GroupEdit", "GroupTrash"].includes(
//         route.name
//     )
// );

//technician
const isGroupRouteShow = computed(() => {
  return ["GroupIndex", "GroupCreate", "GroupEdit", "GroupTrash"].includes(
    route.name
  );
});
const isGroupCreateActive = computed(() => route.name === "GroupCreate");
const isGroupIndexActive = computed(() => route.name === "GroupIndex");

//technician
const isMachineManageSettingsRouteShow = computed(() => {
  return [
    "BrandIndex",
    "BrandCreate",
    "BrandEdit",
    "BrandTrash",
    "ModelIndex",
    "ModelCreate",
    "ModelEdit",
    "ModelTrash",
    "SupplierIndex",
    "SupplierCreate",
    "SupplierEdit",
    "SupplierTrash",
    "MechineTypeIndex",
    "MechineTypeCreate",
    "MechineTypeEdit",
    "MechineSourceIndex",
    "MechineSourceCreate",
    "MechineSourceEdit",
  ].includes(route.name);
});
const isBrandRoute = computed(() =>
  ["BrandIndex", "BrandCreate", "BrandEdit", "BrandTrash"].includes(route.name)
);
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

const MechineTypeRoute = computed(() =>
  [
    "MechineTypeIndex",
    "MechineTypeCreate",
    "MechineTypeEdit",
    "MechineTypeTrash",
  ].includes(route.name)
);
const isMechineSourceRoute = computed(() =>
  [
    "MechineSourceIndex",
    "MechineSourceCreate",
    "MechineSourceEdit",
    "MechineSourceTrash",
  ].includes(route.name)
);

const isMachineStatusRoute = computed(() =>
  [
    "MachineStatusIndex",
    "MachineStatusCreate",
    "MachineStatusEdit",
    "MachineStatusTrash",
  ].includes(route.name)
);

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
    "CategoryIndex",
    "CategoryCreate",
    "CategoryEdit",
    "CategoryTrash",
    "ParseUnitIndex",
    "ParseUnitCreate",
    "ParseUnitEdit",
    "ParseUnitTrash",
    "TechnicianIndex",
    "TechnicianCreate",
    "TechnicianEdit",
    "TechnicianTrash",
    "OperatorIndex",
    "OperatorCreate",
    "OperatorEdit",
    "OperatorTrash",
    "TagIndex",
    "TagCreate",
    "TagEdit",
    "TagTrash",
    "BreakDownNoteIndex",
    "BreakDownNoteCreate",
    "BreakDownNoteEdit",
    "BreakDownNoteTrash",
  ].includes(route.name);
});

const isTechnicianRoute = computed(() =>
  [
    "TechnicianIndex",
    "TechnicianCreate",
    "TechnicianEdit",
    "TechnicianTrash",
  ].includes(route.name)
);
const isOperatorRoute = computed(() =>
  ["OperatorIndex", "OperatorCreate", "OperatorEdit", "OperatorTrash"].includes(
    route.name
  )
);
const isTagRoute = computed(() =>
  ["TagIndex", "TagCreate", "TagEdit", "TagTrash"].includes(route.name)
);
const isParseUnitRoute = computed(() =>
  [
    "ParseUnitIndex",
    "ParseUnitCreate",
    "ParseUnitEdit",
    "ParseUnitTrash",
  ].includes(route.name)
);
</script>

<style scoped>
/* Styles for when the router link is active */
.active {
  font-weight: bold;
  color: #007bff; /* Blue color to indicate active link */
}
</style>
