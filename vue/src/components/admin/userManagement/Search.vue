<template>
  <input
    type="text"
    placeholder="Pesquise Aqui"
    v-model="search"
    v-on:keyup.enter="SubmitEvent"
    ref="input"
  />
  <div class="config-panel"></div>
  <select id="selectType" v-model="estado">
    <option value="">Todos</option>
    <option value="A">Ativos</option>
    <option value="B">Bloqueados</option>
  </select>
  <select id="OrderBy" v-model="order">
    <option value="">Sem ordenação</option>
    <option value="0">Ordenar por saldo descendente</option>
    <option value="1">Ordenar por saldo ascendente</option>
    <option value="2">Ordenar por Débito Limite descendente</option>
    <option value="3">Ordenar por Débito Limite ascendente</option>
  </select>
</template>
<script>
export default {
  props: ["searchTerm", "estadoTerm", "orderBy"],
  data() {
    return {
      timer: undefined,
      enterKey: false,
      search: this.searchTerm,
      estado: this.estadoTerm,
      order: this.orderBy,
      page: 1,
    };
  },
  watch: {
    search: function () {
      //skip the fetch if user press enter key
      if (this.enterKey) {
        return;
      }
      this.useEffects();
    },
    estado: function () {
      this.$emit("update", this.search, this.estado, this.order);
    },
    order: function () {
      this.$emit("update", this.search, this.estado, this.order);
    },
  },
  methods: {
    useEffects() {
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.$emit("update", this.search, this.estado, this.order);
        this.$refs.input.blur();
      }, 2000);
    },
    SubmitEvent() {
      this.enterKey = true;
      this.$emit("update", this.search, this.estado, this.order);
      this.enterKey = false;
      this.$refs.input.blur();
    },
  },
  beforeMount() {},
};
</script>
<style scoped>
select {
  width: 50%;
  height: 32px;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.125);
  margin-bottom: 16px;
}

input {
  width: 50%;
  height: 32px;
  border-radius: 3px;
  border: none;
  margin-bottom: 16px;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
