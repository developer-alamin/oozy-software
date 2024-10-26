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
        meta: { title: "Login" },
    },

    {
        path: "/register",
        name: "Register",
        component: Register,
        meta: { title: "Register" },
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
            {
                path: "factory/index", // New route for Contact
                name: "FactoryIndex",
                component: adminComponents.FactoryIndex,
                meta: { title: "Factory Index" },
            },
            // factory
            {
                path: "factory/create", // New route for Factory
                name: "FactoryCreate",
                component: adminComponents.FactoryCreate,
                meta: { title: "Factory Create" },
            },
            {
                path: "factory/edit/:id", // Dynamic route for Factory Edit
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
                path: "supplier/edit/:id", // Dynamic route for Supplier Edit
                name: "SupplierEdit",
                component: adminComponents.SupplierEdit,
                meta: { title: "Edit Supplier" },
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
                path: "model/edit/:id", // Dynamic route for Model Edit
                name: "ModelEdit",
                component: adminComponents.ModelEdit,
                meta: { title: "Edit Model" },
                props: true, // Enables passing route params as props
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
                path: "category/edit/:id", // Dynamic route for Category Edit
                name: "CategoryEdit",
                component: adminComponents.CategoryEdit,
                meta: { title: "Edit Category" },
                props: true, // Enables passing route params as props
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
                path: "unit/edit/:id", // Dynamic route for Unit Edit
                name: "UnitEdit",
                component: adminComponents.UnitEdit,
                meta: { title: "Edit Unit" },
                props: true, // Enables passing route params as props
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
                path: "technician/edit/:id", // Dynamic route for Technician Edit
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
                meta: { title: "line Index" },
            },
            {
                path: "line/create", // New route for line create
                name: "LineCreate",
                component: adminComponents.LineCreate,
                meta: { title: "line Create" },
            },
            {
                path: "line/edit/:id", // New route for line edit
                name: "LineEdit",
                component: adminComponents.LineEdit,
                meta: { title: "line Edit" },
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
                path: "group/edit/:id", // New route for Group edit
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
                meta: { title: "rent index" },
                props: true,
            },
            {
                path: "rent/create", // New route for rent Create
                name: "RentCreate",
                component: adminComponents.RentCreate,
                meta: { title: "Rent Create" },
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
//     const token = localStorage.getItem("token");
//     const authStore = useAuthStore();
//     console.log(to.name, authStore.role);

//     if (to.name === "AdminDashboard" && authStore.role !== "admin") {
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
