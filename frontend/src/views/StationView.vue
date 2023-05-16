<template>
  <div class="container">
    <div class="station" v-show="!isLoading">
      <station-info :station="station" v-if="!errorMessage"></station-info>
      <no-results v-else>
        {{ errorMessage }}
      </no-results>
    </div>
    <spinner v-show="isLoading"></spinner>
  </div>
</template>

<script>
import L from "leaflet";
import StationInfo from "@/components/StationInfo.vue";
import NoResults from "@/components/UI/NoResults.vue";
import api from "@/services/api.js";
export default {
  components: {
    StationInfo,
  },
  data() {
    return {
      station: {},
      isLoading: false,
      map: null, // Leaflet map instance
      marker: null, // Leaflet marker instance
      errorMessage: "",
    };
  },
  methods: {
    async fetchStationInfo() {
      // Show loading state
      this.isLoading = true;
      try {
        // Fetch station info based on the ID from the route params
        const response = await api.get("station/show", {
          params: {
            id: this.$route.params.id,
          },
        });

        this.station = response.data.data;

        // Set the map view to the station's location
        this.map.setView([this.station.y, this.station.x], 13);
        // Add a marker for the station on the map
        this.marker = L.marker([this.station.y, this.station.x]).addTo(
          this.map
        );
      } catch (error) {
        this.errorMessage = error.response.data.reason;
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    // Fetch the station info when the component is mounted
    this.fetchStationInfo();

    // Create a Leaflet map instance and set the initial view
    this.map = L.map("map").setView([0, 0], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution:
        'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
    }).addTo(this.map); // Add the OpenStreetMap tile layer to the map

    // Assign the map instance to the global window object for debugging
    window.map = this.map;
  },
};
</script>

<style scoped>
.station {
  display: flex;
  justify-content: space-around;
  margin-top: 50px;
}

@media (max-width: 1222px) {
  .station {
    flex-direction: column;
    justify-content: center;
  }
}
</style>
