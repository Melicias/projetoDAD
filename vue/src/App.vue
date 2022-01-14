<template>
  <router-view></router-view>
</template>

<script>

export default {
  name: "App",
  components: {

  },
  data() {
    return {};
  },
  methods: {

  },
  beforeMount() {
    this.$store.state.hasToken = sessionStorage.getItem("token") != null ? true : false;
  },
  mounted(){
    
  }
};
</script>

<style>
@import "./assets/css/sidebar.css";
small{
  color: #cf0202;
}
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
