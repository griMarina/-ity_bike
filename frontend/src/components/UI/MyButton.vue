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
  background: #257bc9;
  transition-duration: 0.3s;
  text-align: center;
  color: white;
  border-radius: 5px;
}

.btn:hover {
  cursor: pointer;
  background-color: white;
  color: #257bc9;
  border-color:  #257bc9;

}
</style>
