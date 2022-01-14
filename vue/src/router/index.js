import { createRouter, createWebHashHistory } from "vue-router";
import AdminLogin from "../components/admin/authentication/AdminLogin.vue";
import AdminRoot from "../components/admin/AdminRoot.vue";
import CreateTransaction from "../components/user/transactions/CreateTransaction.vue";
import AskForTransaction from "../components/user/transactions/AskForTransaction.vue";
import UserTransactions from "../components/user/transactions/UserTransactions.vue";
import Categories from "../components/user/categories/Categories.vue";
import UserProfile from "../components/user/profile/UserProfile.vue";
import Main from "../components/user/Main.vue";
import UserLogin from "../components/user/authentication/UserLogin.vue";
import SignUp from "../components/user/authentication/SignUp.vue";
import notFound from "../components/Errors/not-found.vue";
import UserRoot from "../components/user/UserRoot.vue";
import Administration from "../components/admin/Administration.vue";
import CreateCredits from "../components/admin/credits/CreateCredits.vue";
import UsersManagement from "../components/admin/userManagement/UsersManagement.vue";
import DefaultCategories from "../components/admin/categoriesManagement/DefaultCategories.vue";
import GlobalStatistics from "../components/admin/statistics/GlobalStatistics.vue";
import AdminProfile from "../components/admin/adminProfile/AdminProfile.vue";

const routes = [
  {
    path: "/vcard",
    component: UserRoot,
    meta: { requiresAuth: true },
    redirect: {
      name: "Main",
    },
    beforeEnter: (to, from, next) => {
      if (
        localStorage.getItem("adminLoginState") &&
        store.state.admin &&
        sessionStorage.getItem("admintoken")
      ) {
        next({
          name: "not-found",
        });
      } else {
        next();
      }
    },
    children: [
      {
        path: " ",
        name: "Main",
        component: Main,
        meta: { requiresAuth: true, title: "Inicío" },
      },
      {
        path: "transacao",
        name: "CreateTransaction",
        component: CreateTransaction,
        meta: { requiresAuth: true, title: "Transações" },
      },
      {
        path: "pedirDinheiro",
        name: "AskForTransaction",
        component: AskForTransaction,
        meta: { requiresAuth: true, title: "Pedir Dinheiro" },
      },
      {
        path: "transacoes",
        name: "UserTransactions",
        component: UserTransactions,
        meta: { requiresAuth: true, title: "Lista de transações" },
      },
      {
        path: "categorias",
        name: "Categories",
        component: Categories,
        meta: { requiresAuth: true, title: "Categorias" },
      },
      {
        path: "perfil",
        name: "UserProfile",
        component: UserProfile,
        meta: { requiresAuth: true, title: "Perfil" },
      },
    ],
  },
  {
    path: "/Login",
    name: "UserLogin",
    component: UserLogin,
    meta: { title: "Login" },
    beforeEnter: (to, from, next) => {
      if (
        localStorage.getItem("adminLoginState") &&
        store.state.admin &&
        sessionStorage.getItem("admintoken")
      ) {
        next({
          name: "not-found",
        });
      } else {
        next();
      }
    },
  },
  {
    path: "/Signup",
    name: "SignUp",
    component: SignUp,
    meta: { title: "Registo" },
  },
  {
    path: "/adminLogin",
    name: "AdminLogin",
    component: AdminLogin,
    meta: { requiresAuth: false, title: "Admin Login" },
    beforeEnter: (to, from, next) => {
      if (
        localStorage.getItem("loginState") &&
        store.state.user &&
        sessionStorage.getItem("token")
      ) {
        next({
          name: "not-found",
        });
      } else {
        next();
      }
    },
  },
  {
    path: "/admin",
    name: "admin",
    component: AdminRoot,
    redirect: {
      name: "Administration",
    },
    meta: { requiresAuth: true, adminRole: true, title: "início" },
    beforeEnter: (to, from, next) => {
      if (
        localStorage.getItem("loginState") &&
        store.state.user &&
        sessionStorage.getItem("token")
      ) {
        next({
          name: "not-found",
        });
      } else {
        next();
      }
    },
    children: [
      {
        path: " ",
        name: "Administration",
        component: Administration,
      },
      {
        path: "criar-transacao",
        name: "CreateCredits",
        component: CreateCredits,
        meta: { title: "Criar Transação" },
      },
      {
        path: "gestao-users",
        name: "UsersManagement",
        component: UsersManagement,
        meta: { title: "Utilizadores" },
      },
      {
        path: "gestao-categorias",
        name: "DefaultCategories",
        component: DefaultCategories,
        meta: { title: "Categorias" },
      },
      {
        path: "estatisticas",
        name: "GlobalStatistics",
        component: GlobalStatistics,
        meta: { title: "Estatisticas" },
      },
      {
        path: "perfil",
        name: "AdminProfile",
        component: AdminProfile,
        meta: { title: "Perfil" },
      },
    ],
  },
  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    component: notFound,
    meta: { requiresAuth: true, title: "Not-found" },
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

import store from "../store";

router.beforeEach((to, from, next) => {
  document.title = to.meta.title;
  next();
});

router.beforeEach((to, from, next) => {
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    console.log();
    if (
      (!localStorage.getItem("loginState") &&
        store.state.admin.length == 0 &&
        !localStorage.getItem("adminLoginState")) ||
      (!sessionStorage.getItem("token") &&
        store.state.user.length == 0 &&
        !sessionStorage.getItem("admintoken"))
    ) {
      next({
        name: "UserLogin",
      });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
