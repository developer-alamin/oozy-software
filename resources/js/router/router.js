import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/authStore"; // Import the Pinia store
import Login from "../Pages/Auth/Login.vue";
import Register from "../Pages/Auth/Register.vue"; // Import Register component
import UserDashboard from "../Pages/User/Dashboard.vue";
import AdminDashboard from "../Pages/Admin/Dashboard.vue";
import UserLayout from "../Pages/Layouts/UserLayout.vue";
import AdminLayout from "../Pages/Layouts/AdminLayout.vue";
import * as adminComponents from "./adminComponents.js";
// console.log(adminComponents);

// Your route definitions can follow

import UserTechnicianIndex from "../Pages/Technician/Index.vue";
import Contact from "../Pages/Contact.vue";

const routes = [
    {
        path: "/",
        name: "Login",
        component: Login,
        meta: { title: "Login", requiresAuth: true },
    },

    {
        path: "/register",
        name: "Register",
        component: Register,
        meta: { title: "Register", requiresAuth: true },
    },

    {
        path: "/admin",
        component: AdminLayout, // Use the AdminLayout here
        meta: { requiresAuth: true },
        children: [
            {
                path: "dashboard",
                name: "AdminDashboard",
                component: AdminDashboard,
                meta: { title: "Admin Dashboard" },
            },
            {
                path: "user/index", // New route for Contact
                name: "AdminUserIndex",
                component: adminComponents.AdminUserIndex,
                meta: { title: "Admin All User Index" },
            },
            {
                path: "user/create", // New route for Contact
                name: "AdminUserCreate",
                component: adminComponents.AdminUserCreate,
                meta: { title: "Admin User Create" },
            },
            {
                path: "user/edit/:id", // Dynamic route for User Edit
                name: "AdminUserEdit",
                component: adminComponents.AdminUserEdit,
                meta: { title: "Edit Admin User" },
                props: true, // Enables passing route params as props
            },
            {
                path: "user/trash", // New route for Contact
                name: "AdminUserTrash",
                component: adminComponents.AdminUserTrash,
                meta: { title: "Admin User Trash" },
            },
            {
                path: "company/user/index", // New route for Contact
                name: "AllUserIndex",
                component: adminComponents.AllUserIndex,
                meta: { title: "All User Index" },
            },
            {
                path: "company/user/create", // New route for Contact
                name: "UserCreate",
                component: adminComponents.UserCreate,
                meta: { title: "User Create" },
            },
            {
                path: "company/create", // New route for Contact
                name: "CompanyCreate",
                component: adminComponents.CompanyCreate,
                meta: { title: "Company Create" },
            },
            {
                path: "company/index", // New route for Contact
                name: "AllCompanyIndex",
                component: adminComponents.AllCompanyIndex,
                meta: { title: "All Company Index" },
            },
            // factory
            {
                path: "factory/index", // New route for Contact
                name: "FactoryIndex",
                component: adminComponents.FactoryIndex,
                meta: { title: "Factory Index" },
            },

            {
                path: "factory/create", // New route for Factory
                name: "FactoryCreate",
                component: adminComponents.FactoryCreate,
                meta: { title: "Factory Create" },
            },
            {
                path: "factory/:uuid/edit", // Dynamic route for Factory Edit
                name: "FactoryEdit",
                component: adminComponents.FactoryEdit,
                meta: { title: "Edit Factory" },
                props: true, // Enables passing route params as props
            },
            {
                path: "factory/trash", // New route for Contact
                name: "FactoryTrash",
                component: adminComponents.FactoryTrash,
                meta: { title: "Factory Trash" },
            },
            // mechine
            {
                path: "mechine/index", // New route for Contact
                name: "MechineIndex",
                component: adminComponents.MechineIndex,
                meta: { title: "Mechine Index" },
            },
            {
                path: "mechine/transfer/list", // New route for Contact
                name: "MechineTransferList",
                component: adminComponents.MechineTransferList,
                meta: { title: "Mechine Transfer List" },
            },
            {
                path: "mechine/history/list", // New route for Contact
                name: "MechineHistoryList",
                component: adminComponents.MechineHistoryList,
                meta: { title: "Mechine History List" },
            },

            {
                path: "mechine/create", // New route for Factory
                name: "MechineCreate",
                component: adminComponents.MechineCreate,
                meta: { title: "Mechine Create" },
            },
            {
                path: "mechine/:uuid/edit", // Dynamic route for Factory Edit
                name: "MechineEdit",
                component: adminComponents.MechineEdit,
                meta: { title: "Edit Mechine" },
                props: true, // Enables passing route params as props
            },
            {
                path: "mechine/:uuid/transfer", // Dynamic route for mechine transfer
                name: "MechineTransfer",
                component: adminComponents.MechineTransfer,
                meta: { title: "Mechine Transfer" },
                props: true, // Enables passing route params as props
            },
            {
                path: "mechine/trash", // New route for Contact
                name: "MechineTrash",
                component: adminComponents.MechineTrash,
                meta: { title: "Mechine Trash" },
            },
            {
                path: "machine/movement", // New route for Contact
                name: "MachineMovement",
                component: adminComponents.MachineMovement,
                meta: { title: "Machine Movement" },
            },

            // service
            {
                path: "survice/index", // New route for Contact
                name: "ServiceIndex",
                component: adminComponents.ServiceIndex,
                meta: { title: "Survice Index" },
            },
            {
                path: "survice/create", // New route for Contact
                name: "ServiceCreate",
                component: adminComponents.ServiceCreate,
                meta: { title: "Survice Create" },
            },
            {
                path: "survice/history/create/:id", // New route for Contact
                name: "ServiceHistoryCreate",
                component: adminComponents.ServiceHistoryCreate,
                meta: { title: "Survice History Create" },
            },
            {
                path: "survice/:uuid/edit", // Dynamic route for Survice Edit
                name: "ServiceEdit",
                component: adminComponents.ServiceEdit,
                meta: { title: "Edit Survice" },
                props: true, // Enables passing route params as props
            },
            {
                path: "survice/trash", // Dynamic route for Survice Edit
                name: "ServiceTrash",
                component: adminComponents.ServiceTrash,
                meta: { title: "Trash Survice" },
                props: true, // Enables passing route params as props
            },

            {
                path: "supplier/index", // New route for Contact
                name: "SupplierIndex",
                component: adminComponents.SupplierIndex,
                meta: { title: "Supplier Index" },
            },
            {
                path: "supplier/create", // New route for Contact
                name: "SupplierCreate",
                component: adminComponents.SupplierCreate,
                meta: { title: "Supplier Create" },
            },
            {
                path: "supplier/:uuid/edit", // Dynamic route for Supplier Edit
                name: "SupplierEdit",
                component: adminComponents.SupplierEdit,
                meta: { title: "Edit Supplier" },
                props: true, // Enables passing route params as props
            },
            {
                path: "supplier/trash", // Dynamic route for Supplier Edit
                name: "SupplierTrash",
                component: adminComponents.SupplierTrash,
                meta: { title: "Trash Supplier" },
                props: true, // Enables passing route params as props
            },

            {
                path: "model/index", // New route for Contact
                name: "ModelIndex",
                component: adminComponents.ModelIndex,
                meta: { title: "Model Index" },
            },
            {
                path: "model/create", // New route for Contact
                name: "ModelCreate",
                component: adminComponents.ModelCreate,
                meta: { title: "Model Create" },
            },
            {
                path: "model/edit/:uuid", // Dynamic route for Model Edit
                name: "ModelEdit",
                component: adminComponents.ModelEdit,
                meta: { title: "Edit Model" },
                props: true, // Enables passing route params as props
            },
            {
                path: "model/trash", // New route for Contact
                name: "ModelTrash",
                component: adminComponents.ModelTrash,
                meta: { title: "Model Trash" },
            },

            {
                path: "category/index", // New route for Contact
                name: "CategoryIndex",
                component: adminComponents.CategoryIndex,
                meta: { title: "Category Index" },
            },
            {
                path: "category/create", // New route for Contact
                name: "CategoryCreate",
                component: adminComponents.CategoryCreate,
                meta: { title: "Category Create" },
            },

            {
                path: "category/edit/:uuid", // Dynamic route for Category Edit
                name: "CategoryEdit",
                component: adminComponents.CategoryEdit,
                meta: { title: "Edit Category" },
                props: true, // Enables passing route params as props
            },
            {
                path: "category/trash", // New route for Contact
                name: "CategoryTrash",
                component: adminComponents.CategoryTrash,
                meta: { title: "Category Trash" },
            },
            // brand
            {
                path: "brand/index", // New route for Contact
                name: "BrandIndex",
                component: adminComponents.BrandIndex,
                meta: { title: "Brand Index" },
            },
            {
                path: "brand/create", // New route for Contact
                name: "BrandCreate",
                component: adminComponents.BrandCreate,
                meta: { title: "Brand Create" },
            },

            {
                path: "brand/edit/:uuid", // Dynamic route for Brand Edit
                name: "BrandEdit",
                component: adminComponents.BrandEdit,
                meta: { title: "Edit Brand" },
                props: true, // Enables passing route params as props
            },
            {
                path: "brand/trash", // New route for Contact
                name: "BrandTrash",
                component: adminComponents.BrandTrash,
                meta: { title: "Brand Trash" },
            },

            {
                path: "unit/index", // New route for Contact
                name: "UnitIndex",
                component: adminComponents.UnitIndex,
                meta: { title: "Unit Index" },
            },
            {
                path: "unit/create", // New route for Contact
                name: "UnitCreate",
                component: adminComponents.UnitCreate,
                meta: { title: "Unit Create" },
            },
            {
                path: "unit/edit/:uuid", // Dynamic route for Unit Edit
                name: "UnitEdit",
                component: adminComponents.UnitEdit,
                meta: { title: "Edit Unit" },
                props: true, // Enables passing route params as props
            },
            {
                path: "unit/trash", // New route for Contact
                name: "UnitTrash",
                component: adminComponents.UnitTrash,
                meta: { title: "Unit Trash" },
            },
            // parse
            {
                path: "parse/index", // New route for Contact
                name: "ParseIndex",
                component: adminComponents.ParseIndex,
                meta: { title: "Parse Index" },
            },
            {
                path: "parse/create", // New route for Contact
                name: "ParseCreate",
                component: adminComponents.ParseCreate,
                meta: { title: "Parse Create" },
            },
            {
                path: "parse/:uuid/edit", // Dynamic route for  Edit
                name: "ParseEdit",
                component: adminComponents.ParseEdit,
                meta: { title: "Edit Parse " },
                props: true, // Enables passing route params as props
            },
            {
                path: "parse/trash", // New route for Contact
                name: "ParseTrash",
                component: adminComponents.ParseTrash,
                meta: { title: "Parse Trash" },
            },
            // parse unit
            {
                path: "parse/unit/index", // New route for Contact
                name: "ParseUnitIndex",
                component: adminComponents.ParseUnitIndex,
                meta: { title: "Parse Unit Index" },
            },
            {
                path: "parse/unit/create", // New route for Contact
                name: "ParseUnitCreate",
                component: adminComponents.ParseUnitCreate,
                meta: { title: "ParseUnit Create" },
            },
            {
                path: "parse/unit/edit/:uuid", // Dynamic route for Unit Edit
                name: "ParseUnitEdit",
                component: adminComponents.ParseUnitEdit,
                meta: { title: "Edit Parse Unit" },
                props: true, // Enables passing route params as props
            },
            {
                path: "parse/unit/trash", // New route for Contact
                name: "ParseUnitTrash",
                component: adminComponents.ParseUnitTrash,
                meta: { title: "Parse Unit Trash" },
            },
            // technician
            {
                path: "technician/index", // New route for Contact
                name: "TechnicianIndex",
                component: adminComponents.TechnicianIndex,
                meta: { title: "Technician Index" },
            },
            {
                path: "technician/create", // New route for Contact
                name: "TechnicianCreate",
                component: adminComponents.TechnicianCreate,
                meta: { title: "Technician Create" },
            },
            {
                path: "technician/edit/:uuid", // Dynamic route for Technician Edit
                name: "TechnicianEdit",
                component: adminComponents.TechnicianEdit,
                meta: { title: "Edit Technician" },
                props: true, // Enables passing route params as props
            },
            {
                path: "technician/trash", // New route for Contact
                name: "TechnicianTrash",
                component: adminComponents.TechnicianTrash,
                meta: { title: "Technician Trash" },
            },
            // operator
            {
                path: "operator/index", // New route for Contact
                name: "OperatorIndex",
                component: adminComponents.OperatorIndex,
                meta: { title: "Operator Index" },
            },
            {
                path: "operator/create", // New route for Contact
                name: "OperatorCreate",
                component: adminComponents.OperatorCreate,
                meta: { title: "Operator Create" },
            },
            {
                path: "operator/edit/:uuid", // Dynamic route for operator Edit
                name: "OperatorEdit",
                component: adminComponents.OperatorEdit,
                meta: { title: "Edit Operator" },
                props: true, // Enables passing route params as props
            },
            {
                path: "operator/trash", // New route for Contact
                name: "OperatorTrash",
                component: adminComponents.OperatorTrash,
                meta: { title: "Operator Trash" },
            },
            {
                path: "contact", // New route for Contact
                name: "Contact",
                component: Contact,
                meta: { title: "Contact" },
            },

            {
                path: "line/index", // New route for line index
                name: "LineIndex",
                component: adminComponents.LineIndex,
                meta: { title: "Line Index" },
            },
            {
                path: "line/create", // New route for line create
                name: "LineCreate",
                component: adminComponents.LineCreate,
                meta: { title: "Line Create" },
            },
            {
                path: "line/:uuid/edit", // New route for line edit
                name: "LineEdit",
                component: adminComponents.LineEdit,
                meta: { title: "Line Edit" },
                props: true,
            },
            {
                path: "line/trash", // New route for line trash
                name: "LineTrash",
                component: adminComponents.LineTrash,
                meta: { title: "line Trash" },
                props: true,
            },
            {
                path: "group/create", // New route for Group Create
                name: "GroupCreate",
                component: adminComponents.GroupCreate,
                meta: { title: "Group Create" },
                props: true,
            },
            {
                path: "group/index", // New route for Group Index
                name: "GroupIndex",
                component: adminComponents.GroupIndex,
                meta: { title: "Group Index" },
                props: true,
            },
            {
                path: "group/edit/:uuid", // New route for Group edit
                name: "GroupEdit",
                component: adminComponents.GroupEdit,
                meta: { title: "group Edit" },
                props: true,
            },
            {
                path: "group/trash", // New route for line trash
                name: "GroupTrash",
                component: adminComponents.GroupTrash,
                meta: { title: "group Trash" },
                props: true,
            },
            {
                path: "rent/index", // New route for rent index
                name: "RentIndex",
                component: adminComponents.RentIndex,

                meta: { title: "Rent index" },
                props: true,
            },
            {
                path: "rent/create", // New route for rent Create
                name: "RentCreate",
                component: adminComponents.RentCreate,
                meta: { title: "Rent Create" },
                props: true,
            },
            {
                path: "rent/edit/:uuid", // New route for rent Edit
                name: "RentEdit",
                component: adminComponents.RentEdit,
                meta: { title: "Rent Edit" },
                props: true,
            },
            {
                path: "rent/trash", // New route for rent trash
                name: "RentsTrash",
                component: adminComponents.RentsTrash,
                meta: { title: "Rent Trash" },
                props: true,
            },
            {
                path: "floor/index", // New route for Floor index
                name: "FloorIndex",
                component: adminComponents.FloorIndex,
                meta: { title: "Floor index" },
                props: true,
            },
            {
                path: "floor/create", // New route for Floor create
                name: "FloorCreate",
                component: adminComponents.FloorCreate,
                meta: { title: "Floor Create" },
                props: true,
            },
            {
                path: "floor/:uuid/edit", // New route for Floor create
                name: "FloorEdit",
                component: adminComponents.FloorEdit,
                meta: { title: "Floor Edit" },
                props: true,
            },
            {
                path: "floor/trash", // New route for floor trash
                name: "FloorTrash",
                component: adminComponents.FloorTrash,
                meta: { title: "Floor Trash" },
                props: true,
            },
            {
                path: "mechine/type/index", // New route for mechine type index
                name: "MechineTypeIndex",
                component: adminComponents.MechineTypeIndex,
                meta: { title: "Mechine Type Index" },
                props: true,
            },
            {
                path: "mechine/type/create", // New route for mechine type index
                name: "MechineTypeCreate",
                component: adminComponents.MechineTypeCreate,
                meta: { title: "Mechine Type Create" },
                props: true,
            },
            {
                path: "mechine/type/edit/:uuid", // New route for mechine type index
                name: "MechineTypeEdit",
                component: adminComponents.MechineTypeEdit,
                meta: { title: "Mechine Type Edit" },
                props: true,
            },
            {
                path: "mechine/type/trash", // New route for floor trash
                name: "MechineTypeTrash",
                component: adminComponents.MechineTypeTrash,
                meta: { title: "Mechine Type Trash" },
                props: true,
            },
            {
                path: "mechine/source/index", // New route for mechine source index
                name: "MechineSourceIndex",
                component: adminComponents.MechineSourceIndex,
                meta: { title: "Mechine Source Index" },
                props: true,
            },
            {
                path: "mechine/source/create", // New route for mechine Source index
                name: "MechineSourceCreate",
                component: adminComponents.MechineSourceCreate,
                meta: { title: "Mechine Source Create" },
                props: true,
            },
            {
                path: "mechine/source/edit/:uuid", // New route for mechine Source index
                name: "MechineSourceEdit",
                component: adminComponents.MechineSourceEdit,
                meta: { title: "Mechine Source Edit" },
                props: true,
            },
            {
                path: "mechine/source/trash", // New route for Source trash
                name: "MechineSourceTrash",
                component: adminComponents.MechineSourceTrash,
                meta: { title: "Mechine Source Trash" },
                props: true,
            },
            {
                path: "machine/status/index", // New route for mechine source index
                name: "MachineStatusIndex",
                component: adminComponents.MachineStatusIndex,
                meta: { title: "Machine Status Index" },
                props: true,
            },
            {
                path: "machine/status/create", // New route for mechine Source index
                name: "MachineStatusCreate",
                component: adminComponents.MachineStatusCreate,
                meta: { title: "Machine Status Create" },
                props: true,
            },
            {
                path: "machine/status/edit/:uuid", // New route for mechine Source index
                name: "MachineStatusEdit",
                component: adminComponents.MachineStatusEdit,
                meta: { title: "Machine Status Edit" },
                props: true,
            },
            {
                path: "mechine/status/trash", // New route for Source trash
                name: "MachineStatusTrash",
                component: adminComponents.MachineStatusTrash,
                meta: { title: "Machine Status Trash" },
                props: true,
            },
        ],
    },
    // {
    //     path: "/admin/dashboard",
    //     name: "AdminDashboard",
    //     component: AdminDashboard,
    //     meta: { requiresAuth: true, title: "Admin Dashboard" },
    // },
    {
        path: "/user",
        component: UserLayout, // Use the UserLayout here
        meta: { requiresAuth: true },
        children: [
            {
                path: "dashboard",
                name: "UserDashboard",
                component: UserDashboard,
                meta: { title: "User Dashboard" },
            },
            {
                path: "contact", // New route for Contact
                name: "Contact",
                component: Contact,
                meta: { title: "Contact" },
            },
            {
                path: "technician/index", // New route for Contact
                name: "UserTechnicianIndex",
                component: UserTechnicianIndex,
                meta: { title: "Technician Index" },
            },

            {
                path: "contact", // New route for Contact
                name: "Contact",
                component: Contact,
                meta: { title: "Contact" },
            },
        ],
    },
    // {
    //     path: "/user/dashboard",
    //     name: "UserDashboard",
    //     component: UserDashboard,
    //     meta: { requiresAuth: true, title: "User Dashboard" },
    // },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard to protect routes
// router.beforeEach((to, from, next) => {
//     const authStore = useAuthStore();
//     const isAuthenticated = !!authStore.user; // Check if user is authenticated
//     console.log(isAuthenticated);

//     if (to.meta.requiresAuth && !isAuthenticated) {
//         next({ name: "Login" }); // Redirect to login if not authenticated
//     } else {
//         next();
//     }
// });
// router.beforeEach((to, from, next) => {
//     const role = localStorage.getItem("role");
//     const authStore = useAuthStore();
//     const isAuthenticated = to.meta.requiresAuth;
//     console.log(to.meta.requiresAuth, to.name, authStore.user);

//     if (to.name === "AdminDashboard" && !role) {
//         next({ name: "Login" }); // Redirect to login if not admin
//     } else if (to.name === "UserDashboard" && authStore.role !== "user") {
//         next({ name: "Login" }); // Redirect to login if not user
//     } else {
//         next();
//     }
// });

// Set page title based on route meta
router.afterEach((to) => {
    document.title = to.meta.title || "My Application"; // Set document title
});

// router.beforeEach((to, from, next) => {
//     const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
//     const isAuthenticated = checkAuthentication(); // Your logic for checking authentication

//     if (requiresAuth && !isAuthenticated) {
//         next({ name: "Login" }); // Redirect to login if not authenticated
//     } else {
//         next(); // Proceed to the route
//     }
// });
export default router;
