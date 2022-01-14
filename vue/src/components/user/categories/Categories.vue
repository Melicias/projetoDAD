<template>
  <div class="wrapper fadeInDown">
    <h3>Minhas Categories</h3>
    <div style="margin-top: 40px">
      <loading
        v-model:active="isLoading"
        :can-cancel="false"
        :opacity="0.5"
        :on-cancel="onCancel"
        :is-full-page="fullPage"
        height="500"
      />
      <div v-if="!isLoading" class="leftColumn">
        <table class="table">
          <caption>
            As minhas categories
          </caption>
          <thead>
            <tr>
              <th v-for="column in columns" :key="column">{{ column }}</th>
            </tr>
          </thead>
          <tbody style="text-align: left">
            <tr v-for="(category, index) in categories" :key="category">
              <td>
                <input
                  class="form-control"
                  name="name"
                  v-on:keyup.enter="
                    updateCategory(
                      categories[index].id,
                      categories[index].name,
                      categories[index].type,
                    )
                  "
                  v-model="categories[index].name"
                />
              </td>
              <td>
                <select
                  class="form-control ssize"
                  id="category"
                  v-on:change="
                    updateCategory(
                      categories[index].id,
                      categories[index].name,
                      categories[index].type,
                    )
                  "
                  v-model="categories[index].type"
                >
                  <option value="D">Débito</option>
                  <option value="C">Crédito</option>
                </select>
              </td>
              <td>
                <button
                  class="btn btn-primary btn-xs btnMargin"
                  @click="
                    updateCategory(
                      categories[index].id,
                      categories[index].name,
                      categories[index].type,
                    )
                  "
                >
                  <span
                    class="glyphicon glyphicon-floppy-disk"
                    aria-hidden="true"
                  ></span>
                </button>
                <button
                  class="btn btn-xs"
                  :class="{
                    'btn-success': categories[index].deleted_at != null,
                    'btn-danger': categories[index].deleted_at == null,
                  }"
                  @click="deleteCategory(categories[index].id)"
                >
                  <span
                    class="glyphicon"
                    :class="{
                      'glyphicon-refresh': categories[index].deleted_at != null,
                      'glyphicon-trash': categories[index].deleted_at == null,
                    }"
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
            class="btn btn-default"
            v-show="!showCreateCategory"
            @click="openCreateCat"
          >
            Create Category
          </button>
          <div v-show="showCreateCategory">
            <h4>Create Category:</h4>
            <br />
            <label>Name:&nbsp;&nbsp;</label>
            <input class="form-control size" v-model="bodyCreateCat.name" />
            <div v-if="showNomeError != null">
              <small>&nbsp;&nbsp;{{ showNomeError }}</small>
            </div>
            <div class="separate">
              <label>Type:&nbsp;&nbsp;&nbsp;&nbsp;</label>
              <select
                name="type"
                class="form-control size"
                id="category"
                v-model="bodyCreateCat.type"
              >
                <option value="D">Debit</option>
                <option value="C">Credit</option>
              </select>
            </div>
            <div v-if="showTypeError != null">
              <small>&nbsp;&nbsp;{{ showTypeError }}</small>
            </div>
            <div>
              <button
                class="btn btn-default btnSizeCancel"
                @click="closeCreateCat"
              >
                Cancel
              </button>
              <input
                class="btn btn-default btnSize"
                type="submit"
                value="Create Category"
                @click="createCat"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Loading from "vue-loading-overlay";
import Paginate from "../../Paginate/Paginate.vue";
export default {
  components: { Loading, Paginate },
  name: "Categories",
  props: {
    msg: String,
  },
  data() {
    return {
      columns: ["Name", "Type", "Actions"],
      categories: [],
      showCreateCategory: false,
      bodyCreateCat: {
        vcard: null,
        name: null,
        type: null,
      },
      page: 1,
      paginate: { lastPage: 0, total: 0, per_page: 0 },
      isLoading: false,
      errorName: null,
      errorType: null,
    };
  },
  computed: {
    showNomeError() {
      return this.errorName;
    },
    showTypeError() {
      return this.errorType;
    },
  },
  watch: {
    page: function () {
      this.getCategories();
    },
  },
  methods: {
    async getCategories() {
      this.isLoading = true;
      let response = await this.$store.dispatch(
        "fetchCategoriesByPage",
        this.page,
      );
      this.paginate.total = response.total;
      this.paginate.per_page = response.per_page;
      this.paginate.lastPage = response.lastPage;
      this.categories = response.data;
      this.isLoading = false;
    },
    updateCategory(id, name, type) {
      if (confirm("Are you sure that you whant to change this category?")) {
        this.$axios
          .put("categories/" + id, {
            vcard: this.$store.state.user.phone_number,
            name: name + "",
            type: type,
          })
          .then((response) => {
            console.log(id);
            console.log(response);
            this.getCategories();
            this.$toast.success("Category was updated with success!");
          })
          .catch((error) => {
            console.log(error.response);
            this.getCategories();
            if (error.response.data.name && !error.response.data.type) {
              this.$toast.error("Error. " + error.response.data.name[0]);
            } else if (error.response.data.type && !error.response.data.name) {
              this.$toast.error("Error. " + error.response.data.type[0]);
            } else if (error.response.data.name && error.response.data.type) {
              this.$toast.error(
                "Error. " +
                  error.response.data.name[0] +
                  " and " +
                  error.response.data.type[0],
              );
            } else if (
              error.response.data &&
              !error.response.data.name &&
              !error.response.data.type
            ) {
              this.$toast.error("Error. " + error.response.data);
            } else {
              this.$toast.error("Was not possible to update de Category.");
            }
          });
      }
    },
    deleteCategory(id) {
      if (
        confirm(
          "Are you sure that you want to change the state of this Category?",
        )
      ) {
        this.$axios
          .delete("categories/" + id)
          .then((response) => {
            console.log(response);
            if (this.categories.length == 1 && this.page != 1) {
              this.page--;
            }
            this.getCategories();
            this.$toast.success("Category state modified with success!");
          })
          .catch((error) => {
            console.log(error.response);
            this.$toast.error(
              "Was not possible to modify the state of the Category!",
            );
          });
      }
    },
    createCat() {
      if (confirm("Are you sure that you want to create this Category?")) {
        this.bodyCreateCat.vcard = this.$store.state.user.phone_number;
        this.$axios
          .post("categories", this.bodyCreateCat)
          .then((response) => {
            console.log(response);
            this.closeCreateCat();
            this.getCategories();
            this.$toast.success("Category created with success!");
          })
          .catch((error) => {
            console.log(error);
            this.cleanErrors();
            this.getErrors(error);
            this.$toast.error("Was not possible to create the Category!");
          });
      }
    },
    openCreateCat() {
      this.showCreateCategory = true;
    },
    closeCreateCat() {
      this.showCreateCategory = false;
      this.bodyCreateCat.name = null;
      this.bodyCreateCat.type = null;
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
      console.log(page);
      this.page = page;
    },
  },
  beforeMount() {
    this.getCategories();
  },
};
</script>

<style scoped>
select[name="type"] {
  width: 70%;
}
input[name="name"] {
  width: 90%;
}
input[name="name"] {
  width: 90%;
}
.leftColumn {
  float: left;
  width: 55%;
}
.rightColumn {
  float: left;
  width: 45%;
}
.btnMargin {
  margin-right: 2px;
}
.size {
  display: inline-block;
  width: 70%;
}
.btnSize {
  width: 140px;
  margin-top: 5px;
}
.btnSizeCancel {
  width: 100px;
  margin-top: 5px;
}
.separate {
  margin-top: 2px;
}
.ssize {
  width: 90%;
}
</style>
