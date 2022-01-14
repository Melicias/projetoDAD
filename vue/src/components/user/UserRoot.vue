<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">
        <nav>
          <div class="sidebar-container">
            <div class="sidebar-logo">VCard Platform</div>
            <ul class="sidebar-navigation">
              <li>
                <router-link
                  class="nav-link"
                  :class="{ active: $route.name === 'Main' }"
                  :to="{ name: 'Main' }"
                >
                  🌱 Página Inicial
                </router-link>
              </li>
              <li>
                <router-link
                  class="nav-link"
                  :class="{ active: $route.name === 'CreateTransaction' }"
                  :to="{ name: 'CreateTransaction' }"
                >
                  <small class="fa fa-home" aria-hidden="true"> &#128179;</small
                  >Fazer Transação
                </router-link>
              </li>
              <li>
                <router-link
                  class="nav-link"
                  :class="{ active: $route.name === 'AskForTransaction' }"
                  :to="{ name: 'AskForTransaction' }"
                >
                  <small class="fa fa-home" aria-hidden="true"> &#128179;</small
                  >Pedir Dinheiro
                </router-link>
              </li>
              <li>
                <router-link
                  class="nav-link"
                  :class="{ active: $route.name === 'UserTransactions' }"
                  :to="{ name: 'UserTransactions' }"
                >
                  💸 Transações
                </router-link>
              </li>
              <li>
                <router-link
                  class="nav-link"
                  :class="{ active: $route.name === 'Categories' }"
                  :to="{ name: 'Categories' }"
                >
                  &#128203; Minhas Categorias
                </router-link>
              </li>
              <li>
                <router-link
                  class="nav-link"
                  :class="{ active: $route.name === 'UserProfile' }"
                  :to="{ name: 'UserProfile' }"
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
      <div class="col-md-8">
        <main>
          <div v-if="accept">
            <br>
            <div>
              <label>Autor do Pedido:&nbsp;&nbsp;</label>
              <span>{{ showRequestNumber }}</span><br>
              <label>Valor:&nbsp;&nbsp;</label>
              <span>{{ showRequestValue }}€</span><br>
              <label>Menssagem:&nbsp;&nbsp;</label>
              <span>{{ showRequestMessage }}</span>
            </div>
            <label>Código de Confirmação:</label><br>
            <input class="form-control sizeConfCod" type="password" maxlength="4" placeholder="****" v-model="code"><br>
            <button class="btn btn-default" @click="cancel">Rejeitar</button>
            <button class="btn btn-default" @click="confirmAccept">Confirmar</button>
          </div>
          <router-view></router-view>
        </main>
      </div>
     </div>
  </div>
</template>

<script>
export default {
  name: "UserRoot",
  components: {},
  data() {
    return {
      accept: false,
      code: null,
      number: null,
      value: null,
      description: null,
      bodyDescription: null
    };
  },
  sockets: {
    newTransaction(response) {
      if (
        response.data.payment_reference === this.$store.state.user.phone_number &&
        response.data.pair_transaction !== null
      ) {
        this.$toast.show(
          `You just received ${response.data.value}€ from ${response.data.Vcard}!!`,
        );
      } else if (
        response.data.pair_transaction === null &&
        response.data.Vcard === this.$store.state.user.phone_number
      ) {
        this.$toast.show(
          `You just received ${response.data.value}€ from ${response.data.payment_reference}!!`,
        );
      }
      this.fecthUserProfile();
    },
    askForMoney (response){
      if (response.payment_reference === this.$store.state.user.phone_number) {
        this.$toast.show(`You just received a request of money by ${response.number} in the value of ${response.value}€! 
        Check the top of your screen and make a decision!`)
        this.clickToaster(response.number, response.value, response.description)
      }
    },
    newUserState (response) {
      if (response.blocked === 1 && response.phone_number === this.$store.state.user.phone_number) {
        this.$toast.show(`Your Vcard was blocked by an administrator :(`)
        setTimeout(this.finishSession(), 3000)
      }
    },
    deleteUser(response) {
      if (response === this.$store.state.user.phone_number) {
        this.$toast.show(`Your Vcard was deleted by an administrator :(`);
        setTimeout(this.finishSession(), 3000);
      }
    },
    newMaxDebit(response) {
      if (
        response.data.phone_number === this.$store.state.user.phone_number
      ) {
        this.$toast.show(
          `Your MaxDebit was changed to ${response.data.max_debit}€ by an administrator!`,
        );
        this.fecthUserProfile();
      }
    },
  },
  computed: {
    showRequestNumber(){
      return this.number
    },
    showRequestValue(){
      return this.value
    },
    showRequestMessage(){
      return this.description
    }
  },
  methods: {
    finishSession() {
      sessionStorage.removeItem("token");
      localStorage.removeItem("loginState");
      this.$store.state.user = [];
      this.$router.push("/Login");
    },
    fecthUserProfile() {
      this.$store.dispatch("fetchUserProfile");
    },
    clickToaster(numberResponse, valueResponse, descriptionResponse){
      this.accept = true
      this.number = numberResponse
      this.value = valueResponse
      this.description = descriptionResponse == "null" ? "sem menssagem" : descriptionResponse     
    },
    transaction(){
      this.$axios
        .post("transactions", {
          phone_number: this.$store.state.user.phone_number,
          value: this.value,
          payment_type_code: "VCARD",
          payment_reference: this.number,
          ...(this.selectCategory
            ? { category_id: this.selectCategory }
            : {}),
          description: this.bodyDescription+"",
          confirmation_code: this.code,
        })
        .then((response) => {
          this.$toast.success(
            "Money sent with success to " +
              response.data.payment_reference +
              "!",
          );
          console.log(response.data);
          this.$socket.emit("newTransaction", response);
        })
        .catch((error) => {
          console.log(error.response);
          this.$toast.error("Was not possible to send the money!");
        });
    },
    cancel(){
      if (confirm("You sure that you want to cancel?")) {
        this.accept = false
      }
    },
    confirmAccept(){
      if (confirm("You sure that you want to confirm?")) {
        this.transaction()
        this.accept = false
      }
      this.accept = false
    }
  },
  beforeMount() {
    this.fecthUserProfile();
  },
  mounted() {
    this.emitter.on("finishSession", () => {
      this.finishSession();
    });
  },
};
</script>

<style>
.sizeConfCod{
  width: 340px !important;
  display: inline-block !important;
}
.col-md-3 {
  padding-right: 0 !important;
  padding-left: 0 !important;
}

@media screen and (max-width: 992px) {
  .sidebar-container {
    width: 100%;
    height: auto;
    position: relative;
  }
  .sidebar-container ul {
    float: left;
  }
  div.content {
    margin-left: 0;
  }
}

@media screen and (max-width: 400px) {
  .sidebar ul {
    text-align: center;
    float: none;
  }
}
</style>
