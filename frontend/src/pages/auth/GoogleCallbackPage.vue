<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

onMounted(async () => {
  const token = route.query.token as string
  const name = route.query.name as string
  const email = route.query.email as string
  const error = route.query.error as string

  if (error || !token) {
    router.push('/login?error=google_failed')
    return
  }

  // Save token
  localStorage.setItem('token', token)
  auth.token = token

  // Save user
  const userData = { name, email }
  localStorage.setItem('user', JSON.stringify(userData))
  auth.user = userData as any

  // Fetch full user profile
  await auth.fetchUser()

  router.push('/dashboard')
})
</script>

<template>
  <div class="callback-page">
    <div class="spinner"></div>
    <p>Signing you in with Google...</p>
  </div>
</template>

<style scoped>
.callback-page {
  min-height: 100vh;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 16px; background: #0a1f1c;
  color: #7aa898; font-size: 15px;
}
.spinner {
  width: 40px; height: 40px;
  border: 3px solid rgba(0,229,180,0.15);
  border-top-color: #00e5b4;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
