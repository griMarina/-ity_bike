<template>
  <select class="select" v-model="selectedValue" @change="changeOption">
    <option value="" disabled selected>Order by</option>
    <option v-for="option in options" :key="option.value" :value="option.value">
      {{ option.name }}
    </option>
  </select>
</template>

<script>
export default {
  name: "my-select",
  data() {
    return {
      selectedValue: "",
    };
  },
  props: {
    options: {
      type: Array,
      default: () => [],
    },
    modelValue: {
      type: String,
    },
  },
  watch: {
    modelValue(newValue) {
      this.selectedValue = newValue;
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

@media (max-width: 794px) {
  .select {
    font-size: 12px;
  }
}
</style>
