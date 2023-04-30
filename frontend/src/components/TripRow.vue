<template>
  <tr>
    <td
      class="station__name"
      @click="$router.push(`/stations/${trip.departure_station_id}`)"
    >
      {{ trip.departure_station_name }}
    </td>
    <td
      class="station__name"
      @click="$router.push(`/stations/${trip.return_station_id}`)"
    >
      {{ trip.return_station_name }}
    </td>
    <td>{{ trip.distance }}</td>
    <td>{{ formattedDuration }}</td>
  </tr>
</template>

<script>
export default {
  props: {
    trip: {
      type: Object,
      required: true,
    },
  },
  computed: {
    formattedDuration() {
      const min = Math.floor(this.trip.duration);
      const sec = Math.round((this.trip.duration - min) * 60);

      if (min > 0) {
        return `${min} m ${sec} s`;
      } else {
        return `${sec} s`;
      }
    },
  },
};
</script>

<style scoped>
td,
th {
  text-align: center;
  padding: 4px;
  border-right: 1px solid #d7d6d6;
  font-size: 16px;
  color: #072052;
}

.station__name:hover {
  color: #257bc9;
  cursor: pointer;
}

tr {
  height: 30px;
}

tr:nth-child(even) {
  background: #b2cfeb;
}
</style>
