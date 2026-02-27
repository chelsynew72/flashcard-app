import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: () => import('../pages/LandingPage.vue') },
    { path: '/login', component: () => import('../pages/auth/LoginPage.vue') },
    { path: '/register', component: () => import('../pages/auth/RegisterPage.vue') },
    { path: '/forgot-password', component: () => import('../pages/auth/ForgotPasswordPage.vue') },
    { path: '/reset-password', component: () => import('../pages/auth/ResetPasswordPage.vue') },
    { path: '/dashboard', component: () => import('../pages/DashboardPage.vue'), meta: { requiresAuth: true } },
    { path: '/decks', component: () => import('../pages/decks/DecksPage.vue'), meta: { requiresAuth: true } },
    { path: '/decks/create', component: () => import('../pages/decks/CreateDeckPage.vue'), meta: { requiresAuth: true } },
    { path: '/decks/:id', component: () => import('../pages/decks/DeckDetailPage.vue'), meta: { requiresAuth: true } },
    { path: '/decks/:id/edit', component: () => import('../pages/decks/EditDeckPage.vue'), meta: { requiresAuth: true } },
    { path: '/decks/:id/study', component: () => import('../pages/study/StudyPage.vue'), meta: { requiresAuth: true } },
    { path: '/explore', component: () => import('../pages/ExplorePage.vue'), meta: { requiresAuth: true } },
    { path: '/profile', component: () => import('../pages/ProfilePage.vue'), meta: { requiresAuth: true } },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return '/login'
  if ((to.path === '/login' || to.path === '/register') && auth.isLoggedIn) return '/dashboard'
})

export default router
