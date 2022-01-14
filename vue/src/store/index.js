import { createStore } from "vuex";
import axios from "axios";

axios.defaults.headers.common["Authorization"] = `Bearer ${
  sessionStorage.token ? sessionStorage.token : sessionStorage.admintoken
}`;

export default createStore({
  state: {
    user: [],
    categories: [],
    categoriesByPage: [],
    admin: [],
    admins: [],
    userTransactionData: [],
    adminTransactionsData: [],
    erroNumero: null,
    erroNome: null,
    erroEmail: null,
    erroPassword: null,
    erroConfirmPassword: null,
    erroCode: null,
    errorLogin: null,
    errorAdminLogin: null,
  },
  mutations: {
    setUserTransactionData(state, userTransactionData) {
      state.userTransactionData = userTransactionData;
    },
    setAdminTransactionsData(state, adminTransactionsData) {
      state.adminTransactionsData = adminTransactionsData;
    },
    resetUser(state) {
      state.user = null;
    },
    setUserData(state, userData) {
      state.user = userData;
    },
    setCategoriesDataByPage(state, categoriesData) {
      state.categoriesByPage = categoriesData;
    },
    setCategoriesData(state, categoriesData) {
      state.categories = categoriesData;
    },
    resetAdmin(state) {
      state.admin = null;
    },
    setAdminData(state, adminData) {
      state.admin = adminData;
    },
    setAdmins(state, adminsData) {
      state.admins = adminsData;
    },
    resetAdmins(state) {
      state.admins = [];
    },
  },
  actions: {
    async fetchUserTransactionData(context) {
      try {
        let response = await axios.get("vcards/data");
        context.commit("setUserTransactionData", response.data);
        return response.data;
      } catch (error) {
        console.log(error.response);
        throw error;
      }
    },
    async fetchAdminTransactionsData(context) {
      try {
        let response = await axios.get("admin/data");
        context.commit("setAdminTransactionsData", response.data);
        return response.data;
      } catch (error) {
        console.log(error.response);
        throw error;
      }
    },
    async fetchUserProfile(context) {
      try {
        let response = await axios.get("vcards");
        context.commit("setUserData", response.data.data);
        return response.data.data;
      } catch (error) {
        console.log(error);
        context.commit("resetUser", null);
        throw error;
      }
    },
    async fetchCategoriesByPage(context, page) {
      try {
        let response = await axios.get("vcards/categories?page=" + page);
        context.commit("setCategoriesDataByPage", response.data);
        console.log(response.data);
        return response.data;
      } catch (error) {
        console.log(error.response);
        throw error;
      }
    },
    async fetchCategories(context) {
      try {
        let response = await axios.get("vcards/categories");
        context.commit("setCategoriesData", response.data);
        console.log(response.data);
        return response.data;
      } catch (error) {
        console.log(error.response);
        throw error;
      }
    },
    async login(context, credentials) {
      try {
        let response = await axios.post("login", credentials);
        if (response.data.access_token) {
          sessionStorage.setItem("token", response.data.access_token);
          localStorage.setItem("loginState", true);
          axios.defaults.headers.common[
            "Authorization"
          ] = `Bearer ${sessionStorage.token}`;
          context.commit("setUserData", response.data.vcard);
        }
      } catch (error) {
        delete axios.defaults.headers.common.Authorization;
        sessionStorage.removeItem("token");
        localStorage.removeItem("loginState");
        context.commit("resetUser", null);
        context.dispatch("getLoginErrors", error);
        throw error;
      }
    },
    async getLoginErrors(context, error) {
      if (error.response.data.message) {
        context.state.errorLogin = error.response.data.message;
      }
    },
    async getSignUpErrors(context, error) {
      if (error.response.data.phone_number) {
        context.state.erroNumero = error.response.data.phone_number[0];
      }
      if (error.response.data.name) {
        context.state.erroNome = error.response.data.name[0];
      }
      if (error.response.data.email) {
        context.state.erroEmail = error.response.data.email[0];
      }
      if (error.response.data.password) {
        context.state.erroPassword = error.response.data.password[0];
      }
      if (error.response.data.confirmation_code) {
        context.state.erroCode = error.response.data.confirmation_code[0];
      }
    },
    async adminLogin(context, credentials) {
      try {
        let response = await axios.post("loginadmin", credentials);
        if (response.data.access_token) {
          console.log(response.data);
          sessionStorage.setItem("admintoken", response.data.access_token);
          localStorage.setItem("adminLoginState", true);
          localStorage.setItem("adminId", response.data.admin.id);
          axios.defaults.headers.common[
            "Authorization"
          ] = `Bearer ${sessionStorage.admintoken}`;
          context.commit("setAdminData", response.data.admin);
        }
      } catch (error) {
        console.log(error.response);
        delete axios.defaults.headers.common.Authorization;
        sessionStorage.removeItem("admintoken");
        localStorage.removeItem("adminLoginState");
        localStorage.removeItem("adminId");
        context.commit("resetAdmin", null);
        context.dispatch("getAdminLoginErrors", error);
        throw error;
      }
    },
    async getAdminLoginErrors(context, error) {
      if (error.response.data.message) {
        context.state.errorAdminLogin = error.response.data.message;
      }
    },
    async fetchAdminData(context) {
      try {
        let response = await axios.get(
          "admin/user/" + localStorage.getItem("adminId"),
        );
        context.commit("setAdminData", response.data.data);
        return response.data.data;
      } catch (error) {
        console.log(error.response);
        context.commit("resetAdmin", null);
        throw error;
      }
    },
    async fetchAllAdmins(context) {
      try {
        let response = await axios.get("admin/user");

        context.commit("setAdmins", response.data.data);
        console.log(context.state.admins);
        return response.data.data;
      } catch (error) {
        console.log(error.response);
        context.commit("resetAdmins", null);
        throw error;
      }
    },
  },
  getters: {
    userTransactionData(state) {
      return state.userTransactionData;
    },
  },
  modules: {},
});
