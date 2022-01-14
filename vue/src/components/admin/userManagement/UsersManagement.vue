<template>
  <div class="wrapper fadeInDown">
    <h3>Vcards</h3>
    <Search
      :searchTerm="searchTerm"
      :estadoTerm="estadoTerm"
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
    <div v-if="!isLoading">
      <table class="table">
        <caption>
          Todos os Vcards da Plataforma
        </caption>
        <thead>
          <tr>
            <th v-for="column in columns" :key="column">{{ column }}</th>
          </tr>
        </thead>
        <tbody style="text-align: left">
          <tr v-for="(vcard, index) in vcards" :key="vcard">
            <td>{{ vcards[index].phone_number }}</td>
            <td>{{ vcards[index].name }}</td>
            <td>{{ vcards[index].email }}</td>
            <td>{{ vcards[index].balance }}</td>
            <td>
              <span v-if="vcards[index].deleted_at != null">{{
                vcards[index].max_debit
              }}</span>
              <input
                v-if="vcards[index].deleted_at == null"
                v-on:keyup.enter="
                  updateMaxDebit(
                    vcards[index].phone_number,
                    vcards[index].max_debit,
                    vcards[index].name,
                  )
                "
                type="number"
                step="0.01"
                min="0.01"
                v-model="vcards[index].max_debit"
              />
            </td>
            <td>{{ vcards[index].blocked === 0 ? "Ativo" : "Bloqueado" }}</td>
            <td>
              <span v-if="vcards[index].deleted_at != null">Soft Deleted</span>
              <button
                v-if="vcards[index].deleted_at == null"
                class="btn btn-primary btn-xs btnMargin"
                @click="
                  updateMaxDebit(
                    vcards[index].phone_number,
                    vcards[index].max_debit,
                    vcards[index].name,
                  )
                "
              >
                <span
                  class="glyphicon glyphicon-floppy-disk"
                  aria-hidden="true"
                ></span>
              </button>
              <button
                v-if="
                  vcards[index].blocked === 1 &&
                  vcards[index].deleted_at == null
                "
                class="btn btn-success btn-xs btnMargin"
                @click="changeVcardState(vcards[index].phone_number, 0)"
              >
                <span
                  class="glyphicon glyphicon-ok-circle"
                  aria-hidden="true"
                ></span>
              </button>
              <button
                v-if="
                  vcards[index].blocked === 0 &&
                  vcards[index].deleted_at == null
                "
                class="btn btn-warning btn-xs btnMargin"
                @click="changeVcardState(vcards[index].phone_number, 1)"
              >
                <span
                  class="glyphicon glyphicon-ban-circle"
                  aria-hidden="true"
                ></span>
              </button>
              <button
                v-if="vcards[index].deleted_at == null"
                class="btn btn-danger btn-xs"
                @click="deleteVcard(vcards[index].phone_number)"
              >
                <span
                  class="glyphicon glyphicon-trash"
                  aria-hidden="true"
                ></span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <Paginate
    :paginate="paginate"
    :page="page"
    @update="changePage"
    :key="page"
  />
</template>
<script>
import Paginate from "../../Paginate/Paginate.vue";
import Search from "./Search.vue";
import Loading from "vue-loading-overlay";
export default {
  components: { Search, Paginate, Loading },
  name: "UsersManagement",
  props: {
    msg: String,
  },
  data() {
    return {
      columns: [
        "Número",
        "Nome",
        "Email",
        "Saldo",
        "Débito Limite",
        "Estado",
        "Ações",
      ],
      vcards: [],
      max_debit: null,
      isLoading: false,
      searchTerm: "",
      estadoTerm: "",
      orderBy: "",
      page: 1,
      paginate: { lastPage: 0, total: 0, per_page: 0 },
    };
  },
  sockets: {
    newUserCreated(response) {
      if (response) {
        this.$toast.show(
          `A vcard has just been created  with the name: ${response.name} and number: ${response.phone_number}!`,
        );
        this.fetchVcards()
      }
    },
  },
  watch: {
    page: function () {
      this.fetchVcards();
    },
    searchTerm: function () {
      this.changePage(1);
      this.fetchVcards();
    },
    estadoTerm: function () {
      this.changePage(1);
      this.fetchVcards();
    },
    orderBy: function () {
      this.changePage(1);
      this.fetchVcards();
    },
  },
  methods: {
    updateMaxDebit(vcardId, value, name) {
      if (
        confirm("Are you sure that you whant to change max debit of " + name)
      ) {
        this.$axios
          .put("/admin/vcards/" + vcardId, { max_debit: value })
          .then((response) => {
            console.log(response.data);
            this.fetchVcards();
            this.$toast.success(
              "Max debit of " + name + " was updated with success!",
            )
            this.$socket.emit("newMaxDebit", response);
          })
          .catch((error) => {
            console.log(error.response);
            this.fetchVcards();
            if (error.response.data.max_debit) {
              this.$toast.error("Error. " + error.response.data.max_debit);
            } else if (error.response.data && !error.response.data.max_debit){
              this.$toast.error("Error. " + error.response.data);
            } else {
              this.$toast.error(
                "Was not possible to update max debit of " + name,
              );
            }
          });
      }
    },
    fetchVcards() {
      this.isLoading = true;
      console.log("page changed");
      this.$axios
        .get(
          "/admin/vcards?page=" +
            Number(this.page) +
            "&search=" +
            this.searchTerm +
            "&estado=" +
            this.estadoTerm +
            "&orderBy=" +
            this.orderBy,
        )
        .then((response) => {
          this.paginate.total = response.data.total;
          this.paginate.per_page = response.data.per_page;
          this.paginate.lastPage = response.data.lastPage;
          this.vcards = response.data.data;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error.response);
          this.isLoading = false;
        });
    },
    changeVcardState(vcardId, state) {
      this.$axios
        .put("/admin/vcards/" + vcardId, { blocked: state })
        .then((response) => {
          console.log(response.data);
          this.fetchVcards();
          this.$toast.success("Vcard state was changed!");
          this.$socket.emit("newUserState", response.data);
        })
        .catch((error) => {
          console.log(error.response);
          this.$toast.error("Was not possible to change the Vcard state!");
        });
    },
    deleteVcard(vcardId) {
      if (confirm("Are you sure that you want to delete this vcard?")) {
        var deletedUserNumber = null;
        this.vcards.forEach((element) => {
          deletedUserNumber = element;
          if (element.phone_number == vcardId) {
            deletedUserNumber = element.phone_number;
          }
        });
        this.$axios
          .delete("/admin/vcards/" + vcardId)
          .then((response) => {
            console.log(response.data);
            this.fetchVcards();
            this.$toast.success("Vcard deleted with success!");
            console.log(deletedUserNumber);
            this.$socket.emit("deleteUser", deletedUserNumber);
          })
          .catch((error) => {
            console.log(error.response);
            if (error.response.data.error) {
              this.$toast.error('Error. ' + error.response.data.error)
            } else {
              this.$toast.error("Was not possible to delete the Vcard!");
            }
          });
      }
    },
    changePage(page) {
      this.page = page;
    },
    update(searchTerm, estadoTerm, orderBy) {
      this.searchTerm = searchTerm;
      this.estadoTerm = estadoTerm;
      this.orderBy = orderBy;
    },
  },
  beforeMount() {
    this.fetchVcards();
  },
};
</script>

<style scoped>
.btnMargin {
  margin-right: 2px;
}

input[type="number"] {
  width: 75px;
}
</style>
