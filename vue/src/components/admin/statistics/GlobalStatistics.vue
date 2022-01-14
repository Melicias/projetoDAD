<template>
  <h3>Estatísticas da plataforma</h3>
  <div class="row">
    <div class="Charts">
      <div class="col-md-6">
        <Chart
          id="canvas1"
          v-if="chartData.dataC"
          :chartData="chartData.dataC"
          chartType="doughnut"
          :chartLabel="chartLabels"
          :chartColors="chartColor"
          :chartOptions="chartOption.optionC"
        >
        </Chart>
      </div>
      <div class="col-md-6">
        <Chart
          id="canvas2"
          v-if="chartData.dataD"
          :chartData="chartData.dataD"
          chartType="doughnut"
          :chartLabel="chartLabels"
          :chartColors="chartColor"
          :chartOptions="chartOption.optionD"
        >
        </Chart>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="datas">
      <div class="row">
        <div class="col-sm-4 grid-margin align">
          <div class="rowText">
            <h5>Dinheiro Total da Plataforma</h5>
            <h2 class="mb-0">{{ transactionData.total_balance }} €</h2>
            <h6 class="text-muted font-weight-normal">
              Somatorio de todos os balance da plataforma.
            </h6>
          </div>
        </div>
        <div class="col-sm-4 grid-margin">
          <div class="rowText">
            <h5>Transações na Plataforma</h5>
            <h2 class="mb-0">{{ transactionData.total_transaction }}</h2>
            <h6 class="text-muted font-weight-normal">
              Número total de transações na plataforma
            </h6>
          </div>
        </div>
        <div class="col-sm-4 grid-margin">
          <div class="rowText">
            <h5>Vcards na Plataforma</h5>
            <h2 class="mb-0">{{ transactionData.total_vcards }}</h2>
            <h6 class="text-muted font-weight-normal">
              Número de Vcards ativos na plataforma
            </h6>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4 grid-margin bottom-grid-margin">
          <div class="rowText">
            <h5>Vcards não Bloqueados</h5>
            <h2 class="mb-0">{{ transactionData.vcards_not_blocked }}</h2>
            <h6 class="text-muted font-weight-normal">
              Número de Vcards não bloqueados na plataforma.
            </h6>
          </div>
        </div>
        <div class="col-sm-4 grid-margin bottom-grid-margin">
          <div class="rowText">
            <h5>Vcards Bloqueados</h5>
            <h2 class="mb-0">{{ transactionData.vcards_blocked }}</h2>
            <h6 class="text-muted font-weight-normal">
              Número de Vcards bloqueados na plataforma.
            </h6>
          </div>
        </div>
        <div class="col-sm-4 grid-margin bottom-grid-margin">
          <div class="rowText">
            <h5>Vcards Apagados</h5>
            <h2 class="mb-0">{{ transactionData.deleted_vcards }}</h2>
            <h6 class="text-muted font-weight-normal">
              Número de Vcards Apagados na plataforma.
            </h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from "../../Stats/Charts.vue";
export default {
  name: "Statistics",
  components: { Chart },
  props: {
    msg: String,
  },
  data() {
    return {
      transactionData: [],
      chartData: { dataC: undefined, dataD: undefined },
      chartLabels: [],
      chartColor: [
        "#e91c2b",
        "#f48a1d",
        "#eacf2a",
        "#0aa350",
        "#44a0e3",
        "#d90b91",
        "#6d330d",
        "#101115",
        "#808000",
      ],
      chartOption: {
        optionC: { title: "Transações (Credito)" },
        optionD: { title: "Transações (Debito)" },
      },
    };
  },
  computed: {},
  methods: {
    fetchUserTransactionData() {
      console.log(this.$store.state.adminTransactionsData);
      if (this.$store.state.adminTransactionsData.lenght === undefined) {
        this.$store
          .dispatch("fetchAdminTransactionsData")
          .then(() => {
            this.transactionData = this.$store.state.adminTransactionsData;
            this.chartData.dataC =
              this.transactionData.transactionCount.transactionsCountByC;
            this.chartData.dataD =
              this.transactionData.transactionCount.transactionsCountByD;
            this.chartLabels =
              this.transactionData.transactionCount.transactionType;
            console.log(this.chartData);
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        this.transactionData = this.$store.state.adminTransactionsData;
        this.chartData.dataC =
          this.transactionData.transactionCount.transactionsCountByC;
        this.chartData.dataD =
          this.transactionData.transactionCount.transactionsCountByD;
        this.chartLabels =
          this.transactionData.transactionCount.transactionType;
      }
    },
  },
  beforeMount() {
    this.fetchUserTransactionData();
  },
};
</script>
<style>
#canvas1,
#canvas2 {
  width: 150%;
  height: 150%;
  margin-top: 50px;
  cursor: pointer;
}
@media screen and (max-width: 992px) {
  #canvas1,
  #canvas2 {
    width: 150%;
    height: auto;
    position: relative;
  }
}

@media screen and (max-width: 400px) {
}
.rowText {
  justify-content: center;
}
.align {
  text-align: center;
}
</style>
