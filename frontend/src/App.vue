<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const authPages = ['/', '/login', '/register', '/forgot-password', '/reset-password']
const showNav = computed(() => !authPages.includes(route.path))

const navItems = [
  { label: 'Dashboard', icon: '🏠', path: '/dashboard' },
  { label: 'My Decks', icon: '📚', path: '/decks' },
  { label: 'Explore', icon: '🔍', path: '/explore' },
  { label: 'Settings', icon: '⚙️', path: '/profile' },
]

const handleLogout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <div class="app-shell">
    <aside v-if="showNav" class="sidebar">
      <div class="sidebar-logo">
        <span>🃏</span>
        <span class="logo-text">FlashcardApp</span>
      </div>
      <nav class="sidebar-nav">
        <RouterLink
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          class="nav-link"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </RouterLink>
      </nav>
      <div class="sidebar-bottom">
        <button class="nav-link logout" @click="handleLogout">
          <span class="nav-icon">🚪</span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <main class="main" :class="{ 'with-sidebar': showNav }">
      <div class="page-content">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<style>
@import './assets/main.css';

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: #0a1f1c;
  color: #e8f5f0;
  font-family: 'DM Sans', system-ui, sans-serif;
  min-height: 100vh;
}

.app-shell {
  display: flex;
  min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 220px;
  height: 100vh;
  background: #0b201c;
  border-right: 1px solid rgba(0,229,180,0.08);
  display: flex;
  flex-direction: column;
  padding: 20px 12px;
  z-index: 100;
  flex-shrink: 0;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px 24px;
  font-size: 20px;
}

.logo-text {
  font-size: 17px;
  font-weight: 700;
  color: #00e5b4;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  color: #7aa898;
  text-decoration: none;
  transition: all 0.15s;
  border: 1px solid transparent;
  background: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
}

.nav-link:hover {
  background: rgba(0,229,180,0.06);
  color: #e8f5f0;
}

.router-link-active.nav-link {
  background: rgba(0,229,180,0.1);
  color: #00e5b4;
  border-color: rgba(0,229,180,0.15);
  font-weight: 600;
}

.nav-icon { font-size: 16px; width: 20px; text-align: center; }

.sidebar-bottom {
  border-top: 1px solid rgba(0,229,180,0.06);
  padding-top: 12px;
}

.logout { color: #e87a7a !important; }
.logout:hover { background: rgba(255,100,100,0.06) !important; }

/* MAIN */
.main {
  flex: 1;
  min-height: 100vh;
  overflow-y: auto;
}

.main.with-sidebar {
  margin-left: 220px;
}

.page-content {
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 32px;
}
</style>
