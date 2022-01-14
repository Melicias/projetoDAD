<template>
  <div class="wrapper fadeInDown">
    <h4>Altere o seu perfil</h4>
    <label>Novo nome:&nbsp;</label>
    <a @click="updateUsername = true" v-show="!updateUsername">alterar</a>
    <a @click="updateUsername = false" v-show="updateUsername">cancelar</a
    ><br />
    <div v-show="updateUsername">
      <input type="text" v-model="newName" /><br />
    </div>
    <div v-if="erroName != null">
      <small>{{ showNameError }}</small
      ><br />
    </div>
    <label>Nova password:&nbsp;</label>
    <a @click="updatePassword = true" v-show="!updatePassword">alterar</a
    ><a @click="updatePassword = false" v-show="updatePassword">cancelar</a
    ><br />
    <div v-show="updatePassword">
      <input
        type="password"
        placeholder="nova password"
        v-model="newPassword"
      /><br />
    </div>
    <div v-if="erroPassword != null">
      <small>{{ showPasswordError }}</small
      ><br />
    </div>
    <div v-show="updatePassword">
      <label>&nbsp;Password Atual:</label><br />
      <input
        type="password"
        placeholder="password atual"
        v-model="confirmPassword"
      /><br />
    </div>
    <div v-if="erroConPassword != null">
      <small>{{ showConPasswordError }}</small
      ><br />
    </div>
    <button @click="closeSettings">Cancelar</button>
    <input type="submit" value="Guardar" @click="changeAdminProfile" />
  </div>
</template>

<script>
export default {
  name: 'AdminProfileSettings',
  props: {
    msg: String
  },
  data(){
    return {
      updateUsername: false,
      updatePassword: false,
      newName: null,
      newPassword: null,
      confirmPassword: null,
      erroPassword: null,
      erroName: null,
      erroConPassword: null
    }
  },
  computed:{
    showPasswordError(){
      return this.erroPassword
    },
    showNameError(){
      return this.erroName
    },
    showConPasswordError(){
      return this.erroConPassword
    }
  },
  methods: {
    closeSettings() {
      this.updateUsername = false;
      this.updatePassword = false;
      this.cleanFieldAndErrors();
      this.emitter.emit("closeAdminSettings");
    },
    changeAdminProfile() {
      console.log(this.newPassword)
      if (confirm("Tem a certeza que pretende avançar com as alterações?")) {
        this.$axios
          .patch("admin/user", {
            ...(this.newName ? { name: this.newName } : {}),
            ...(this.newPassword ? { password: this.newPassword } : {}),
            ...(this.confirmPassword ? 
            { confirmation_password: this.confirmPassword } : {}),
          })
          .then((response) => {
            console.log(response);
            this.$store.state.admin = response.data.data
            this.$toast.success("Profile updated with success!");
            this.cleanFieldAndErrors();
          })
          .catch((error) => {
            console.log(this.newPassword)
            this.$toast.error("Was not possible to update the profile!");
            this.getProSetErrors(error);
          });
      }
    },
    getProSetErrors(error) {
        console.log(error.response)
        if (error.response.data.password) {
            this.erroPassword = error.response.data.password[0];
        }
        if (error.response.data.name) {
            this.erroName = error.response.data.name[0];
        }
        if (error.response.data.confirmation_password) {
            this.erroConPassword = error.response.data.confirmation_password[0];
        }
    },
    cleanFieldAndErrors() {
      (this.newName = null),
        (this.newPassword = null),
        (this.confirmPassword = null),
      this.erroPassword = null;
      this.erroName = null;
      this.erroConPassword = null;
    },
  },
}
</script>

<style scoped>

</style>