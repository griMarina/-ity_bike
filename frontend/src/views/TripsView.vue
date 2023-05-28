<template>
  <div class="container">
    <h1 class="header" role="header"><span>City Bike Trips</span></h1>
    <div class="app__btns">
      <my-input
        role="search"
        class="search"
        v-focus
        v-model="searchQuery"
        placeholder="Search trip"
      ></my-input>
      <my-select v-model="selectedSort" :options="sortOptions"></my-select>
      <my-button class="add-btn" @click="showDialog">New trip</my-button>
    </div>
    <my-dialog v-model:show="dialogVisible">
      <trip-form @add="addTrip"></trip-form>
    </my-dialog>
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
import TripForm from "@/components/TripForm.vue";
import Pagination from "@/components/Pagination.vue";
import api from "@/services/api.js";
export default {
  components: {
    TripTable,
    TripForm,
    Pagination,
  },
  data() {
    return {
      trips: [],
      isLoading: false,
      dialogVisible: false,
      selectedSort: "",
      searchQuery: "",
      page: 1,
      limit: 30,
      totalPages: 10,
      sortOptions: [
        { value: "departure_station_name", name: "departure" },
        { value: "return_station_name", name: "return" },
        { value: "distance", name: "distance" },
        { value: "duration", name: "duration" },
      ],
    };
  },
  methods: {
    // Updates the current page
    changePage(pageNum) {
      this.page = pageNum;
    },
    showDialog() {
      this.dialogVisible = true;
    },
    async addTrip(trip) {
      try {
        // Send a POST request to the server with the new trip data
        const response = await api.post("trips/create", trip, {
          headers: {
            "Content-Type": "application/json",
          },
        });

        this.dialogVisible = false;

        // Fetch the updated trips data
        this.fetchTrips();
      } catch (error) {
        console.log(error.response.data);
      }
    },
    async fetchTrips() {
      try {
        // Show loading state
        this.isLoading = true;
        const response = await api.get("trips/show", {
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
          return trip1[this.selectedSort] - trip2[this.selectedSort]; // Sort trips in ascending order
        }
        // Sort trips in alphabetical order
        return trip1[this.selectedSort]?.localeCompare(
          trip2[this.selectedSort]
        );
      });
    },
    searchedAndSortedTrips() {
      // Create a regular expression for case-insensitive search
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
    // Fetch the trips data when the page changes
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
  margin: 19px 0 15px;
  font-size: 14px;
  height: 43px;
}

@media (max-width: 794px) {
  .app__btns {
    font-size: 12px;
    height: 38px;
  }
}
</style>
