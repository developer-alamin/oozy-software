<template>
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <!-- Start Dashboard Nav -->
      <li class="nav-item">
        <router-link class="nav-link" :to="{ name: 'AdminDashboard' }">
          <i class="bi bi-house-door"></i>
          <span>Dashboard</span>
        </router-link>
      </li>
      <!-- End Dashboard Nav -->

      <!-- Start License Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#license-license"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-shield-lock"></i>
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
      <!-- End License Nav -->


       <!-- Start Machine Requisition Nav -->
      <li class="nav-item">
        <a
         :class="{ show: isActiveMachineRequisionRoute }"
          class="nav-link collapsed"
          data-bs-target="#machine-requisition"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-shield-lock"></i>
          <span>Machine Requisition</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="machine-requisition"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
        >
          <li>
            <router-link 
            :to="{ name: 'MachineRequisitionIndex' }"
            active-class="active"
            :class="{ active: isActiveMachineIndex }"
            >
              <i class="bi bi-circle"></i>
              <span>Machine Requisition</span>
            </router-link>
          </li>
          <li>
            <router-link 
            :to="{ name: 'MachineRequisitionCelender' }"
            active-class="active"
            :class="{ active: isActiveMachineCelander }"
            >
              <i class="bi bi-circle"></i>
              <span>Machine Calender</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Machine Requisition Nav -->

      <!-- Start Machines Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          :class="{ show: isMechineRouteShow }"
          data-bs-target="#components-nav"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-gear"></i><span>Machines</span
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
      <!-- End Machines Nav -->
      <!-- Start Organization Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#forms-nav-company"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isOrganizationRouteShow }"
        >
          <i class="bi bi-building"></i><span>Organization</span
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
       <!-- End Organization Nav -->
      <!-- Start parse Nav-->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-parse"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-funnel"></i><span>Spares parts</span
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
              <i class="bi bi-circle"></i><span>Create Parts</span>
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
              <i class="bi bi-circle"></i><span>All Parts</span>
            </router-link>
          </li>
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
        </ul>
      </li>
       <!-- End parse Nav-->

      <!-- Start Service Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-Service"
          data-bs-toggle="collapse"
          :class="{ show: isBreakdownRouteShow }"
          href="#"
        >
          <i class="bi bi-truck"></i><span>Breakdown Service</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-Service"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isBreakdownRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'BreakdownServiceCreate' }"
              :class="{ active: isBreakdownServiceRouteCreate }"
              active-class="active"
            >
              <i class="bi bi-circle"></i><span>Add New Service</span>
            </router-link>
          </li>

          <li>
            <router-link
              :to="{ name: 'BreakdownServiceIndex' }"
              :class="{ active: isBreakdownServiceRouteIndex }"
              active-class="active"
            >
              <i class="bi bi-circle"></i><span>All Services</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Service Nav -->

      <!-- Start Preventive Service Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-preventive-service"
          data-bs-toggle="collapse"
          :class="{ show: isPreventiveServiceRouteShow }"
          href="#"
        >
          <i class="bi bi-wrench"></i><span>Preventive Service</span
          ><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul
          id="tables-nav-preventive-service"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isPreventiveServiceRouteShow }"
        >
          <li>
            <router-link
              :to="{ name: 'PreventiveServiceCreate' }"
              :class="{ active: isPreventiveServiceRouteCreate }"
              active-class="active"
            >
              <i class="bi bi-circle"></i><span>Add New Service</span>
            </router-link>
          </li>

          <li>
            <router-link
              :to="{ name: 'PreventiveServiceIndex' }"
              :class="{ active: isPreventiveServiceRouteIndex }"
              active-class="active"
            >
              <i class="bi bi-circle"></i><span>All Services</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Service Nav -->


      <!-- Start Reports Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#charts-nav"
          data-bs-toggle="collapse"
          href="#"
        >
          <i class="bi bi-graph-up"></i><span>Reports</span
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
      <!-- End Reports Nav -->
      <!-- Start User & Permissions Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#icons-nav"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isAdminUserRouteShow }"
        >
          <i class="bi bi-people"></i><span>User & Permissions</span
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
      <!-- End User & Permissions Nav -->
      <!-- Start Technician Groups Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-tt"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isGroupRouteShow }"
        >
          <i class="bi bi-person-gear"></i><span>Technician Groups</span
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
      <!-- End Technician Groups Nav -->
      <!-- Start Machine Settings Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#tables-nav-pp"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isMachineManageSettingsRouteShow }"
        >
          <i class="bi bi-tools"></i
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
      <!-- End Machine Settings Nav -->
        <!-- Start Fish Bone Nav -->
      <li class="nav-item">
          <a
            class="nav-link collapsed"
            data-bs-target="#fish-bone-nav"
            data-bs-toggle="collapse"
            href="#"
            :class="{ show: isFishBoneRouteShow }"
          >
            <i class="bi bi-sliders"></i><span>Fish Bone Digram</span
            ><i class="bi bi-chevron-down ms-auto"></i>
          </a>
        <ul
          id="fish-bone-nav"
          class="nav-content collapse"
          data-bs-parent="#sidebar-nav"
          :class="{ show: isFishBoneRouteShow }"
        >
        <li>
            <router-link
              :to="{ name: 'FishboneDigrameIndex' }"
              active-class="active"
              :class="{ active: isActiveFishboneDigrameIndexRoute}"
            >
              <i class="bi bi-circle"></i><span>Fishbone Digrame</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'ProblemNoteIndex' }"
              active-class="active"
              :class="{ active: isActiveProblemNoteRoute}"
            >
              <i class="bi bi-circle"></i><span>Problem Note</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'FishboneCategoryIndex' }"
              active-class="active"
              :class="{ active: isFishboneCategoryRoute }"
            >
              <i class="bi bi-circle"></i><span>Category List</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'CausesIndex' }"
              active-class="active"
              :class="{ active: isCausesRoute }"
            >
              <i class="bi bi-circle"></i><span>Cause List</span>
            </router-link>
          </li>
        </ul>
      </li>
      <!-- End Fish Bone Nav -->
      <!-- Start Settings Nav -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          data-bs-target="#settings-nav"
          data-bs-toggle="collapse"
          href="#"
          :class="{ show: isSettingRouteShow }"
        >
          <i class="bi bi-sliders"></i><span>Settings</span
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
              :to="{ name: 'BreakDownNoteIndex' }"
              active-class="active"
              :class="{ active: isBreakDownNoteRoute }"
            >
              <i class="bi bi-circle"></i><span>Problems List</span>
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'ActionIndexPage' }"
              active-class="active"
              :class="{ active: isAdminActionsIndexRoute}"
            >
              <i class="bi bi-circle"></i><span>Actions List</span>
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



// Check if the current route is part of the Fishbone-related routes


const isActiveProblemNoteRoute = computed(() => {
  return [
    "ProblemNoteIndex",
  ].includes(route.name);
});

// Check if the current route is part of the Mechine-related routes
const isActiveMachineCelander = computed(() => {
  return [
    "MachineRequisitionCelender"
  ].includes(route.name);
});


const isActiveMachineIndex = computed(() => {
  return [
    "MachineRequisitionIndex",
  ].includes(route.name);
});
// Check if the current route is part of the Mechine-related routes
const isActiveMachineRequisionRoute = computed(() => {
  return [
    "MachineRequisitionIndex",
    "MachineRequisitionCelender"
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
const isBreakdownRouteShow = computed(() => {
  return [
    "BreakdownServiceCreate",
    "BreakdownServiceIndex",
    "BreakdownServiceEdit",
    "BreakdownServiceTrash",
  ].includes(route.name);
});

const isBreakdownServiceRouteIndex = computed(() =>
  ["ServiceIndex"].includes(route.name)
);
const isBreakdownServiceRouteCreate = computed(() =>
  ["ServiceCreate"].includes(route.name)
);

const isBreakdownServiceRouteBreakdownServiceHistory = computed(() =>
  ["BreakdownServiceHistory"].includes(route.name)
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

//preventive service 
const isPreventiveServiceRouteShow = computed(() => {
  return [
    "PreventiveServiceCreate",
    "PreventiveServiceIndex",
    "PreventiveServiceEdit",
    "AssignToTechnicianPreventiveService",
    "PreventiveServiceStart",
    "PreventiveServiceStartDetails",
    "PreventiveServiceTrash",
    "PreventiveServiceDetailsList",
    "SignleServiceDetails"
  ].includes(route.name);
});

const isPreventiveServiceRouteIndex = computed(() =>
  ["PreventiveServiceIndex"].includes(route.name)
);
const isPreventiveServiceRouteCreate = computed(() =>
  ["PreventiveServiceCreate"].includes(route.name)
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

const isAdminActionsIndexRoute = computed(() =>
  [
    "ActionIndexPage",
  ].includes(route.name)
);




const isProblemNoteRoute = computed(() =>
  [
    "ProblemNoteIndex",
    "ProblemNoteCreate",
    'ProblemNoteEdit',
    'ProblemNoteTrash'
  ].includes(route.name)
);
const isFishboneCategoryRoute = computed(() =>
  [
    "FishboneCategoryIndex",
    "FishboneCategoryCreate",
    'FishboneCategoryEdit',
    'FishboneCategoryTrash'
  ].includes(route.name)
);
const isCausesRoute = computed(() =>
  [
    "CausesIndex",
    "CausesCreate",
    'CausesEdit',
    'CausesTrash'
  ].includes(route.name)
);


const isFishBoneRouteShow = computed(() => {
  return [
    "ProblemNoteIndex",
    "ProblemNoteCreate",
    'ProblemNoteEdit',
    'ProblemNoteTrash',
    "FishboneCategoryIndex",
    "FishboneCategoryCreate",
    'FishboneCategoryEdit',
    'FishboneCategoryTrash',
    "CausesIndex",
    "CausesCreate",
    'CausesEdit',
    'CausesTrash',
    "FishboneDigrameIndex",
  ].includes(route.name);
});

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
    "MachineStatusIndex",
    "MachineStatusCreate",
    "MachineStatusEdit",
    "MachineStatusTrash"
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
    "ActionIndexPage",
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
