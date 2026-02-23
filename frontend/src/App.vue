<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { RouterView, useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const activeTab = ref('home')

const tabs = [
  { id: 'home', label: 'HOME', icon: '🏠' },
  { id: 'library', label: 'LIBRARY', icon: '📚' },
  { id: 'discover', label: 'DISCOVER', icon: '🔍' },
  { id: 'stats', label: 'STATS', icon: '📊' },
  { id: 'profile', label: 'PROFILE', icon: '👤' },
]

const showBottomNav = computed(() => {
  return route.meta.requiresAuth === true
})

watch(
  () => route.name,
  (newRoute) => {
    if (newRoute === 'home' || newRoute === 'library' || newRoute === 'discover' || newRoute === 'stats' || newRoute === 'profile') {
      activeTab.value = newRoute
    }
  },
  { immediate: true },
)

const navigateTo = (tabId: string) => {
  activeTab.value = tabId
  router.push({ name: tabId })
}
</script>

<template>
  <div class="app-container">
    <div class="app-content">
      <RouterView />
    </div>

    <nav v-if="showBottomNav" class="bottom-nav">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        class="nav-item"
        :class="{ active: activeTab === tab.id }"
        @click="navigateTo(tab.id)"
      >
        <span class="nav-icon">{{ tab.icon }}</span>
        <span class="nav-label">{{ tab.label }}</span>
      </button>
    </nav>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body {
  height: 100%;
  width: 100%;
}

.app-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #0a1628;
  color: #ffffff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell,
    sans-serif;
}

.app-content {
  flex: 1;
  overflow-y: auto;
  padding-bottom: 80px;
}

.bottom-nav ~ .app-content {
  padding-bottom: 80px;
}

.app-container:not(:has(.bottom-nav)) .app-content {
  padding-bottom: 0;
}

.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-around;
  align-items: center;
  height: 80px;
  background-color: #0a1628;
  border-top: 1px solid rgba(0, 255, 213, 0.1);
  z-index: 100;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  padding: 8px 16px;
  background: none;
  border: none;
  color: #7a8fa3;
  cursor: pointer;
  transition: color 0.3s ease;
  font-size: 12px;
  font-weight: 500;
}

.nav-item:hover {
  color: #a0b0c0;
}

.nav-item.active {
  color: #00ffd5;
}

.nav-icon {
  font-size: 20px;
}

.nav-label {
  white-space: nowrap;
}
</style>
