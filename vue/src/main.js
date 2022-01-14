import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import axios from "axios";
import bootstrap from "bootstrap/dist/css/bootstrap.min.css";
import mitt from "mitt";
import Toaster from "@meforma/vue-toaster";
import VueSocketIO from "vue-3-socket.io";

let toastOptions = {
  position: "top",
  timeout: 3000,
  pauseOnHover: true,
  queue: true,
  dismissible: true,
};

const socketIO = new VueSocketIO({
  debug: true,
  connection: "http://localhost:8080",
});

const emitter = mitt();
const app = createApp(App)
  .use(store)
  .use(bootstrap)
  .use(router)
  .use(Toaster, toastOptions)
  .use(socketIO);
//const apiDomain = process.env.VUE_APP_API_DOMAIN;
//const wsConnection = process.env.VUE_APP_WS_CONNECTION;

/* const socketIO = new VueSocketIO({
  connection: wsConnection,
}); */

app.config.globalProperties.emitter = emitter;

axios.defaults.baseURL = "http://127.0.0.1:8000/api";
app.config.globalProperties.$serverUrl = "http://127.0.0.1:8000/";
axios.defaults.headers.common["Authorization"] = `Bearer ${
  sessionStorage.token ? sessionStorage.token : sessionStorage.admintoken
}`;
app.config.globalProperties.$axios = axios;

app.mount("#app");
