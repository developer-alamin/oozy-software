import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "./stores/authStore"; // Import the Pinia store
import Login from "./Pages/Auth/Login.vue";
import Register from "./Pages/Auth/Register.vue"; // Import Register component
import UserDashboard from "./Pages/User/Dashboard.vue";
import AdminDashboard from "./Pages/Admin/Dashboard.vue";
import UserLayout from "./Pages/Layouts/UserLayout.vue";
import AdminLayout from "./Pages/Layouts/AdminLayout.vue";
import SupplierIndex from "./Pages/Admin/Supplier/Index.vue";
import SupplierEdit from "./Pages/Admin/Supplier/Edit.vue";
import SupplierCreate from "./Pages/Components/Admin/Supplier/SupplierCreateFrom.vue";
import ModelIndex from "./Pages/Admin/Model/Index.vue";
import ModelCreate from "./Pages/Admin/Model/Create.vue";
import ModelEdit from "./Pages/Admin/Model/Edit.vue";
import CategoryIndex from "./Pages/Admin/Category/Index.vue";
import CategoryCreate from "./Pages/Admin/Category/Create.vue";
import CategoryEdit from "./Pages/Admin/Category/Edit.vue";
import UnitIndex from "./Pages/Admin/Unit/Index.vue";
import UnitCreate from "./Pages/Admin/Unit/Create.vue";
import UnitEdit from "./Pages/Admin/Unit/Edit.vue";
import Contact from "./Pages/Contact.vue";

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
                path: "supplier/index", // New route for Contact
                name: "SupplierIndex",
                component: SupplierIndex,
                meta: { title: "Supplier Index" },
            },
            {
                path: "supplier/create", // New route for Contact
                name: "SupplierCreate",
                component: SupplierCreate,
                meta: { title: "Supplier Create" },
            },
            {
                path: "supplier/edit/:id", // Dynamic route for Supplier Edit
                name: "SupplierEdit",
                component: SupplierEdit,
                meta: { title: "Edit Supplier" },
                props: true, // Enables passing route params as props
            },
            {
                path: "model/index", // New route for Contact
                name: "ModelIndex",
                component: ModelIndex,
                meta: { title: "Model Index" },
            },
            {
                path: "model/create", // New route for Contact
                name: "ModelCreate",
                component: ModelCreate,
                meta: { title: "Model Create" },
            },
            {
                path: "model/edit/:id", // Dynamic route for Model Edit
                name: "ModelEdit",
                component: ModelEdit,
                meta: { title: "Edit Model" },
                props: true, // Enables passing route params as props
            },

            {
                path: "category/index", // New route for Contact
                name: "CategoryIndex",
                component: CategoryIndex,
                meta: { title: "Category Index" },
            },
            {
                path: "category/create", // New route for Contact
                name: "CategoryCreate",
                component: CategoryCreate,
                meta: { title: "Category Create" },
            },

            {
                path: "category/edit/:id", // Dynamic route for Category Edit
                name: "CategoryEdit",
                component: CategoryEdit,
                meta: { title: "Edit Category" },
                props: true, // Enables passing route params as props
            },

            {
                path: "unit/index", // New route for Contact
                name: "UnitIndex",
                component: UnitIndex,
                meta: { title: "Unit Index" },
            },
            {
                path: "unit/create", // New route for Contact
                name: "UnitCreate",
                component: UnitCreate,
                meta: { title: "Unit Create" },
            },
            {
                path: "unit/edit/:id", // Dynamic route for Unit Edit
                name: "UnitEdit",
                component: UnitEdit,
                meta: { title: "Edit Unit" },
                props: true, // Enables passing route params as props
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
    //     path: "/admin/dashboard",
    //     name: "AdminDashboard",
    //     component: AdminDashboard,
    //     meta: { requiresAuth: true, title: "Admin Dashboard" },
    // },
    {
        path: "/user",
        component: UserLayout, // Use the AdminLayout here
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

export default router;
