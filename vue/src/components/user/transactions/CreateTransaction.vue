<template>
  <div>
    <h3>Nova transação</h3>
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
      <small>&nbsp;&nbsp;{{ showValueError }}</small>
      <br /><label>Tipo de Pagamento:&nbsp;&nbsp;</label><br />
      <select
        class="form-control margin"
        id="paymentTypeSelect"
        v-model="selectPaymentType"
      >
        <option value="null">Selecione o tipo de pagamento</option>
        <option
          v-for="paymentType in paymentTypes"
          :key="paymentType.code"
          :value="paymentType.code"
        >
          {{ paymentType.name }}
        </option>
      </select>
      <div v-if="showPaymenttypeSelect">
        <small>&nbsp;&nbsp;{{ showPaymenttypeSelect }}</small>
      </div>
      <label>Refência de Pagamento:&nbsp;&nbsp;</label><br />
      <input
        placeholder="Referencia de Pagamento"
        class="form-control margin"
        v-model="paymentReference"
      />
      <small>&nbsp;&nbsp;{{ showPaymenttype }}</small>
      <br /><label>Descrição:&nbsp;&nbsp;</label><br />
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
      <label>Categoria:&nbsp;&nbsp;</label><br />
      <select
        class="form-control margin"
        id="category"
        v-model="selectCategory"
      >
        <option value="null">Selecione uma categoria.</option>
        <option
          v-for="category in $store.state.categories"
          :key="category.id"
          v-bind:value="category.id"
        >
          {{ category.name }}
        </option>
      </select>
      <label>Código de validação:&nbsp;&nbsp;</label><br />
      <input
        class="form-control margin"
        type="password"
        placeholder="****"
        size="1"
        maxlength="4"
        v-model="code"
      />
      <small>&nbsp;&nbsp;{{ showCodeError }}</small>
      <br /><input
        class="btn btn-default"
        type="submit"
        value="Efetuar transação"
        @click="requestForTranscationToServer"
      /><br />
    </div>
  </div>
</template>

<script>
export default {
  name: "CreateTransaction",
  data() {
    return {
      selectPaymentType: null,
      paymentTypes: [],
      selectCategory: null,
      transferValue: null,
      paymentReference: null,
      description: null,
      code: null,
      errorCode: null,
      errorValue: null,
      errorPaymenttype: null,
      errorPaymenttypeSelect: null,
    };
  },
  computed: {
    showCodeError() {
      return this.errorCode;
    },
    showValueError() {
      return this.errorValue;
    },
    showPaymenttype() {
      return this.errorPaymenttype;
    },
    showPaymenttypeSelect() {
      return this.errorPaymenttypeSelect;
    },
  },
  methods: {
    fetchPaymentTypes() {
      this.$axios
        .get("paymenttypes")
        .then((response) => {
          console.log(response);
          this.paymentTypes = response.data;

          this.selectPaymentType = null;
        })
        .catch((error) => {
          console.log(error.response.data);
        });
    },
    fetchCategories() {
      this.$store.dispatch("fetchCategories");
    },
    fecthUserProfile() {
      if (this.$store.state.user.length === 0) {
        this.$store.dispatch("fetchUserProfile");
      }
    },
    requestForTranscationToServer() {
      if (confirm("Tem a certeza que pretende efetuar o débito?")) {
        this.$axios
          .post("transactions", {
            phone_number: this.$store.state.user.phone_number,
            value: this.transferValue,
            payment_type_code: this.selectPaymentType,
            payment_reference: this.paymentReference,
            ...(this.selectCategory
              ? { category_id: this.selectCategory }
              : {}),
            description: this.description + "",
            confirmation_code: this.code,
          })
          .then((response) => {
            this.$toast.success(
              "Money sent with success to " +
                response.data.payment_reference +
                "!",
            );
            console.log(response.data);
            if (this.selectPaymentType === "VCARD") {
              this.$socket.emit("newTransaction", response);
            }
            this.clearInputs();
            this.clearErrors();
          })
          .catch((error) => {
            console.log(error.response);
            this.clearErrors();
            this.getErrors(error);
            this.$toast.error("Was not possible to send the money!");
          });
      }
    },
    getErrors(error) {
      if (error.response.data.message_confirmation_code) {
        this.errorCode = error.response.data.message_confirmation_code;
      }
      if (error.response.data.errors.value) {
        this.errorValue = error.response.data.errors.value[0];
      }
      if (error.response.data.errors.payment_reference) {
        this.errorPaymenttype = error.response.data.errors.payment_reference[0];
      }
      if (error.response.data.errors.payment_type_code) {
        this.errorPaymenttypeSelect = "You have to select a payment type";
      }
    },
    clearInputs() {
      this.selectPaymentType = null;
      this.selectCategory = null;
      this.transferValue = null;
      this.paymentReference = null;
      this.description = null;
      this.code = null;
    },
    clearErrors() {
      (this.errorCode = null),
        (this.errorValue = null),
        (this.errorPaymenttype = null),
        (this.errorPaymenttypeSelect = null);
    },
  },
  mounted() {},
  beforeMount() {
    this.fetchPaymentTypes();
    this.fetchCategories();
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
