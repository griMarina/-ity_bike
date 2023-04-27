<template>
  <tr>
    <td>{{ station.id }}</td>
    <td>{{ station.name }}</td>
    <td>{{ station.address }}</td>
    <td>{{ station.capacity }}</td>
    <td >
      <my-button @click="$router.push(`/stations/${station.id}`)"
        >View</my-button
      >
    </td>
  </tr>
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
td,
th {
  text-align: center;
  padding: 8px;
  border-right: 1px solid #d7d6d6;
  font-size: 16px;
  color: #072052;
}

thead th {
  background: #745a5a;
  color: #ffffff;
  text-transform: uppercase;
}

tr {
  height: 50px;
}

tr:nth-child(even) {
  background: #c3c9d7;
}
</style>
