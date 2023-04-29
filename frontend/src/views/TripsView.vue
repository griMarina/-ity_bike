<template>
  <div class="container">
    <h2 class="header">Trips</h2>
    <div class="app__btns">
      <my-input
        class="search"
        v-focus
        v-model="searchQuery"
        placeholder="Search trip ..."
      ></my-input>
      <my-select v-model="selectedSort" :options="sortOptions"></my-select>
    </div>
    <pagination
      :totalPages="totalPages"
      :perPage="limit"
      :currentPage="page"
      @pagechanged="changePage"
    ></pagination>
    <trip-table :trips="searchedAndSortedTrips" v-if="!isLoading"></trip-table>
    <spinner v-else>Loading...</spinner>
  </div>
</template>

<script>
import TripTable from "@/components/TripTable.vue";
import Pagination from "@/components/Pagination.vue";
import axios from "axios";
export default {
  components: {
    TripTable,
    Pagination,
  },
  data() {
    return {
      trips: [],
      isLoading: false,
      selectedSort: "",
      searchQuery: "",
      page: 1,
      limit: 30,
      totalPages: 10,
      sortOptions: [
        { value: "departure", name: "departure station" },
        { value: "return", name: "return station" },
      ],
    };
  },
  methods: {
    changePage(pageNum) {
      this.page = pageNum;
    },
    async fetchTrips() {
      try {
        this.isLoading = true;
        const response = await axios.get("http://localhost:8888/trips/show", {
          params: {
            page: this.page,
            limit: this.limit,
          },
        });
        this.totalPages = Math.ceil(response.data.data.entries / this.limit);
        this.trips = response.data.data.trips;
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    this.fetchTrips();
  },
  computed: {
    sortedTrips() {
      return [...this.trips].sort((trip1, trip2) =>
        trip1[this.selectedSort]?.localeCompare(trip2[this.selectedSort])
      );
    },
    searchedAndSortedTrips() {
      return this.sortedTrips.filter((trip) =>
        trip.departure.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
  watch: {
    page() {
      this.fetchTrips();
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
