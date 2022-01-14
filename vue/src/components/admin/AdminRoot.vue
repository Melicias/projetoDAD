<template>
  <div class="container-fluid">
    <div class="row">
    <div class="col-md-2">
      <nav>
        <div class="sidebar-container">
          <div class="sidebar-logo">VCard Administration</div>
          <ul class="sidebar-navigation">
            <li>
              <router-link
                class="nav-link"
                :class="{ active: $route.name === 'Administration' }"
                :to="{ name: 'Administration' }"
              >
                &#128188; Administração
              </router-link>
            </li>
            <li>
              <router-link
                class="nav-link"
                :class="{ active: $route.name === 'CreateCredits' }"
                :to="{ name: 'CreateCredits' }"
              >
                &#128179; Fazer Créditos
              </router-link>
            </li>
            <li>
              <router-link
                class="nav-link"
                :class="{ active: $route.name === 'UsersManagement' }"
                :to="{ name: 'UsersManagement' }"
              >
                &#128106; Gestão de Utilizadores
              </router-link>
            </li>
            <li>
              <router-link
                class="nav-link"
                :class="{ active: $route.name === 'DefaultCategories' }"
                :to="{ name: 'DefaultCategories' }"
              >
                &#128203; Gestão de Categorias
              </router-link>
            </li>
            <li>
              <router-link
                class="nav-link"
                :class="{ active: $route.name === 'GlobalStatistics' }"
                :to="{ name: 'GlobalStatistics' }"
              >
                &#128200; Estatísticas
              </router-link>
            </li>
            <li>
              <router-link
                class="nav-link"
                :class="{ active: $route.name === 'AdminProfile' }"
                :to="{ name: 'AdminProfile' }"
              >
                &#129492; Perfil
              </router-link>
            </li>
            <li>
              <a @click="finishSession" style="cursor: pointer">
                &#128682; Terminar Sessão
              </a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
    <div class="col-md-10">
      <main>
        <router-view></router-view>
      </main>
    </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminRoot",
  props: {
    msg: String,
  },
  data() {
    return {};
  },
  sockets: {
    deleteAdmin(response) {
      if (response === this.$store.state.admin.email) {
        this.$toast.show(
          `Your account was deleted by another administrator :(`,
        );
        setTimeout(this.finishSession(), 3000);
      }
    },
  },
  computed: {},
  methods: {
    finishSession() {
      sessionStorage.removeItem("admintoken");
      localStorage.removeItem("adminLoginState");
      localStorage.removeItem("adminId");
      this.$store.state.admin = [];
      this.$router.push("/adminLogin");
    },
    fecthAdminProfile() {
      if (this.$store.state.admin.length === 0) {
        try {
          this.$store.dispatch("fetchAdminData");
        } catch (error) {
          console.log(error);
        }
      }
    },
    fetchAdmins() {
      if (this.$store.state.admins.length === 0) {
        try {
          let response = this.$store.dispatch("fetchAllAdmins");
          console.log(response + " Deu");
        } catch (error) {
          console.log(error);
        }
      }
    },
  },
  beforeMount() {
    this.fecthAdminProfile();
    this.fetchAdmins();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
