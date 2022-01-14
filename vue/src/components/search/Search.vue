<template>
  <input
    class="form-control"
    type="text"
    placeholder="Pesquise Aqui"
    v-model="search"
    v-on:keyup.enter="SubmitEvent"
    ref="input"
  />
  <div class="config-panel"></div>
  <select class="form-control" id="selectType" v-model="type">
    <option value="">Todos</option>
    <option value="C">Crédito</option>
    <option value="D">Débito</option>
  </select>
  <select class="form-control" id="category" v-model="category">
    <option value="">Todas Categoria</option>
    <option
      v-for="category in $store.state.categories"
      :key="category.id"
      v-bind:value="category.id"
    >
      {{ category.name }}
    </option>
  </select>
  <select class="form-control" id="OrderBy" v-model="order">
    <option value="0">Ordenar por data descendente</option>
    <option value="1">Ordenar por data ascendente</option>
    <option value="2">Ordenar por valor descendente</option>
    <option value="3">Ordenar por valor ascendente</option>
  </select>
</template>
<script>
export default {
  props: ["searchTerm", "typeTerm", "categoryTerm", "orderBy"],
  data() {
    return {
      timer: undefined,
      enterKey: false,
      search: this.searchTerm,
      type: this.typeTerm,
      category: this.categoryTerm,
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
    type: function () {
      this.$emit("update", this.search, this.type, this.category, this.order);
    },
    category: function () {
      this.$emit("update", this.search, this.type, this.category, this.order);
    },
    order: function () {
      this.$emit("update", this.search, this.type, this.category, this.order);
    },
  },
  methods: {
    useEffects() {
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.$emit("update", this.search, this.type, this.category, this.order);
        this.$refs.input.blur();
      }, 2000);
    },
    SubmitEvent() {
      this.enterKey = true;
      this.$emit("update", this.search, this.type, this.category, this.order);
      this.enterKey = false;
      this.$refs.input.blur();
    },
    fetchCategories() {
      this.$store.dispatch("fetchCategories");
    },
  },
  beforeMount() {
    this.fetchCategories();
  },
};
</script>
<style scoped>
select {
  width: 33%;
  height: 32px;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.125);
}
input {
  width: 70%;
  height: 32px;
  border-radius: 3px;
  border: none;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.125);
  margin-top: 40px;
  margin-bottom: 20px;
}
.form-control{
  display: inline-block;
}
</style>
