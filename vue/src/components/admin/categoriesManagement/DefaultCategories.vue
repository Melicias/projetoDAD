<template>
  <div class="wrapper fadeInDown">
    <h3>Categorias padrão</h3>
    <loading
      v-model:active="isLoading"
      :can-cancel="false"
      :opacity="0.5"
      :on-cancel="onCancel"
      :is-full-page="fullPage"
      height="500"
    />
    <div v-if="!isLoading" style="margin-top: 40px">
      <div class="leftColumn">
        <table class="table">
          <caption>
            Todas as Categorias padrão
          </caption>

          <thead>
            <tr>
              <th v-for="column in columns" :key="column">{{ column }}</th>
            </tr>
          </thead>
          <tbody style="text-align: left">
            <tr
              v-for="(defaultCategory, index) in defaultCategories"
              :key="defaultCategory"
            >
              <td>
                <input
                  name="name"
                  v-on:keyup.enter="
                    updateDefaultCategory(
                      defaultCategories[index].id,
                      defaultCategories[index].name,
                      defaultCategories[index].type,
                      defaultCategories[index].deleted_at,
                    )
                  "
                  v-model="defaultCategories[index].name"
                />
              </td>
              <td>
                <select
                  name="type"
                  id="category"
                  v-on:change="
                    updateDefaultCategory(
                      defaultCategories[index].id,
                      defaultCategories[index].name,
                      defaultCategories[index].type,
                      defaultCategories[index].deleted_at,
                    )
                  "
                  v-model="defaultCategories[index].type"
                >
                  <option value="D">Debit</option>
                  <option value="C">Credit</option>
                </select>
              </td>
              <td>
                <button
                  class="btn btn-primary btn-xs btnMargin"
                  @click="
                    updateDefaultCategory(
                      defaultCategories[index].id,
                      defaultCategories[index].name,
                      defaultCategories[index].type,
                      defaultCategories[index].deleted_at,
                    )
                  "
                >
                  <span
                    class="glyphicon glyphicon-floppy-disk"
                    aria-hidden="true"
                  ></span>
                </button>

                <button
                  class="btn btn-xs btn-danger"
                  @click="deleteDefaultCategory(defaultCategories[index].id)"
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
        <Paginate
          :paginate="paginate"
          :page="page"
          @update="changePage"
          :key="page"
        />
      </div>
      <div class="rightColumn">
        <div style="margin-top: 50px">
          <button
            v-show="!showCreateDeafaultCategory"
            @click="openCreateDefCat"
          >
            Criar Categorias padrão
          </button>
          <div v-show="showCreateDeafaultCategory">
            <h4>Criar Categorias padrão:</h4>
            <label>Nome:&nbsp;&nbsp;</label>
            <input v-model="bodyCreateDefCat.name" />
            <div v-if="showNomeError != null">
              <small>&nbsp;&nbsp;{{ showNomeError }}</small>
            </div>
            <div>
              <label>Tipo:&nbsp;&nbsp;</label>
              <select name="type" id="category" v-model="bodyCreateDefCat.type">
                <option value="D">Debit</option>
                <option value="C">Credit</option>
              </select>
            </div>
            <div v-if="showTypeError != null">
              <small>&nbsp;&nbsp;{{ showTypeError }}</small>
            </div>
            <div>
              <button @click="closeCreateDefCat">Cancelar</button>
              <input
                type="submit"
                value="Criar 
Categorias padrão"
                @click="createDefCat"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Paginate from "../../Paginate/Paginate.vue";
import Loading from "vue-loading-overlay";
export default {
  components: { Paginate, Loading },
  name: "DefaultCategories",
  props: {
    msg: String,
  },
  data() {
    return {
      columns: ["Nome", "Tipo (Débito/Crédito)", "Ações"],
      defaultCategories: [],
      showCreateDeafaultCategory: false,
      bodyCreateDefCat: {
        name: null,
        type: null,
      },
      isLoading: false,
      errorName: null,
      errorType: null,
      page: 1,
      paginate: { lastPage: 0, total: 0, per_page: 0 },
    };
  },
  watch: {
    page: function () {
      this.getDefaultCategories();
    },
  },
  sockets: {
    updatesOnDefaultCategory(response) {
      if (
        response.id != null &&
        response.name != null &&
        response.created_at == null
      ) {
        this.$toast.show(
          `The "${response.name}:(${response.type})" default category was updated by an administrator!`,
        );
      }
      if (response.name && response.type && response.created_at) {
        this.$toast.show(
          `The state of "${response.name}:(${response.type})" default category was updated by an administrator!`,
        );
      }
      if (response.name && response.type && response.id == null) {
        this.$toast.show(
          `An administrator just created a new default category - ${response.name}:${response.type}`,
        );
      }
      this.getDefaultCategories();
    },
  },
  computed: {
    showNomeError() {
      return this.errorName;
    },
    showTypeError() {
      return this.errorType;
    },
  },
  methods: {
    getDefaultCategories() {
      this.isLoading = true;
      this.$axios
        .get("admin/defaultcategories?page=" + this.page)
        .then((response) => {
          console.log(response.data);
          this.defaultCategories = response.data.data;
          this.paginate.lastPage = response.data.last_page;
          this.paginate.total = response.data.total;
          this.paginate.per_page = response.data.per_page;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error.response);
          this.isLoading = false;
        });
    },
    updateDefaultCategory(id, name, type, state) {
      if (
        confirm(
          "Are you sure that you whant to change this category " + name + "?",
        )
      ) {
        this.$axios
          .put("/admin/defaultcategories/" + id, {
            name: name,
            type: type,
          })
          .then((response) => {
            console.log(response.data);
            this.getDefaultCategories();
            this.$toast.success("DefaulCategory was updated with success!");
            this.$socket.emit("updatesOnDefaultCategory", response.data);
          })
          .catch((error) => {
            console.log(error.response);
            this.getDefaultCategories();
            if (error.response.data.name) {
              this.$toast.error("Error. " + error.response.data.name);
            } else if (state != null) {
              this.$toast.error(
                "This Category its in state of deleted so cant be updated.",
              );
            } else {
              this.$toast.error(
                "Was not possible to update the default category.",
              );
            }
          });
      }
    },
    deleteDefaultCategory(id) {
      console.log(this.page);
      var deletedDefaultCategory = null;
      this.defaultCategories.forEach((element) => {
        if (element.id == id) {
          deletedDefaultCategory = element;
        }
      });
      if (
        confirm(
          "Are you sure that you want to delete this Default Category?",
        )
      ) {
        this.$axios
          .delete("admin/defaultcategories/" + id)
          .then((response) => {
            console.log(response);
            if (this.defaultCategories.length == 1 && this.page != 1) {
              this.page--;
            }
            this.getDefaultCategories();
            this.$toast.success(
              "Default Category deleted with success!",
            );
            this.$socket.emit(
              "updatesOnDefaultCategory",
              deletedDefaultCategory,
            );
          })
          .catch((error) => {
            console.log(error.response);
            this.$toast.error(
              "Was not possible to delete the Default Category!",
            );
          });
      }
    },
    createDefCat() {
      if (
        confirm("Are you sure that you want to create this Default Category?")
      ) {
        this.$axios
          .post("admin/defaultcategories", this.bodyCreateDefCat)
          .then((response) => {
            console.log(response);
            this.$socket.emit(
              "updatesOnDefaultCategory",
              this.bodyCreateDefCat,
            );
            this.closeCreateDefCat();
            this.getDefaultCategories();
            this.$toast.success("Default Category created with success!");
          })
          .catch((error) => {
            console.log(error);
            this.cleanErrors();
            this.getErrors(error);
            this.$toast.error(
              "Was not possible to create the Default Category!",
            );
          });
      }
    },
    openCreateDefCat() {
      this.showCreateDeafaultCategory = true;
    },
    closeCreateDefCat() {
      this.showCreateDeafaultCategory = false;
      this.bodyCreateDefCat.name = null;
      this.bodyCreateDefCat.type = null;
      this.cleanErrors();
    },
    cleanErrors() {
      this.errorName = null;
      this.errorType = null;
    },
    getErrors(error) {
      console.log(error.response);
      if (error.response.data.type) {
        this.errorType = error.response.data.type[0];
      }
      if (error.response.data.name) {
        this.errorName = error.response.data.name[0];
      }
    },
    changePage(page) {
      this.page = page;
    },
  },
  beforeMount() {
    this.getDefaultCategories();
  },
};
</script>

<style scoped>
input[name="type"] {
  width: 20px;
}
input[name="name"] {
  width: 140px;
}
.leftColumn {
  float: left;
  width: 65%;
}
.rightColumn {
  float: left;
  width: 35%;
}
.btnMargin {
  margin-right: 2px;
}
</style>
