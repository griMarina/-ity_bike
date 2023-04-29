<template>
  <div class="container">
    <div  class="station" v-show="!isLoading">
      <div class="station__info">
        <h2 class="station__header">{{ station.name }}</h2>
        <div>
          Address: <span>{{ station.address }}</span>
        </div>
        <div>
          Capacity: <span>{{ station.capacity }}</span>
        </div>
        <div>
          Total number of journeys starting from the station:
          <span>{{ station.start_trips }}</span>
        </div>
        <div>
          Total number of journeys ending at the station:
          <span>{{ station.end_trips }}</span>
        </div>
      </div>
      <div class="station__location" id="map"></div>
    </div>
    <spinner v-show="isLoading"></spinner>
  </div>
</template>

<script>
import axios from "axios";
import L from "leaflet";
import Spinner from "../components/UI/Spinner.vue";
export default {
  components: {
    Spinner
  },
  data() {
    return {
      station: {},
      isLoading: false,
      map: null,
      marker: null,
      stationX: null,
      stationY: null,
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
        console.log(e);
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
  },
};
</script>

<style scoped>
.station {
  display: flex;
  justify-content: space-around;
  margin-top: 50px;
}
.station__info {
  border: 2px solid #257bc9;
  width: 450px;
  height: 300px;
  background-color: rgb(38 124 201 / 88%);
  border-radius: 15px;
  padding: 20px;
  color: #ffffff;
  font-size: 18px;
  margin: 0 auto;
}

.station__header {
  margin-bottom: 30px;
  text-align: center;
}

.station__info div {
  margin-bottom: 10px;
}

.station__info span {
  font-weight: 600;
}
.station__location {
  width: 550px;
  border: 2px solid #257bc9;
  border-radius: 15px;
  height: 450px;
  margin: 0 auto;
}
@media (max-width: 1222px) {
  .station {
    flex-direction: column;
    justify-content: center;
  }

  .station__info {
    margin-bottom: 40px;
  }
}
</style>
