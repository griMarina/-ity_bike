<template>
  <div class="navbar__wrapper">
    <div class="navbar container">
      <div @click="$router.push('/')">Home</div>
      <div class="navbar__btns">
        <my-button class="navbar__btn" @click="$router.push('/stations')"
          >Stations</my-button
        >
        <my-button class="navbar__btn" @click="$router.push('/trips')"
          >Trips</my-button
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {};
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
