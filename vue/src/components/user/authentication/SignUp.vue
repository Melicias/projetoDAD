<template>
  <div class="wrapper fadeInDown">
    <div id="formContent">
      <!-- Tabs Titles -->
      <div class="fadeIn first">
        <h3>Registo</h3>
      </div>

      <!-- Login Form -->
      <form>
        <input
          type="text"
          id="numero"
          class="fadeIn second"
          name="numero"
          v-model="credentials.phone_number"
          placeholder="número de telefone"
          maxlength="9"
        />
        <div v-if="this.$store.state.erroNumero != null">
          <small>{{ numberError }}</small>
        </div>
        <input
          type="text"
          id="nome"
          class="fadeIn second"
          name="nome"
          v-model="credentials.name"
          placeholder="nome"
        />
        <div v-if="this.$store.state.erroNome != null">
          <small>{{ nameError }}</small>
        </div>
        <input
          type="text"
          id="email"
          class="fadeIn second"
          name="email"
          v-model="credentials.email"
          placeholder="email"
        />
        <div v-if="this.$store.state.erroEmail != null">
          <small>{{ emailError }}</small>
        </div>
        <input
          type="password"
          id="password"
          class="fadeIn third"
          name="password"
          v-model="credentials.password"
          placeholder="password"
        />
        <div v-if="this.$store.state.erroPassword != null">
          <small>{{ passwordError }}</small>
        </div>
        <input
          type="password"
          id="confirmPassword"
          class="fadeIn third"
          name="confirmPassword"
          v-model="credentials.password_confirmation"
          placeholder="confirme a password"
        />
        <div v-if="this.$store.state.erroConfirmPassword != null">
          <small>{{ confPasswordError }}</small>
        </div>
        <input
          type="password"
          id="confirmationCode"
          class="fadeIn third"
          name="confirmationCode"
          v-model="credentials.confirmation_code"
          placeholder="código de confirmação"
          maxlength="4"
        />
        <div v-if="this.$store.state.erroCode != null">
          <small>{{ codeError }}</small>
        </div>
        <input
          type="submit"
          class="fadeIn fourth"
          v-on:click="trySignup"
          value="Criar Conta"
        />
      </form>
      <span>Já tem um vcard?</span><br />
      <a style="cursor: pointer" @click="goToLogin">Faça login</a><br />
    </div>
  </div>
</template>

<script>
export default {
  name: "SignUp",
  props: {
    msg: String,
  },
  data() {
    return {
      credentials: {
        phone_number: "",
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        confirmation_code: "",
      },
    };
  },
  computed: {
    numberError() {
      return this.$store.state.erroNumero;
    },
    nameError() {
      return this.$store.state.erroNome;
    },
    emailError() {
      return this.$store.state.erroEmail;
    },
    passwordError() {
      return this.$store.state.erroPassword;
    },
    confPasswordError() {
      return this.$store.state.erroConfirmPassword;
    },
    codeError() {
      return this.$store.state.erroCode;
    },
  },
  methods: {
    goToLogin() {
      this.$router.push("/Login");
    },
    trySignup(event) {
      event.preventDefault();
      //fazer o pedido a db
      this.$axios
        .post("vcards", this.credentials)
        .then((response) => {
          console.log(response.data.vcard);
          this.cleanErrors()
          this.$socket.emit("newUserCreated", response.data.vcard);
          if (
            confirm("Vcard created with success. Do you want to login now?")
          ) {
            this.$store
              .dispatch("login", {
                phone_number: this.credentials.phone_number,
                password: this.credentials.password,
              })
              .then(() => {
                this.$router.push({ name: "Main" });
                this.$toast.success(
                  "Welcome to Vcard Platform " +
                    this.$store.state.user.name +
                    "!",
                );
              })
              .catch(() => {
                this.$toast.error("Was not possible to do the login!");
              });
          }
        })
        .catch((error) => {
          console.log(error.response);
          this.$toast.error("Was not possible to create the vcard.");
          this.$store.dispatch("getSignUpErrors", error);
        });
    },
    cleanErrors(){
      this.$store.state.erroNumero = null
      this.$store.state.erroNome = null
      this.$store.state.erroEmail = null
      this.$store.state.erroPassword = null
      this.$store.state.erroConfirmPassword = null
      this.$store.state.erroCode = null
    }
  },
};
</script>

<style scoped>
a {
  color: #92badd;
  display: inline-block;
  text-decoration: none;
  font-weight: 400;
}

h2 {
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
  display: inline-block;
  margin: 40px 8px 10px 8px;
  color: #cccccc;
}

/* STRUCTURE */

.wrapper {
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: center;
  width: 100%;
  min-height: 100%;
  padding: 20px;
}

#formContent {
  -webkit-border-radius: 10px 10px 10px 10px;
  border-radius: 10px 10px 10px 10px;
  background: #fff;
  padding: 30px;
  width: 90%;
  max-width: 450px;
  position: relative;
  padding: 0px;
  -webkit-box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
  box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
  text-align: center;
}

#formFooter {
  background-color: #f6f6f6;
  border-top: 1px solid #dce8f1;
  padding: 25px;
  text-align: center;
  -webkit-border-radius: 0 0 10px 10px;
  border-radius: 0 0 10px 10px;
}

/* TABS */

h2.inactive {
  color: #cccccc;
}

h2.active {
  color: #0d0d0d;
  border-bottom: 2px solid #5fbae9;
}

/* FORM TYPOGRAPHY*/

input[type="button"],
input[type="submit"],
input[type="reset"] {
  background-color: #56baed;
  border: none;
  color: white;
  padding: 15px 80px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  text-transform: uppercase;
  font-size: 13px;
  -webkit-box-shadow: 0 10px 30px 0 rgba(95, 186, 233, 0.4);
  box-shadow: 0 10px 30px 0 rgba(95, 186, 233, 0.4);
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
  margin: 5px 20px 40px 20px;
  -webkit-transition: all 0.3s ease-in-out;
  -moz-transition: all 0.3s ease-in-out;
  -ms-transition: all 0.3s ease-in-out;
  -o-transition: all 0.3s ease-in-out;
  transition: all 0.3s ease-in-out;
}

input[type="button"]:hover,
input[type="submit"]:hover,
input[type="reset"]:hover {
  background-color: #39ace7;
}

input[type="button"]:active,
input[type="submit"]:active,
input[type="reset"]:active {
  -moz-transform: scale(0.95);
  -webkit-transform: scale(0.95);
  -o-transform: scale(0.95);
  -ms-transform: scale(0.95);
  transform: scale(0.95);
}

input[type="text"] {
  background-color: #f6f6f6;
  border: none;
  color: #0d0d0d;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 5px;
  width: 85%;
  border: 2px solid #f6f6f6;
  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
}
input[type="password"] {
  background-color: #f6f6f6;
  border: none;
  color: #0d0d0d;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 5px;
  width: 85%;
  border: 2px solid #f6f6f6;
  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
}

input[type="text"]:focus {
  background-color: #fff;
  border-bottom: 2px solid #5fbae9;
}
input[type="password"]:focus {
  background-color: #fff;
  border-bottom: 2px solid #5fbae9;
}

input[type="text"]:placeholder {
  color: #cccccc;
}
input[type="password"]:placeholder {
  color: #cccccc;
}

/* ANIMATIONS */

/* Simple CSS3 Fade-in-down Animation */
.fadeInDown {
  -webkit-animation-name: fadeInDown;
  animation-name: fadeInDown;
  -webkit-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
}

@-webkit-keyframes fadeInDown {
  0% {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

@keyframes fadeInDown {
  0% {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

/* Simple CSS3 Fade-in Animation */
@-webkit-keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@-moz-keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.fadeIn {
  opacity: 0;
  -webkit-animation: fadeIn ease-in 1;
  -moz-animation: fadeIn ease-in 1;
  animation: fadeIn ease-in 1;

  -webkit-animation-fill-mode: forwards;
  -moz-animation-fill-mode: forwards;
  animation-fill-mode: forwards;

  -webkit-animation-duration: 1s;
  -moz-animation-duration: 1s;
  animation-duration: 1s;
}

.fadeIn.first {
  -webkit-animation-delay: 0.4s;
  -moz-animation-delay: 0.4s;
  animation-delay: 0.4s;
}

.fadeIn.second {
  -webkit-animation-delay: 0.6s;
  -moz-animation-delay: 0.6s;
  animation-delay: 0.6s;
}

.fadeIn.third {
  -webkit-animation-delay: 0.8s;
  -moz-animation-delay: 0.8s;
  animation-delay: 0.8s;
}

.fadeIn.fourth {
  -webkit-animation-delay: 1s;
  -moz-animation-delay: 1s;
  animation-delay: 1s;
}

/* Simple CSS3 Fade-in Animation */
.underlineHover:after {
  display: block;
  left: 0;
  bottom: -10px;
  width: 0;
  height: 2px;
  background-color: #56baed;
  content: "";
  transition: width 0.2s;
}

.underlineHover:hover {
  color: #0d0d0d;
}

.underlineHover:hover:after {
  width: 100%;
}

/* OTHERS */

*:focus {
  outline: none;
}

#icon {
  width: 60%;
}
</style>
