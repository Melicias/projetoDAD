<template>
  <div class="container">
    <h3>Administração</h3>
    <div id="divCriarAdmin">
      <button v-show="!showCreateAdmin" @click="openCreateAdmin">
        Criar Administrador
      </button>
      <div v-show="showCreateAdmin">
        <h4>Criar Administrador:</h4>
        <label>Nome:&nbsp;&nbsp;</label>
        <input v-model="bodyCreateAdmin.name" />
        <small>&nbsp;&nbsp;{{ showNomeError }}</small>
        <br /><label>Email:&nbsp;&nbsp;</label>
        <input v-model="bodyCreateAdmin.email" />
        <small>&nbsp;&nbsp;{{ showEmailError }}</small>
        <br /><label>Password:&nbsp;&nbsp;</label>
        <input type="password" v-model="bodyCreateAdmin.password" />
        <small>&nbsp;&nbsp;{{ showPasswordError }}</small>
        <br /><label>Confirme a password:&nbsp;&nbsp;</label>
        <input type="password" v-model="bodyCreateAdmin.password_confirmation" />
        <br /><button @click="closeCreateAdmin">Cancelar</button>
        <input type="submit" value="Criar Administrador" @click="createAdmin" />
      </div>
    </div>
    <loading
      v-model:active="isLoading"
      :can-cancel="false"
      :opacity="0.5"
      :on-cancel="onCancel"
      :is-full-page="fullPage"
      height="500"
    />
    <div v-if="!isLoading">
      <table class="table">
        <caption>
          Administradores da Vcard Platform
        </caption>
        <thead>
          <tr>
            <th v-for="column in columns" :key="column">{{ column }}</th>
          </tr>
        </thead>
        <tbody style="text-align: left">
          <tr v-for="(admin, index) in $store.state.admins" :key="admin">
            <td>{{ $store.state.admins[index].name }}</td>
            <td>{{ $store.state.admins[index].email }}</td>
            <td>
              <button
                class="btn btn-danger btn-xs"
                @click="deleteAdmin($store.state.admins[index].id)"
              >
                <span
                  class="glyphicon glyphicon-trash"
                  aria-hidden="true"
                ></span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import Loading from "vue-loading-overlay";
export default {
  components: { Loading },
  name: "Administration",
  props: {
    msg: String,
  },
  data() {
    return {
      columns: ["Nome", "Email", "Apagar"],
      showCreateAdmin: false,
      bodyCreateAdmin: {
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
      },
      isLoading: false,
      errorName: null,
      errorEmail: null,
      errorPassword: null,
    };
  },
  sockets: {
    newAdminCreated(response) {
      if (response) {
        this.$toast.show(
          `An admin has just been created by another administrator (name: ${response.name} and email: ${response.email})!`,
        );
        this.fetchAdmins() 
      }
    },
  },
  computed: {
    showNomeError() {
      return this.errorName;
    },
    showEmailError() {
      return this.errorEmail;
    },
    showPasswordError() {
      return this.errorPassword;
    },
  },
  methods: {
    openCreateAdmin() {
      this.showCreateAdmin = true;
    },
    closeCreateAdmin() {
      this.showCreateAdmin = false;
      this.bodyCreateAdmin.name = null;
      this.bodyCreateAdmin.email = null;
      this.bodyCreateAdmin.password = null;
      this.bodyCreateAdmin.password_confirmation = null;
      this.cleanErrors();
    },
    cleanErrors() {
      this.errorName = null;
      this.errorEmail = null;
      this.errorPassword = null;
    },
    async fetchAdmins() {
      try {
        this.isLoading = true;
        await this.$store.dispatch("fetchAllAdmins");
        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
        console.log(error);
      }
    },
    createAdmin() {
      if (confirm("Are you sure that you want to create this administrator?")) {
        this.$axios
          .post("admin/user", this.bodyCreateAdmin)
          .then((response) => {
            console.log(response.data);
            this.closeCreateAdmin();
            this.fetchAdmins();
            this.$toast.success("Administrator created with success!");
            this.$socket.emit("newAdminCreated", response.data.data);
          })
          .catch((error) => {
            console.log(error.response);
            this.cleanErrors();
            this.getErrors(error);
            this.$toast.error("Was not possible to create the Administrator!");
          });
      }
    },
    deleteAdmin(adminId) {
      if (confirm("Are you sure that you want to delete this administrator?")) {
        var deletedAdminEmail = null;
        this.$store.state.admins.forEach((element) => {
          if (element.id == adminId) {
            deletedAdminEmail = element.email;
          }
        });
        this.$axios
          .delete("admin/user/" + adminId)
          .then((response) => {
            console.log(response);
            this.fetchAdmins();
            this.$toast.success("Administrator deleted with success!");
            this.$socket.emit("deleteAdmin", deletedAdminEmail);
          })
          .catch((error) => {
            console.log(error.response);
            this.$toast.error("Was not possible to delete the Administrator!");
          });
      }
    },
    getErrors(error) {
      console.log(error.response);
      if (error.response.data.name) {
        this.errorName = error.response.data.name[0];
      }
      if (error.response.data.email) {
        this.errorEmail = error.response.data.email[0];
      }
      if (error.response.data.password) {
        this.errorPassword = error.response.data.password[0];
      }
    },
  },
  beforeMount() {
    this.fetchAdmins();
  },
};
</script>

<style scoped>
#divCriarAdmin {
  margin-top: 40px;
  margin-bottom: 40px;
  text-align: left;
}
</style>
