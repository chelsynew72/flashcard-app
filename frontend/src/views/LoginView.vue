<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  if (!email.value || !password.value) {
    authStore.error = 'Please fill in all fields'
    return
  }

  const success = await authStore.login(email.value, password.value)
  
  if (success) {
    router.push({ name: 'home' })
  }
}

const handleSignup = () => {
  router.push({ name: 'signup' })
}

const handleBackToLanding = () => {
  router.push({ name: 'landing' })
}
</script>

<template>
  <div class="login-view">
    <div class="login-container">
      <button class="back-button" @click="handleBackToLanding">← Back</button>

      <div class="login-card">
        <div class="login-header">
          <h1>Welcome Back</h1>
          <p>Log in to your FlashcardApp account</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="you@example.com"
              class="form-input"
            />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input
              id="password"
              v-model="password"
              type="password"
              placeholder="••••••••"
              class="form-input"
            />
          </div>

          <div v-if="authStore.error" class="error-message">{{ authStore.error }}</div>

          <button type="submit" class="btn-submit" :disabled="authStore.loading">
            {{ authStore.loading ? 'Logging in...' : 'Log In' }}
          </button>
        </form>

        <div class="login-footer">
          <p>Don't have an account? <button class="link-button" @click="handleSignup">Sign up</button></p>
          <a href="#forgot-password" class="forgot-password">Forgot password?</a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-view {
  background: linear-gradient(135deg, #0a1628 0%, #0f1f33 100%);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell,
    sans-serif;
}

.login-container {
  width: 100%;
  max-width: 400px;
  position: relative;
}

.back-button {
  position: absolute;
  top: -50px;
  left: 0;
  background: none;
  border: none;
  color: #00ffd5;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.2s ease;
}

.back-button:hover {
  color: #00d9b8;
}

.login-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 0%, rgba(0, 100, 100, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 16px;
  padding: 32px 24px;
}

.login-header {
  margin-bottom: 32px;
  text-align: center;
}

.login-header h1 {
  font-size: 28px;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 8px 0;
}

.login-header p {
  color: #8a9db5;
  font-size: 14px;
  margin: 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
}

.form-input {
  padding: 12px 16px;
  background: rgba(0, 255, 213, 0.05);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 8px;
  color: #ffffff;
  font-size: 14px;
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #00ffd5;
  background: rgba(0, 255, 213, 0.08);
}

.form-input::placeholder {
  color: #5a6f8a;
}

.error-message {
  padding: 12px 16px;
  background: rgba(255, 100, 100, 0.1);
  border: 1px solid rgba(255, 100, 100, 0.3);
  border-radius: 8px;
  color: #ff6464;
  font-size: 13px;
  margin-top: -8px;
}

.btn-submit {
  padding: 14px 16px;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  border: none;
  color: #0a1628;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  margin-top: 8px;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 255, 213, 0.3);
}

.btn-submit:active:not(:disabled) {
  transform: translateY(0);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-footer {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  text-align: center;
}

.login-footer p {
  margin: 0;
  font-size: 14px;
  color: #8a9db5;
}

.link-button {
  background: none;
  border: none;
  color: #00ffd5;
  cursor: pointer;
  font-weight: 600;
  transition: color 0.2s ease;
  padding: 0;
}

.link-button:hover {
  color: #00d9b8;
}

.forgot-password {
  color: #8a9db5;
  text-decoration: none;
  font-size: 13px;
  transition: color 0.2s ease;
}

.forgot-password:hover {
  color: #00ffd5;
}
</style>
