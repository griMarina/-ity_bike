<template>
  <div class="station__info">
    <h2 class="station__header">{{ station.name }}</h2>
    <p class="station_text">
      Address: <span>{{ station.address }}</span>
    </p>
    <p class="station_text">
      Capacity: <span>{{ station.capacity }}</span>
    </p>
    <p class="station_text">
      Total number of journeys starting from the station:
      <span>{{ station.total_start }}</span>
    </p>
    <p class="station_text">
      Total number of journeys ending at the station:
      <span>{{ station.total_end }}</span>
    </p>
    <p class="station_text">
      The average distance of a journey starting from the station:
      <span>{{ station.avg_distance_start }} km</span>
    </p>
    <p class="station_text">
      The average distance of a journey ending at the station:
      <span>{{ station.avg_distance_end }} km</span>
    </p>
  </div>
  <div class="station__location" id="map"></div>
</template>
<script>
export default {
  props: {
    station: {
      type: Object,
      required: true,
    },
  },
};
</script>
<style scoped>
.station__info {
  border: 2px solid #257bc9;
  width: 450px;
  /* height: 300px; */
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
.station_text {
  margin-bottom: 10px;
}

.station_text span {
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
  .station__info {
    margin-bottom: 40px;
  }
}
</style>
