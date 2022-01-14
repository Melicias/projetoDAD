<template>
  <table
    id="dtBasicExample"
    class="table table-striped table-bordered table-sm table-hover"
    cellspacing="0"
    width="100%"
  >
    <caption>
      Lista do total de tranferências efetuadas pelo presente Vcard
    </caption>
    <thead>
      <tr v-if="transactions != 0">
        <th style="text-align: center" v-for="column in columns" :key="column">
          {{ column }}
        </th>
      </tr>
    </thead>
    <span v-if="transactions == 0">There's no transcation to show!</span>
    <tbody v-for="transaction in transactionsOrder" :key="transaction.value">
      <tr>
        <td>
          <button
            class="btn btn-xs"
            aria-hidden="true"
            :class="{
              'btn-info': !transaction.detail,
              'btn-warning': transaction.detail,
            }"
            @click="showTransactionDetail(transaction)"
          >
            <span
              class="glyphicon glyphicon-chevron-down"
              v-show="!transaction.detail"
            ></span>
            <span
              class="glyphicon glyphicon-chevron-up"
              v-show="transaction.detail"
            ></span>
          </button>
        </td>
        <td>{{ transaction.type === "D" ? "Débito" : "Crédito" }}</td>
        <td>
          {{
            transaction.type === "D"
              ? "- " + transaction.value
              : "+ " + transaction.value
          }}
        </td>
        <td>
          <select
            class="form-control"
            id="category"
            v-on:change="updateTransaction(transaction)"
            v-model="transaction.category"
          >
            <option value="null">Sem Categoria</option>
            <option
              v-for="category in $store.state.categories"
              :key="category.id"
              :value="category"
            >
              {{ category.name }}
            </option>
          </select>
        </td>
        <td>{{ transaction.datetime }}</td>
      </tr>
      <tr>
        <td v-if="transaction.detail">
          <table
            class="table table-striped table-bordered table-sm"
            cellspacing="0"
          >
            <tbody>
              <tr>
                <td>Description:</td>
                <td v-show="transaction.detail">
                  <input
                    maxlength="255"
                    class="form-control"
                    style="text-align: center"
                    v-on:keyup.enter="updateTransaction(transaction)"
                    v-model="transaction.description"
                  />
                  <small class="limiter">{{
                    charactersLeft(transaction.description)
                  }}</small>
                </td>
              </tr>
              <tr>
                <td>Payment type:</td>
                <td v-show="transaction.detail">
                  {{ transaction.payment_type }}
                </td>
              </tr>
              <tr>
                <td>Payment reference:</td>
                <td v-show="transaction.detail">
                  {{ transaction.payment_reference }}
                </td>
              </tr>
              <tr>
                <td>Old balance:</td>
                <td v-show="transaction.detail">
                  {{ transaction.old_balance }}
                </td>
              </tr>
              <tr>
                <td>New balance:</td>
                <td v-show="transaction.detail">
                  {{ transaction.new_balance }}
                </td>
              </tr>
              <tr>
                <td>Save description:</td>
                <td v-show="transaction.detail">
                  <button
                    class="btn btn-primary btn-xs btnMargin"
                    @click="updateTransaction(transaction)"
                  >
                    <span
                      class="glyphicon glyphicon-floppy-disk"
                      aria-hidden="true"
                    ></span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: "TransactionDetails",
  props: ["transactions", "fetch"],
  component: {},
  methods: {
    showTransactionDetail(transaction) {
      transaction.detail ^= 1;
    },
    charactersLeft(descrip) {
      var char = 0;
      if (descrip) {
        char = descrip.length;
      }
      var limit = 255;

      return limit - char + " / " + limit + " Caracteres em falta";
    },
    updateTransaction(transaction) {
      if (confirm("Are you sure that you whant to update this transaction?")) {
        //console.log(error.response);
        this.$axios
          .patch("transactions/" + transaction.id, {
            ...(transaction.category
              ? { category_id: transaction.category.id }
              : null),
            ...(transaction.description
              ? { description: transaction.description }
              : null),
          })
          .then((response) => {
            console.log(response.data);
            this.fetch();
            this.$toast.success("Transaction was updated with success!");
          })
          .catch((error) => {
            console.log(error.response);
            /* if (error.response.data.errors.description) {
            this.$toast.error("Error. " + error.response.data.errors.description)
          } else { */
            this.$toast.error("Was not possible to update the transaction.");
            //}
          });
      }
    },
  },
  computed: {},
  data() {
    return {
      columns: ["Details", "Transaction", "Value", "Category", "Date"],
      transactionsOrder: this.transactions,
      stateUpdateTransaction: false,
    };
  },
  mounted() {},
};
</script>

<style scoped>
.form-control {
  height: 31px;
}
</style>
