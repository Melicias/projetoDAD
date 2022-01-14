<template>
  <div>
    <h1>404 Error</h1>
    <p>The resource could not be found.</p>
    <a @click="redirectToMain">Go to home page!</a>
  </div>
</template>

<script>
export default {
  name: 'notFound',
  data(){
    return {

    }
  },
  methods: {
    redirectToMain(){
      console.log(this.$store.state.user)
      console.log(this.$store.state.admin)
      if (localStorage.getItem("loginState") && this.$store.state.user && sessionStorage.getItem("token")) {
        this.$router.push({name: 'Main'})
      } else {
        this.$router.push({name: 'Administration'})
      }
    },
  },
}
</script>

<style scoped>

</style>