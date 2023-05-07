<template>
  <div class="no-results">
    <img src="/public/no-results.png" alt="no results" />
    <p><slot></slot></p>
  </div>
</template>

<script>
export default {
  name: "no-results",
};
</script>

<style scoped>
.no-results {
  text-align: center;
}

.no-results p {
  margin-top: 20px;
  color: #257bc9;
  font-size: 24px;
}

@media (max-width: 794px) {
  .no-results img {
    width: 70px;
  }
  .no-results p {
    font-size: 16px;
  }
}
</style>
