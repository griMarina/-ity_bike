<template>
  <ul class="pagination">
    <li>
      <button
        role="button-first"
        class="pagination__btn"
        type="button"
        @click="onClickFirstPage"
        :disabled="isInFirstPage"
      >
        &laquo;
      </button>
    </li>
    <li>
      <button
        role="button-previous"
        class="pagination__btn"
        @click="onClickPreviousPage"
        :disabled="isInFirstPage"
      >
        Prev
      </button>
    </li>
    <li v-for="page in pages">
      <button
        class="pagination__btn"
        @click="onClickPage(page.name)"
        :disabled="page.isDisabled"
        :class="{ active: isPageActive(page.name) }"
      >
        {{ page.name }}
      </button>
    </li>
    <li>
      <button
        role="button-next"
        class="pagination__btn"
        @click="onClickNextPage"
        :disabled="isInLastPage"
      >
        Next
      </button>
    </li>
    <li>
      <button
        role="button-last"
        class="pagination__btn"
        @click="onClickLastPage"
        :disabled="isInLastPage"
      >
        &raquo;
      </button>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    maxVisibleButtons: {
      type: Number,
      required: false,
      default: 3,
    },
    totalPages: {
      type: Number,
      required: true,
    },
    perPage: {
      type: Number,
      required: true,
    },
    currentPage: {
      type: Number,
      required: true,
    },
  },
  methods: {
    isPageActive(page) {
      return this.currentPage === page;
    },
    onClickFirstPage() {
      this.$emit("pagechanged", 1);
    },
    onClickPreviousPage() {
      this.$emit("pagechanged", this.currentPage - 1);
    },
    onClickPage(page) {
      this.$emit("pagechanged", page);
    },
    onClickNextPage() {
      this.$emit("pagechanged", this.currentPage + 1);
    },
    onClickLastPage() {
      this.$emit("pagechanged", this.totalPages);
    },
  },
  computed: {
    startPage() {
      if (this.currentPage === 1) {
        return 1;
      }
      if (this.currentPage === this.totalPages) {
        return this.totalPages - this.maxVisibleButtons;
      }

      return this.currentPage - 1;
    },
    isInFirstPage() {
      return this.currentPage === 1;
    },
    isInLastPage() {
      return this.currentPage === this.totalPages;
    },
    pages() {
      const range = [];

      for (
        let i = this.startPage;
        i <=
        Math.min(this.startPage + this.maxVisibleButtons - 1, this.totalPages);
        i++
      ) {
        range.push({
          name: i,
          isDisabled: i === this.currentPage,
        });
      }

      return range;
    },
  },
};
</script>

<style>
.pagination {
  list-style-type: none;
  display: flex;
  margin-top: 15px;
}

.pagination__btn {
  border: 1px solid #58595a;
  padding: 4px;
  background-color: white;
  color: #47494d;
  cursor: pointer;
  min-width: 36px;
  text-align: center;
  margin-bottom: 20px;
  border-radius: 5px;
}

.pagination__btn:hover {
  cursor: pointer;
  background-color: #257bc9;
  color: white;
}

.active {
  background-color: #257bc9;
  color: white;
}
</style>
