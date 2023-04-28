<template>
  <span class="loader"></span>
</template>

<script>
export default {
  name: "spinner",
};
</script>

<style scoped>
.loader {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: block;
  margin: 50px auto;
  position: relative;
  color: #072052;
  box-sizing: border-box;
  animation: animloader 1s linear infinite alternate;
}

@keyframes animloader {
  0% {
    box-shadow: -38px -6px, -14px 6px, 14px -6px;
  }
  33% {
    box-shadow: -38px 6px, -14px -6px, 14px 6px;
  }
  66% {
    box-shadow: -38px -6px, -14px 6px, 14px -6px;
  }
  100% {
    box-shadow: -38px 6px, -14px -6px, 14px 6px;
  }
}
</style>
