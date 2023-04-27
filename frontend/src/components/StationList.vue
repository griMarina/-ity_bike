<template>
  <table class="table" v-if="stations.length > 0">
    <thead>
      <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Address</th>
        <th>Capacity</th>
        <th></th>
      </tr>
    </thead>
    <station-item
      v-for="station in stations"
      :station="station"
      :key="station.id"
    ></station-item>
  </table>
  <h2 v-else>List is empty</h2>
</template>

<script>
import StationItem from "@/components/StationItem.vue";
export default {
  components: {
    StationItem,
  },
  props: {
    stations: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scoped>
.table {
  font-size: 14px;
  font-weight: normal;
  border: none;
  border-collapse: collapse;
  width: 100%;
  white-space: nowrap;
  background-color: white;
}
th {
  text-align: center;
  padding: 8px;
  border-right: 1px solid #787998;
  font-size: 14px;
}

thead th {
  background: #072052;
  color: #ffffff;
  text-transform: uppercase;
}
</style>
