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
import axios from "axios";
import L from "leaflet";
import StationInfo from "../components/StationInfo.vue";
import NoResults from "../components/UI/NoResults.vue";
export default {
  components: {
    StationInfo,
  },
  data() {
    return {
      station: {},
      isLoading: false,
      map: null,
      marker: null,
      errorMessage: "",
    };
  },
  methods: {
    async fetchStationInfo() {
      this.isLoading = true;
      try {
        const response = await axios.get("http://localhost:8888/station/show", {
          params: {
            id: this.$route.params.id,
          },
        });
        this.station = response.data.data;
        this.map.setView([this.station.y, this.station.x], 13);
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
    this.fetchStationInfo();

    this.map = L.map("map").setView([0, 0], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution:
        'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
    }).addTo(this.map);

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
