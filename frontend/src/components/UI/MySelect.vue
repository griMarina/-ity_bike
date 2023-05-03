<template>
  <select class="select" v-model="modelValue" @change="changeOption">
    <option value="" disabled selected>Filter by</option>
    <option v-for="option in options" :key="option.value" :value="option.value">
      {{ option.name }}
    </option>
  </select>
</template>

<script>
export default {
  name: "my-select",
  modelValue: {
    type: String,
  },
  props: {
    options: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    changeOption(event) {
      this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>

<style scoped>
.select {
  min-width: 30%;
  margin-top: 10px;
  padding: 10px 15px;
  border: 2px solid #257bc9;
  border-radius: 5px;
  font-size: 14px;
}
</style>
