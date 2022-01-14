<template>
  <h3>Histórico de Transações</h3>
  <Search
    :searchTerm="searchTerm"
    :typeTerm="typeTerm"
    :categoryTerm="categoryTerm"
    :orderBy="orderBy"
    @update="update"
  />
  <loading
    v-model:active="isLoading"
    :can-cancel="false"
    :opacity="0.5"
    :on-cancel="onCancel"
    :is-full-page="fullPage"
    height="500"
  />
  <transaction-details
    v-if="!isLoading"
    :transactions="transactions"
    :fetch="fetchTransactions"
  ></transaction-details>
  <Paginate
    :paginate="paginate"
    :page="page"
    @update="changePage"
    :key="page"
  />
</template>

<script>
import Paginate from "../../Paginate/Paginate.vue";
import TransactionDetails from "./TransactionDetails.vue";
import Search from "../../search/Search.vue";
import Loading from "vue-loading-overlay";
export default {
  name: "UserTransactions",
  components: { TransactionDetails, Loading, Search, Paginate },
  data() {
    return {
      transactions: [],
      isLoading: false,
      fullPage: true,
      searchTerm: "",
      typeTerm: "",
      categoryTerm: "",
      orderBy: "0",
      page: 1,
      paginate: { lastPage: 0, total: 0, per_page: 0 },
    };
  },
  sockets: {
    newTransaction () {
      this.fetchTransactions()
    }
  },
  watch: {
    searchTerm: function () {
      this.changePage(1);
      this.fetchTransactions();
    },
    typeTerm: function () {
      this.changePage(1);
      this.fetchTransactions();
    },
    categoryTerm: function () {
      this.changePage(1);
      this.fetchTransactions();
    },
    orderBy: function () {
      this.changePage(1);
      this.fetchTransactions();
    },
    page: function () {
      this.fetchTransactions();
    },
  },
  methods: {
    fetchTransactions() {
      this.isLoading = true;

      this.$axios
        .get(
          "vcards/transactions?page=" +
            Number(this.page) +
            "&search=" +
            this.searchTerm +
            "&type=" +
            this.typeTerm +
            "&category=" +
            this.categoryTerm +
            "&orderBy=" +
            this.orderBy,
        )
        .then((response) => {
          this.paginate.total = response.data.total;
          this.paginate.per_page = response.data.per_page;
          this.paginate.lastPage = response.data.lastPage;
          this.transactions = response.data.data;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error);
          this.isLoading = false;
        });
    },
    changePage(page) {
      console.log(page);
      this.page = page;
    },
    update(searchTerm, typeTerm, categoryTerm, orderBy) {
      this.searchTerm = searchTerm;
      this.typeTerm = typeTerm;
      this.categoryTerm = categoryTerm;
      this.orderBy = orderBy;
    },
  },
  beforeMount() {
    this.fetchTransactions();
  },
};
</script>

<style></style>
