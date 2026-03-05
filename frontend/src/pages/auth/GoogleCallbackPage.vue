<script setup lang="ts">
// Use vanilla JS instead of Vue Router to handle the redirect
// This runs immediately, before Vue fully mounts
const params = new URLSearchParams(window.location.search)
const token = params.get('token')
const name = params.get('name') || ''
const email = params.get('email') || ''
const error = params.get('error')

if (error || !token) {
  window.location.replace('/login?error=google_failed')
} else {
  localStorage.setItem('token', token)
  localStorage.setItem('user', JSON.stringify({ name, email }))
  window.location.replace('/dashboard')
}
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
