<template>
  <div class="container">
    <h1 class="header"><span>City Bike Trips</span></h1>
    <div class="app__btns">
      <my-input
        class="search"
        v-focus
        v-model="searchQuery"
        placeholder="Search trip"
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
        { value: "departure_station_name", name: "departure station" },
        { value: "return_station_name", name: "return station" },
        { value: "distance", name: "distance" },
        { value: "duration", name: "duration" },
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
      return [...this.trips].sort((trip1, trip2) => {
        if (
          this.selectedSort === "duration" ||
          this.selectedSort === "distance"
        ) {
          return trip1[this.selectedSort] - trip2[this.selectedSort];
        }
        return trip1[this.selectedSort]?.localeCompare(
          trip2[this.selectedSort]
        );
      });
    },
    searchedAndSortedTrips() {
      const regex = new RegExp(this.searchQuery.trim(), "i");
      return this.sortedTrips.filter((trip) => {
        return (
          regex.test(trip.departure_station_name) ||
          regex.test(trip.return_station_name)
        );
      });
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
.search {
  width: 30%;
}
.app__btns {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
}
</style>
