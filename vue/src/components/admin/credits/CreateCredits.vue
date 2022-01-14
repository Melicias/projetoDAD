<template>
  <div>
    <h3>Fazer Créditos</h3>
  </div>
  <div style="text-align: left" class="wrapper fadeInDown">
    <label>Vcard:&nbsp;&nbsp;</label>
    <input size="6" maxlength="9" v-model="vcard" />
    <small>&nbsp;&nbsp;{{ showVcardError }}</small>
    <br /><label>Valor:&nbsp;&nbsp;</label>
    <input
      type="number"
      step="0.01"
      min="0.01"
      placeholder="0,01"
      v-model="transferValue"
    /><label>&nbsp;€</label>
    <small>&nbsp;&nbsp;{{ showValueError }}</small>
    <br /><label>Tipo de Pagamento:&nbsp;&nbsp;</label>
    <select id="paymentTypeSelect" v-model="selectPaymentType">
      <option
        v-for="paymentType in paymentTypes"
        :key="paymentType.code"
        v-bind:value="paymentType.code"
      >
        {{ paymentType.name }}
      </option>
    </select>
    <small>&nbsp;&nbsp;{{ showPaymenttypeCodeError }}</small>
    <br /><label>Refência de Pagamento:&nbsp;&nbsp;</label>
    <input v-model="paymentReference" />
    <small>&nbsp;&nbsp;{{ showPaymenttypeError }}</small>
    <br /><label>Descrição:&nbsp;&nbsp;</label><br />
    <textarea
      type="text"
      id="inputDescription"
      name="description"
      rows="2"
      cols="46"
      v-model="description"
    ></textarea>
    <br /><input
      type="submit"
      value="Efetuar transação"
      @click="requestForTranscationToServer"
    /><br />
  </div>
</template>

<script>
export default {
  name: "CreateCredits",
  props: {
    msg: String,
  },
  data() {
    return {
      selectPaymentType: null,
      paymentTypes: [],
      transferValue: null,
      vcard: null,
      paymentReference: null,
      description: null,
      errorValue: null,
      errorVcard: null,
      errorPaymenttype: null,
      errorPaymenttype_code: null,
    };
  },
  computed: {
    showVcardError() {
      return this.errorVcard;
    },
    showValueError() {
      return this.errorValue;
    },
    showPaymenttypeError() {
      return this.errorPaymenttype;
    },
    showPaymenttypeCodeError() {
      return this.errorPaymenttype_code;
    },
  },
  methods: {
    fetchPaymentTypes() {
      this.$axios
        .get("paymenttypes")
        .then((response) => {
          console.log(response.data);
          this.paymentTypes = response.data;
          this.paymentTypes.forEach((element, index) => {
            if (element.code === "VCARD") {
              this.paymentTypes.splice(index, 1);
            }
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    requestForTranscationToServer() {
      if (confirm("Tem a certeza que pretende efetuar o débito?")) {
        this.$axios
          .post("admin/transactions", {
            value: this.transferValue,
            phone_number: this.vcard,
            payment_type_code: this.selectPaymentType,
            payment_reference: this.paymentReference,
            description: this.description + "",
          })
          .then((response) => {
            this.$toast.success(
              "Money sent with success to " + response.data.Vcard + "!",
            );
            console.log(response.data);
            this.$socket.emit("newTransaction", response);
            this.clearInputs();
            this.clearErrors();
          })
          .catch((error) => {
            this.clearErrors();
            this.getErrors(error);
            this.$toast.error("Was not possible to send the money!");
          });
      }
    },
    getErrors(error) {
      console.log(error.response);
      if (error.response.data.errors.phone_number) {
        this.errorVcard = error.response.data.errors.phone_number[0];
      }
      if (error.response.data.errors.value) {
        this.errorValue = error.response.data.errors.value[0];
      }
      if (error.response.data.errors.payment_type_code) {
        this.errorPaymenttype_code =
          error.response.data.errors.payment_type_code[0];
      }
      if (error.response.data.errors.payment_reference) {
        this.errorPaymenttype = error.response.data.errors.payment_reference[0];
      }
    },
    clearInputs() {
      this.selectPaymentType = null;
      this.transferValue = null;
      this.paymentReference = null;
      this.description = null;
    },
    clearErrors() {
      (this.errorVcard = null),
        (this.errorValue = null),
        (this.errorPaymenttype = null);
      this.errorPaymenttype_code = null;
    },
  },
  mounted() {},
  beforeMount() {
    this.fetchPaymentTypes();
  },
};
</script>

<style scoped>
textarea {
  resize: none;
}
</style>
