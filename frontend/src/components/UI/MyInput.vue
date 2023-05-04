<template>
  <input :value="modelValue" @input="updateInput" type="text" class="input" />
</template>

<script>
export default {
  name: "my-input",
  props: {
    modelValue: [String, Number],
  },
  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>

<style scoped>
.input {
  margin-top: 15px;
  padding: 10px 15px;
  border: 2px solid #257bc9;
  border-radius: 5px;
  font-size: 14px;
}

@media (max-width: 794px) {
  .input {
    font-size: 12px;
  }
}
</style>
