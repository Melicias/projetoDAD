<template>
  <div>
    <h3>Pedir Dinheiro</h3>
    <div class="group">
      <label>Valor:&nbsp;&nbsp;</label><br />
      <input
        class="form-control margin"
        type="number"
        step="0.01"
        min="0.01"
        placeholder="0,01"
        v-model="transferValue"
      /><label>&nbsp;€</label>
      <div v-if="valueError">
        <small>&nbsp;&nbsp;{{ showValueError }}</small>
      </div>
      <label>Refência do Vcard:&nbsp;&nbsp;</label><br />
      <input
        placeholder="Referencia"
        class="form-control margin"
        maxlength="9"
        v-model="paymentReference"
      />
      <small>&nbsp;&nbsp;{{ showPaymentReferenceError }}</small>
      <br /><label>Mensagem:&nbsp;&nbsp;</label><br />
      <textarea
        placeholder="Lorem ipsum dolor sit amet..."
        class="form-control margin"
        type="text"
        id="inputDescription"
        name="description"
        rows="2"
        cols="46"
        v-model="description"
      ></textarea>
      <br /><input
        class="btn btn-default"
        type="submit"
        value="Efetuar Pedido"
        @click="askForMoney"
      /><br />
    </div>
  </div>
</template>

<script>
export default {
  name: "AskForTransaction",
  data() {
    return {
      selectCategory: null,
      transferValue: null,
      paymentReference: null,
      description: null,
      valueError: false,
      errorValue: null,
      errorPaymenttype: null
    };
  },
  computed: {
    showValueError() {
      return this.errorValue;
    },
    showPaymentReferenceError() {
      return this.errorPaymenttype;
    },
  },
  methods: {
    fetchCategories() {
      this.$store.dispatch("fetchCategories");
    },
    fecthUserProfile() {
      if (this.$store.state.user.length === 0) {
        this.$store.dispatch("fetchUserProfile");
      }
    },
    askForMoney() {
      if (confirm("Tem a certeza que pretende efetuar pedir dinheiro a " + this.paymentReference + "?")) {
        this.$axios
          .post("transactions", {
            phone_number: this.$store.state.user.phone_number,
            value: 0,
            payment_type_code: "VCARD",
            payment_reference: this.paymentReference,
            ...(this.selectCategory
              ? { category_id: this.selectCategory }
              : {}),
            description: this.description + "",
            confirmation_code: this.code,
          })
          .then((response) => {
              console.log(response.data);
          })
          .catch((error) => {
            console.log(error.response);
            this.clearErrors();
            if (error.response.data.errors.payment_reference || this.transferValue <= 0) {
              this.getErrors(error);
              this.$toast.error("Was not possible to ask for the money!");
            } else {
                const body = {
                    value: this.transferValue,
                    payment_reference: this.paymentReference,
                    description: this.description + "",
                    number: this.$store.state.user.phone_number
                }
                this.$socket.emit("askForMoney", body);
                this.$toast.success(
                "Money asked with success!",
                );
                this.clearInputs();
            }
          });
      }
    },
    getErrors(error) {
      if (error.response.data.errors.payment_reference) {
        this.errorPaymenttype = "This reference its not a Vcard"
      }
      if (this.transferValue <= 0) {
        this.valueError = true
        this.errorValue = "Value cant be lower than 0.01"
      } 
    },
    clearInputs() {
      this.transferValue = null;
      this.paymentReference = null;
      this.description = null;
    },
    clearErrors() {
        (this.errorPaymenttype = null)
        this.valueError = false
        this.errorValue = null
    },
  },
  mounted() {},
  beforeMount() {
    this.fecthUserProfile();
  },
};
</script>

<style scoped>
textarea {
  resize: none;
}
.form-control {
  width: 95%;
}
input {
  display: inline-block;
}
.group {
  display: inline-block;
  text-align: left;
  margin-top: 30px;
}
.margin {
  margin-bottom: 2px;
}
</style>
