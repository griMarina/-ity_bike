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
import api from "@/services/api.js";
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
    // Updates the current page
    changePage(pageNum) {
      this.page = pageNum;
    },
    async fetchStations() {
      try {
        // Show loading state
        this.isLoading = true;

        // Send a GET request with parameters to the API to fetch stations
        const response = await api.get("stations/show", {
          params: {
            page: this.page,
            limit: this.limit,
          },
        });
        // Calculate the total number of pages
        this.totalPages = Math.ceil(response.data.data.entries / this.limit);

        // Update the stations array with the fetched stations data
        this.stations = response.data.data.stations;
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    // Fetch stations when the component is mounted
    this.fetchStations();
  },
  computed: {
    // Computed property to return sorted stations based on selected option
    sortedStations() {
      return [...this.stations].sort((st1, st2) => {
        if (this.selectedSort === "id" || this.selectedSort === "capacity") {
          return st1[this.selectedSort] - st2[this.selectedSort];
        }
        return st1[this.selectedSort]?.localeCompare(st2[this.selectedSort]);
      });
    },
    // Computed property to return stations that match search query
    searchedAndSortedStations() {
      const regex = new RegExp(this.searchQuery.trim(), "i");
      return this.sortedStations.filter((station) => {
        return regex.test(station.name) || regex.test(station.address);
      });
    },
  },
  watch: {
    // Fetch the stations data when the page changes
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
