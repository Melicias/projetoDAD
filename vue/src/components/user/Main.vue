<template>
  <div class="payment-title">
    <h1>Vcard</h1>
  </div>
  <VcardSVG
    :Balance="$store.state.user.balance"
    :VcardNumber="$store.state.user.phone_number"
    :VcardName="username"
    :MaxBalance="$store.state.user.max_debit"
  />
  <div class="rowText centerMid">
    <h5>PiggyBank</h5>
    <h2 class="mb-0">{{ $store.state.user.piggy_bank+"€" }}</h2>
    <h6 class="text-muted font-weight-normal">Saldo</h6>
  </div>
  <div class="payment-title">
    <h1>Balance over time</h1>
  </div>
  <div class="chart">
    <Chart
      id="canvas3"
      v-if="VcardChartData"
      :chartData="VcardChartData"
      chartType="line"
      :chartLabel="chartLabels"
      :chartOptions="chartOption"
      :chartColors="chartColor"
    >
    </Chart>
  </div>
  <div class="col-sm-4 grid-margin bottom-grid-margin">
    <div class="rowText">
      <h5>Dinheiro Recebido</h5>
      <h2 class="mb-0">{{ userTransactionData.typeC }}</h2>
      <h6 class="text-muted font-weight-normal">
        Total de dinheiro já recebido.
      </h6>
    </div>
  </div>
  <div class="col-sm-4 grid-margin bottom-grid-margin">
    <div class="rowText">
      <h5>Dinheiro Gasto</h5>
      <h2 class="mb-0">{{ userTransactionData.typeD }}</h2>
      <h6 class="text-muted font-weight-normal">Total de dinheiro já gasto.</h6>
    </div>
  </div>
  <div class="col-sm-4 grid-margin bottom-grid-margin">
    <div class="rowText">
      <h5>Credito - Debitos</h5>
      <h2 class="mb-0">{{ userTransactionData.difTypeCTypeD }}</h2>
      <h6 class="text-muted font-weight-normal">
        Diferença entre credito e debitos.
      </h6>
    </div>
  </div>
</template>

<script>
import Chart from "../Stats/Charts.vue";
import VcardSVG from "./VcardSVG.vue";

export default {
  name: "Main",
  components: { Chart, VcardSVG },
  data() {
    return {
      VcardChartData: undefined,
      userTransactionData: "",
      username: null,
      chartLabels: [],
      chartOption: { title: "Vcards", xTitle: "Months", yTitle: "Balance" },
      chartColor: ["#2574a9", "#5cddff", "#ffc764", "#FF0000"],
    };
  },
  methods: {
    fetchUserTransactionData() {
      if (this.$store.state.userTransactionData.lenght === undefined) {
        this.$store
          .dispatch("fetchUserTransactionData")
          .then(() => {
            this.userTransactionData =
              this.$store.state.userTransactionData.userTransactionData;
            this.VcardChartData = JSON.stringify(
              this.$store.state.userTransactionData.lastYearData.data,
            );
            this.chartLabels =
              this.$store.state.userTransactionData.lastYearData.months;
            console.log(this.userTransactionData);
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        this.userTransactionData =
          this.$store.state.userTransactionData.userTransactionData;
        this.VcardChartData = JSON.stringify(
          this.$store.state.userTransactionData,
        );
        this.chartLabels =
          this.$store.state.userTransactionData.lastYearData.months;
      }
    },
    fecthUserProfile() {
      if (this.$store.state.user.length === 0) {
        this.$store
          .dispatch("fetchUserProfile")
          .then(() => {
            this.workUsername();
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        this.workUsername();
      }
    },
    workUsername() {
      if (this.$store.state.user.name) {
        let ArraySplit = this.$store.state.user.name.split(" ");
        let first = ArraySplit[0];
        console.log(ArraySplit);
        let last = ArraySplit[ArraySplit.length - 1];

        this.username = first + " " + last;
      }
    },
  },
  computed: {},
  beforeMount() {
    this.fetchUserTransactionData();
    this.fecthUserProfile();
  },
};
</script>
<style>
#canvas3 {
  width: 50%;
  height: auto;
  margin-top: 50px;
  cursor: pointer;
}
@media screen and (max-width: 992px) {
  #canvas3 {
    width: 150%;
    height: auto;
    position: relative;
  }
}
.rowText {
  justify-content: center;
}
.grid-margin {
  margin-top: 40px;
}
.bottom-grid-margin {
  margin-bottom: 40px;
}
.centerMid {
  margin-top: 30px;
}
</style>
