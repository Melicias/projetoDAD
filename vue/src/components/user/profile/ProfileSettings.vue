<template>
  <div>
    <h4>Altere o seu perfil</h4>
    <br />
    <button
      class="btn btn-info btn-xs btnMargin"
      :class="{
        'btn-info': !updateUsername,
        'btn-warning': updateUsername,
      }"
      @click="updateUsername = !updateUsername"
    >
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
    </button>
    <label>&nbsp;Novo username</label>
    <br/>
    <div class="marginInput" v-show="updateUsername">
      <input class="form-control" type="text" v-model="newName" />
    </div>
    <button
      class="btn btn-info btn-xs btnMargin"
      :class="{
        'btn-info': !updateEmail,
        'btn-warning': updateEmail,
      }"
      @click="updateEmail = !updateEmail"
    >
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
    </button>
    <label>&nbsp;Novo email</label><br/>
    <div class="marginInput" v-show="updateEmail">
      <input class="form-control" type="text" v-model="newEmail" />
    </div>
    <button
      class="btn btn-info btn-xs btnMargin"
      :class="{
        'btn-info': !updatePassword,
        'btn-warning': updatePassword,
      }"
      @click="updatePassword = !updatePassword"
    >
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
    </button>
    <label>&nbsp;Nova password</label><br />
    <div class="marginInput" v-show="updatePassword">
      <input class="form-control"
        type="password"
        placeholder="nova password"
        v-model="newPassword"
      />
      <div v-if="erroPassword != null">
        <small>{{ showPasswordError }}</small
        ><br />
      </div>
    </div>
    <button
      class="btn btn-info btn-xs btnMargin"
      :class="{
        'btn-info': !updateCode,
        'btn-warning': updateCode,
      }"
      @click="updateCode = !updateCode"
    >
      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
    </button>
    <label>&nbsp;Novo código de validação</label><br/>
    <div class="marginInput" v-show="updateCode">
      <input class="form-control"
        type="password"
        id="inputCoder"
        name="phoneCode"
        size="1"
        maxlength="4"
        v-model="newConfirmationCode"
      />
      <div v-if="erroCode != null">
        <small>{{ showCodeError }}</small
        ><br />
      </div>
    </div>
    <div class="marginInput" v-if="updatePassword == true || updateCode == true">
      <label>&nbsp;Password Atual:</label><br />
      <input class="form-control"
        type="password"
        placeholder="password atual"
        v-model="confirmPassword"
      />
    </div>
    <div v-if="erroConfirmPassword != null">
      <small>{{ showConfirmPasswordError }}</small
      ><br />
    </div>
    <button class="btn btn-default size" @click="closeSettings">
      Cancelar
    </button>
    <input
      class="btn btn-default size"
      type="submit"
      value="Guardar"
      @click="changeProfile"
    />
  </div>
</template>

<script>
export default {
  name: "Main",
  components: {},
  data() {
    return {
      updateCode: false,
      updateUsername: false,
      updateEmail: false,
      updatePassword: false,
      newName: null,
      newEmail: null,
      newPassword: null,
      confirmPassword: null,
      newConfirmationCode: null,
      erroPassword: null,
      erroConfirmPassword: null,
      erroCode: null,
    };
  },
  computed: {
    showPasswordError() {
      return this.erroPassword;
    },
    showConfirmPasswordError() {
      return this.erroConfirmPassword;
    },
    showCodeError() {
      return this.erroCode;
    },
  },
  methods: {
    closeSettings() {
      this.updateUsername = false;
      this.updateEmail = false;
      this.updatePassword = false;
      this.updateCode = false;
      this.cleanFieldAndErrors();
      this.emitter.emit("closeSettings");
    },
    changeProfile() {
      if (confirm("Tem a certeza que pretende avançar com as alterações?")) {
        this.$axios
          .patch("vcards", {
            ...(this.newName ? { name: this.newName } : {}),
            ...(this.newEmail ? { email: this.newEmail } : {}),
            ...(this.newPassword ? { password: this.newPassword } : {}),
            ...(this.confirmPassword
              ? { confirmation_password: this.confirmPassword }
              : {}),
            ...(this.confirmationCode
              ? { confirmation_code: this.confirmationCode }
              : {}),
          })
          .then((response) => {
            console.log(response);
            this.$toast.success("Profile updated with success!");
            this.$store.dispatch("fetchUserProfile");
            this.cleanFieldAndErrors();
          })
          .catch((error) => {
            this.$toast.error("Was not possible to update the profile!");
            this.getProSetErrors(error);
          });
      }
    },
    getProSetErrors(error) {
      if (error.response.data.password) {
        this.erroPassword = error.response.data.password[0];
      }
      if (error.response.data.message) {
        this.erroConfirmPassword = error.response.data.message;
      }
      if (error.response.data.confirmation_code) {
        this.erroCode = error.response.data.confirmation_code[0];
      }
    },
    cleanFieldAndErrors() {
      (this.newName = null),
        (this.newEmail = null),
        (this.newPassword = null),
        (this.confirmPassword = null),
        (this.newConfirmationCode = null);
      this.erroPassword = null;
      this.erroConfirmPassword = null;
      this.erroCode = null;
    },
  },
};
</script>

<style scoped>
.size {
  width: 40%;
}
.marginInput{
  margin-bottom: 2px;
}
</style>
