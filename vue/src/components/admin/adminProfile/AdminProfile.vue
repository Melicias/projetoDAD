<template>
  <div class="wrapper fadeInDown">
    <h3>Admin Profile</h3>
    <label>Name:&nbsp;</label>
        <span>{{ $store.state.admin.name }}</span>
        <br>
    <label>Email:&nbsp;</label>
        <span>{{ $store.state.admin.email }}</span>
        <br>
    <button @click="openProfileSettings" v-show="btnProfileSettings">Defenições de Perfil</button>
    <admin-profile-settings v-show="profileSettingsActivation"></admin-profile-settings>
  </div>
</template>

<script>
import AdminProfileSettings from "./AdminProfileSettings.vue"

export default {
  name: 'AdminProfile',
  props: {
    msg: String
  },
  components: {
      AdminProfileSettings
    },
  data(){
    return {
      profileSettingsActivation: false,
      btnProfileSettings: true,
    }
  },
  computed:{
  
  },
  methods: {
    openProfileSettings(){
      this.profileSettingsActivation = true
      this.btnProfileSettings = false
      this.emitter.on("closeAdminSettings", () => {
          this.profileSettingsActivation = false
          this.btnProfileSettings = true
      })
    },
  },
}
</script>

<style scoped>

</style>