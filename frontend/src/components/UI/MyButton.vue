<template>
  <button class="btn">
    <slot></slot>
  </button>
</template>

<script>
export default {
  name: "my-button",
};
</script>

<style scoped>
.btn {
  padding: 10px 15px;
  border: 1px solid white;
  background: #072052;
  transition-duration: 0.3s;
  text-align: center;
  color: white;
}

.btn:hover {
  cursor: pointer;
  background-color: white;
  color: #072052;
}
</style>
