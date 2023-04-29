<template>
  <div class="container">
    <h2 class="header">Stations</h2>
    <div class="app__btns">
      <my-input
        class="search"
        v-focus
        v-model="searchQuery"
        placeholder="Search station by name"
      ></my-input>
      <my-select v-model="selectedSort" :options="sortOptions"></my-select>
    </div>
    <pagination
      :totalPages="totalPages"
      :perPage="limit"
      :currentPage="page"
      @pagechanged="changePage"
    ></pagination>
    <station-list
      :stations="searchedAndSortedStations"
      v-if="!isLoading"
    ></station-list>
    <spinner v-else>Loading...</spinner>
  </div>
</template>

<script>
import StationList from "@/components/StationList.vue";
import Pagination from "@/components/Pagination.vue";
import Spinner from "@/components/UI/Spinner.vue";
import axios from "axios";
export default {
  components: {
    StationList,
    Pagination,
    Spinner
  },
  data() {
    return {
      stations: [],
      isLoading: false,
      selectedSort: "",
      searchQuery: "",
      page: 1,
      limit: 30,
      totalPages: 10,
      sortOptions: [
        { value: "name", name: "name" },
        { value: "address", name: "address" },
      ],
    };
  },
  methods: {
    changePage(pageNum) {
      this.page = pageNum;
    },
    async fetchStations() {
      try {
        this.isLoading = true;
        const response = await axios.get(
          "http://localhost:8888/stations/show",
          {
            params: {
              page: this.page,
              limit: this.limit,
            },
          }
        );
        this.totalPages = Math.ceil(response.data.data.entries / this.limit);
        this.stations = response.data.data.stations;
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    this.fetchStations();
  },
  computed: {
    sortedStations() {
      return [...this.stations].sort((st1, st2) =>
        st1[this.selectedSort]?.localeCompare(st2[this.selectedSort])
      );
    },
    searchedAndSortedStations() {
      return this.sortedStations.filter((st) =>
        st.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
  watch: {
    page() {
      this.fetchStations();
    },
  },
};
</script>

<style scoped>
.header {
  text-align: center;
  color: #257bc9;
  font-size: 26px;
  margin-top: 20px;
}

.search {
  width: 30%;
}
.app__btns {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
}
</style>
