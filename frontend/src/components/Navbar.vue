<template>
  <div class="navbar__wrapper">
    <div class="navbar container">
      <my-button
        class="navbar__btn"
        @click="$router.push('/')"
        :class="{ active: isRouteActive('/') }"
        >Home</my-button
      >
      <div class="navbar__btns">
        <my-button
          class="navbar__btn"
          @click="$router.push('/stations')"
          :class="{ active: isRouteActive('/stations') }"
          >Stations</my-button
        >
        <my-button
          class="navbar__btn"
          @click="$router.push('/trips')"
          :class="{ active: isRouteActive('/trips') }"
          >Trips</my-button
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    currentRoute() {
      return this.$route.path;
    },
  },
  methods: {
    isRouteActive(path) {
      return this.currentRoute === path;
    },
  },
};
</script>

<style scoped>
.navbar__wrapper {
  background-color: #257bc9;
}

.navbar {
  display: flex;
  align-items: center;
  color: #ffffff;
  font-size: 20px;
  height: 80px;
}
.navbar__btns {
  margin-left: auto;
  display: flex;
  justify-content: space-between;
  min-width: 180px;
}

.navbar__btn {
  min-width: 80px;
}
</style>
