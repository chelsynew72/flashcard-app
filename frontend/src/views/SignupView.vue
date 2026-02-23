<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const loading = ref(false)
const error = ref('')

const handleSignup = async () => {
  if (!name.value || !email.value || !password.value || !confirmPassword.value) {
    error.value = 'Please fill in all fields'
    return
  }

  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match'
    return
  }

  if (password.value.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return
  }

  loading.value = true
  error.value = ''

  try {
    await new Promise((resolve) => setTimeout(resolve, 500))
    localStorage.setItem('auth_token', 'demo_token_' + Date.now())
    router.push({ name: 'home' })
  } catch (err) {
    error.value = 'Signup failed. Please try again.'
  } finally {
    loading.value = false
  }
}

const handleLogin = () => {
  router.push({ name: 'login' })
}

const handleBackToLanding = () => {
  router.push({ name: 'landing' })
}
</script>

<template>
  <div class="signup-view">
    <div class="signup-container">
      <button class="back-button" @click="handleBackToLanding">← Back</button>

      <div class="signup-card">
        <div class="signup-header">
          <h1>Create Account</h1>
          <p>Join FlashcardApp and start learning smarter</p>
        </div>

        <form @submit.prevent="handleSignup" class="signup-form">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input
              id="name"
              v-model="name"
              type="text"
              placeholder="John Doe"
              class="form-input"
            />
          </div>

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
            <p class="field-hint">Must be at least 8 characters</p>
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input
              id="confirmPassword"
              v-model="confirmPassword"
              type="password"
              placeholder="••••••••"
              class="form-input"
            />
          </div>

          <div v-if="error" class="error-message">{{ error }}</div>

          <button type="submit" class="btn-submit" :disabled="loading">
            {{ loading ? 'Creating Account...' : 'Create Account' }}
          </button>
        </form>

        <div class="signup-footer">
          <p>Already have an account? <button class="link-button" @click="handleLogin">Log in</button></p>
          <p class="terms">By signing up, you agree to our Terms of Service and Privacy Policy</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.signup-view {
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

.signup-container {
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

.signup-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 0%, rgba(0, 100, 100, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 16px;
  padding: 32px 24px;
}

.signup-header {
  margin-bottom: 32px;
  text-align: center;
}

.signup-header h1 {
  font-size: 28px;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 8px 0;
}

.signup-header p {
  color: #8a9db5;
  font-size: 14px;
  margin: 0;
}

.signup-form {
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

.field-hint {
  margin: 4px 0 0 0;
  font-size: 12px;
  color: #8a9db5;
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

.signup-footer {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  text-align: center;
}

.signup-footer p {
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

.terms {
  font-size: 12px !important;
  color: #5a6f8a !important;
}
</style>
