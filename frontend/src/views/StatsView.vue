<script setup lang="ts"></script>

<template>
  <div class="stats-view">
    <header class="view-header">
      <h1>Statistics</h1>
    </header>
    <main class="view-content">
      <p class="placeholder">View your learning progress and statistics</p>
    </main>
  </div>
</template>

<style scoped>
.stats-view {
  padding: 24px 16px 0;
}

.view-header {
  margin-bottom: 24px;
}

.view-header h1 {
  font-size: 32px;
  font-weight: 700;
  color: #ffffff;
}

.view-content {
  padding-bottom: 20px;
}

.placeholder {
  text-align: center;
  color: #8a9db5;
  padding: 60px 20px;
  font-size: 16px;
}
</style>
