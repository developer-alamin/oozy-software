<template>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <router-link
        :to="{ name: 'AdminDashboard' }"
        class="logo d-flex align-items-center"
      >
        <!-- <img src="assets/img/logo.png" alt="" /> -->
        <img :src="loginImage" class="mx-auto" width="50px" alt="" />
        <span class="d-none d-lg-block">Oozy System</span>
      </router-link>
      <i class="bi bi-list toggle-sidebar-btn ml-5" @click="toggleSidebar"></i>
    </div>
    <!-- End Logo -->
    <!--
        <div class="search-bar">
            <form
                class="search-form d-flex align-items-center"
                method="POST"
                action="#"
            >
                <input
                    type="text"
                    name="query"
                    placeholder="Search"
                    title="Enter search keyword"
                />
                <button type="submit" title="Search">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div> -->
    <!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle" href="#">
            <i class="bi bi-search"></i>
          </a>
        </li>
        <!-- End Search Icon-->

        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span> </a
          ><!-- End Notification Icon -->

          <ul
            class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications"
          >
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"
                ><span class="badge rounded-pill bg-primary p-2 ms-2"
                  >View all</span
                ></a
              >
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider" />
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider" />
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider" />
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider" />
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>
          </ul>
          <!-- End Notification Dropdown Items -->
        </li>

        <!-- End Messages Nav -->

        <li class="nav-item dropdown pe-3">
          <a
            class="nav-link nav-profile d-flex align-items-center pe-0"
            href="#"
            data-bs-toggle="dropdown"
          >
            <!-- <img
                            src="assets/img/profile-img.jpg"
                            alt="Profile"
                            class="rounded-circle"
                        /> -->
            <span class="d-none d-md-block dropdown-toggle ps-2">{{
              userName
            }}</span> </a
          ><!-- End Profile Iamge Icon -->

          <ul
            class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile"
          >
            <li class="dropdown-header">
              <h6 v-if="userName">{{ userName }}</h6>
              <span>{{ userRole }}</span>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>

            <li>
              <a
                class="dropdown-item d-flex align-items-center"
                href="users-profile.html"
              >
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>

            <li>
              <a
                class="dropdown-item d-flex align-items-center"
                href="users-profile.html"
              >
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>

            <li>
              <a
                class="dropdown-item d-flex align-items-center"
                href="pages-faq.html"
              >
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>

            <li>
              <a
                class="dropdown-item d-flex align-items-center"
                href="#"
                @click="store.logout()"
              >
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
          <!-- End Profile Dropdown Items -->
        </li>
        <!-- End Profile Nav -->
      </ul>
    </nav>
    <!-- End Icons Navigation -->
  </header>
  <!-- End Header -->
</template>

<script setup>
import { ref, onMounted } from "vue";
import loginImage from "../../../img/login_page_img.png";
import { useAuthStore } from "@/stores/authStore";
import { useAxios } from "@/composables/useAxios";
const userName = localStorage.getItem("user");
const userRole = localStorage.getItem("role");
// console.log(userName);
const sidebarVisible = ref(false);

const store = useAuthStore();

const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
  document.body.classList.toggle("toggle-sidebar", sidebarVisible.value);
};

const user = ref(null);
const testData = async () => {
  const api = await useAxios("get", "/admin/user/all", {}, true);
  console.log(api);
};
testData();
// Fetch user info when component mounts
// onMounted(() => {
//     fetchUserInfo();
// });
</script>

<style scoped>
.logo img {
  max-height: 50px;
  /* margin-right: 6px; */
}
@media (min-width: 1200px) {
  .logo {
    width: 235px !important;
  }
}

.header {
  transition: all 0.5s;
  z-index: 997;
  height: 60px;
  /* box-shadow: 0px 2px 20px rgba(1, 41, 112, 0.1); */
  box-shadow: none !important;
  background-color: #fff;
  padding-left: 20px;
}
</style>
