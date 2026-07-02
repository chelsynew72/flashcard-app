<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const menuOpen = ref(false)

const authPages = ['/', '/login', '/register', '/forgot-password', '/reset-password']
const showNav = computed(() => !authPages.includes(route.path))

const navItems = [
  { label: 'Dashboard', icon: '🏠', path: '/dashboard' },
  { label: 'My Decks', icon: '📚', path: '/decks' },
  { label: 'AI Generate', icon: '🤖', path: '/ai-generate' },
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

    <!-- SIDEBAR (desktop) -->
    <aside v-if="showNav" class="sidebar">
      <div class="sidebar-logo">
        <span>🃏</span>
        <span class="logo-text">FlashcardApp</span>
      </div>
      <nav class="sidebar-nav">
        <RouterLink v-for="item in navItems" :key="item.path" :to="item.path" class="nav-link">
          <span class="nav-icon">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
          <span v-if="item.path === '/ai-generate'" class="ai-badge">AI</span>
        </RouterLink>
      </nav>
      <div class="sidebar-bottom">
        <button class="nav-link logout" @click="handleLogout">
          <span class="nav-icon">🚪</span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- MOBILE TOP BAR -->
    <header v-if="showNav" class="mobile-header">
      <div class="mobile-logo">🃏 <span>FlashcardApp</span></div>
      <button class="hamburger" @click="menuOpen = !menuOpen">
        {{ menuOpen ? '✕' : '☰' }}
      </button>
    </header>

    <!-- MOBILE DRAWER -->
    <div v-if="showNav && menuOpen" class="mobile-overlay" @click="menuOpen = false">
      <nav class="mobile-drawer" @click.stop>
        <div class="drawer-logo">🃏 FlashcardApp</div>
        <RouterLink
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          class="nav-link"
          @click="menuOpen = false"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
          <span v-if="item.path === '/ai-generate'" class="ai-badge">AI</span>
        </RouterLink>
        <button class="nav-link logout" @click="handleLogout">
          <span class="nav-icon">🚪</span>
          <span>Logout</span>
        </button>
      </nav>
    </div>

    <!-- BOTTOM NAV (mobile) -->
    <nav v-if="showNav" class="bottom-nav">
      <RouterLink v-for="item in navItems" :key="item.path" :to="item.path" class="bottom-nav-item">
        <span class="bottom-nav-icon">{{ item.icon }}</span>
        <span class="bottom-nav-label">{{ item.label }}</span>
      </RouterLink>
    </nav>

    <!-- MAIN CONTENT -->
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

.app-shell { display: flex; min-height: 100vh; }

/* ── SIDEBAR (desktop) ── */
.sidebar {
  position: fixed; top: 0; left: 0;
  width: 220px; height: 100vh;
  background: #0b201c;
  border-right: 1px solid rgba(0,229,180,0.08);
  display: flex; flex-direction: column;
  padding: 20px 12px; z-index: 100;
}
.sidebar-logo {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 12px 24px; font-size: 20px;
}
.logo-text { font-size: 17px; font-weight: 700; color: #00e5b4; }
.sidebar-nav { display: flex; flex-direction: column; gap: 2px; flex: 1; }
.nav-link {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px; border-radius: 10px;
  font-size: 14px; font-weight: 500; color: #7aa898;
  text-decoration: none; transition: all 0.15s;
  border: 1px solid transparent;
  background: none; cursor: pointer; width: 100%; text-align: left;
}
.nav-link:hover { background: rgba(0,229,180,0.06); color: #e8f5f0; }
.router-link-active.nav-link {
  background: rgba(0,229,180,0.1); color: #00e5b4;
  border-color: rgba(0,229,180,0.15); font-weight: 600;
}
.nav-icon { font-size: 16px; width: 20px; text-align: center; }
.ai-badge {
  margin-left: auto;
  background: linear-gradient(135deg, #00e5b4, #00c896);
  color: #0a1f1c; font-size: 9px; font-weight: 800;
  padding: 2px 6px; border-radius: 999px;
}
.sidebar-bottom { border-top: 1px solid rgba(0,229,180,0.06); padding-top: 12px; }
.logout { color: #e87a7a !important; }
.logout:hover { background: rgba(255,100,100,0.06) !important; }

/* ── MOBILE HEADER ── */
.mobile-header {
  display: none;
  position: fixed; top: 0; left: 0; right: 0;
  height: 56px; background: #0b201c;
  border-bottom: 1px solid rgba(0,229,180,0.08);
  align-items: center; justify-content: space-between;
  padding: 0 16px; z-index: 200;
}
.mobile-logo {
  display: flex; align-items: center; gap: 8px;
  font-size: 16px; font-weight: 700; color: #00e5b4;
}
.hamburger {
  background: none; border: none; color: #e8f5f0;
  font-size: 20px; cursor: pointer; padding: 4px;
}

/* ── MOBILE DRAWER ── */
.mobile-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 300;
}
.mobile-drawer {
  position: absolute; top: 0; left: 0;
  width: 260px; height: 100vh;
  background: #0b201c;
  border-right: 1px solid rgba(0,229,180,0.08);
  padding: 24px 12px;
  display: flex; flex-direction: column; gap: 4px;
}
.drawer-logo {
  font-size: 18px; font-weight: 700; color: #00e5b4;
  padding: 0 12px 20px;
}

/* ── BOTTOM NAV (mobile) ── */
.bottom-nav {
  display: none;
  position: fixed; bottom: 0; left: 0; right: 0;
  height: 60px; background: #0b201c;
  border-top: 1px solid rgba(0,229,180,0.08);
  z-index: 200;
  justify-content: space-around; align-items: center;
  padding: 0 8px;
}
.bottom-nav-item {
  display: flex; flex-direction: column; align-items: center;
  gap: 2px; text-decoration: none; color: #7aa898;
  font-size: 11px; font-weight: 500;
  padding: 4px 8px; border-radius: 8px;
  transition: all 0.15s; flex: 1; text-align: center;
}
.bottom-nav-item.router-link-active { color: #00e5b4; }
.bottom-nav-icon { font-size: 20px; }
.bottom-nav-label { font-size: 10px; }

/* ── MAIN CONTENT ── */
.main { flex: 1; min-height: 100vh; overflow-y: auto; }
.main.with-sidebar { margin-left: 220px; }
.page-content { max-width: 1000px; margin: 0 auto; padding: 40px 32px; }

/* ── RESPONSIVE BREAKPOINTS ── */
@media (max-width: 768px) {
  .sidebar { display: none; }
  .mobile-header { display: flex; }
  .mobile-overlay { display: block; }
  .bottom-nav { display: flex; }
  .main.with-sidebar { margin-left: 0; }
  .page-content { padding: 72px 16px 76px; }
}

/* Auth pages responsive */
@media (max-width: 768px) {
  .page {
    grid-template-columns: 1fr !important;
  }
  .left-panel { display: none !important; }
  .right-panel { padding: 40px 24px !important; min-height: 100vh; }
  .form-container { max-width: 100% !important; }
}

/* Dashboard responsive */
@media (max-width: 768px) {
  .row-2 { grid-template-columns: 1fr !important; }
  .stats-row { grid-template-columns: repeat(2, 1fr) !important; }
  .recent-grid { grid-template-columns: 1fr !important; }
  .tip-grid { grid-template-columns: repeat(2, 1fr) !important; }
}

/* Decks responsive */
@media (max-width: 768px) {
  .decks-grid { grid-template-columns: 1fr !important; }
  .cards-grid { grid-template-columns: 1fr !important; }
  .form-grid { grid-template-columns: 1fr !important; }
  .field-row { grid-template-columns: 1fr !important; }
}

/* Study page responsive */
@media (max-width: 768px) {
  .flashcard { height: 220px !important; }
  .face-text { font-size: 16px !important; }
  .rating-btns { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
  .r-btn { padding: 12px 8px !important; }
}

/* Tables and misc */
@media (max-width: 480px) {
  .stats-row { grid-template-columns: repeat(2, 1fr) !important; }
  .tip-grid { grid-template-columns: 1fr !important; }
  .session-stats { grid-template-columns: repeat(2, 1fr) !important; }
  .difficulty-btns { flex-direction: column !important; }
}
</style>
