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
  padding: 4px;
  border-right: 1px solid #d7d6d6;
  font-size: 16px;
  color: #072052;
}

tr {
  height: 30px;
}

tr:nth-child(even) {
  background: #b2cfeb;
}
</style>
