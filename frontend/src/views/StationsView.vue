<template>
  <div class="container">
    <h1 class="header" role="header"><span>City Bike Stations</span></h1>
    <div class="app__btns">
      <my-input
        role="search"
        class="search"
        v-focus
        v-model="searchQuery"
        placeholder="Search station by name or address"
      ></my-input>
      <my-select v-model="selectedSort" :options="sortOptions"></my-select>
    </div>
    <pagination
      :totalPages="totalPages"
      :perPage="limit"
      :currentPage="page"
      @pagechanged="changePage"
    ></pagination>
    <station-table
      :stations="searchedAndSortedStations"
      v-if="!isLoading"
    ></station-table>
    <spinner v-else>Loading...</spinner>
  </div>
</template>

<script>
import StationTable from "@/components/StationTable.vue";
import Pagination from "@/components/Pagination.vue";
import axios from "axios";
export default {
  components: {
    StationTable,
    Pagination,
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
        { value: "id", name: "id" },
        { value: "name", name: "name" },
        { value: "address", name: "address" },
        { value: "capacity", name: "capacity" },
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
      return [...this.stations].sort((st1, st2) => {
        if (this.selectedSort === "id" || this.selectedSort === "capacity") {
          return st1[this.selectedSort] - st2[this.selectedSort];
        }
        return st1[this.selectedSort]?.localeCompare(st2[this.selectedSort]);
      });
    },
    searchedAndSortedStations() {
      const regex = new RegExp(this.searchQuery.trim(), "i");
      return this.sortedStations.filter((station) => {
        return regex.test(station.name) || regex.test(station.address);
      });
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
.search {
  width: 30%;
}
.app__btns {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
}
</style>
