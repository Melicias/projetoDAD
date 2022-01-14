<template>
  <nav aria-label="Page navigation example">
    <ul class="pagination">
      <li class="page-item" :class="{ disabled: currentPage == 1 }">
        <a
          class="page-link disabled"
          @click="currentPage > 1 ? currentPage-- : ''"
          >&#8592;</a
        >
      </li>
      <li
        v-for="page in paginate.lastPage"
        class="page-item"
        :class="{
          active: page == currentPage,
        }"
        :key="page"
      >
        <a class="page-link" @click="currentPage = page"
          >{{ page }} <span class="sr-only">(current)</span></a
        >
      </li>
      <li
        class="page-item"
        :class="{ disabled: currentPage == paginate.lastPage }"
      >
        <a
          class="page-link disabled"
          @click="currentPage < paginate.lastPage ? currentPage++ : ''"
          >&#8594;</a
        >
      </li>
    </ul>
  </nav>
</template>
<script>
export default {
  props: ["paginate", "page"],
  data() {
    return {
      currentPage: this.page,
    };
  },
  watch: {
    currentPage: function () {
      this.changePage(this.currentPage);
    },
  },
  methods: {
    changePage(page) {
      this.$emit("update", page);
    },
  },
};
</script>
