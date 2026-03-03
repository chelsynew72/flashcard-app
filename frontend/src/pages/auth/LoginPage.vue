<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import api from '../../api'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    router.push('/dashboard')
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Invalid email or password.'
  } finally {
    loading.value = false
  }
}

const handleGoogleLogin = async () => {
  try {
    const res = await api.get('/auth/google')
    window.location.href = res.data.url
  } catch (e) {
    error.value = 'Failed to connect to Google. Please try again.'
  }
}
</script>

<template>
  <div class="page">
    <div class="left-panel">
      <div class="left-content">
        <div class="brand">
          <div class="brand-icon">🃏</div>
          <span class="brand-name">FlashcardApp</span>
        </div>
        <div class="left-hero">
          <h1>Welcome<br>back to <span class="accent">learning.</span></h1>
          <p>Continue where you left off. Your cards are ready and waiting for today's review session.</p>
        </div>
        <div class="stats">
          <div class="stat-item">
            <p class="stat-value">95%</p>
            <p class="stat-label">Retention Rate</p>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <p class="stat-value">1M+</p>
            <p class="stat-label">Active Learners</p>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <p class="stat-value">500M+</p>
            <p class="stat-label">Cards Studied</p>
          </div>
        </div>
        <div class="testimonial">
          <p class="testimonial-text">"I went from failing my med school exams to acing them. FlashcardApp changed everything."</p>
          <div class="testimonial-author">
            <div class="author-avatar">M</div>
            <div>
              <p class="author-name">Marcus Johnson</p>
              <p class="author-title">3rd Year Medical Student</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="right-panel">
      <div class="form-container">
        <div class="form-header">
          <h2>Sign in to your account</h2>
          <p>Don't have an account? <a @click="router.push('/register')">Create one free</a></p>
        </div>
        <div class="error-box" v-if="error">{{ error }}</div>
        <form @submit.prevent="handleLogin" class="form">
          <div class="field">
            <label>Email Address</label>
            <input v-model="email" type="email" placeholder="email@example.com" required autofocus />
          </div>
          <div class="field">
            <div class="label-row">
              <label>Password</label>
              <a class="forgot-link" @click="router.push('/forgot-password')">Forgot password?</a>
            </div>
            <div class="input-wrap">
              <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" required />
              <button type="button" class="eye-btn" @click="showPassword = !showPassword">
                {{ showPassword ? '🙈' : '👁️' }}
              </button>
            </div>
          </div>
          <button type="submit" class="submit-btn" :disabled="loading">
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign In →</span>
          </button>
        </form>

        <div class="divider"><span>or continue with</span></div>

        <div class="oauth-row">
          <button class="oauth-btn google" @click="handleGoogleLogin">
            <svg width="18" height="18" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
          </button>
          <button class="oauth-btn" disabled>
            🍎 Apple
          </button>
        </div>

        <div class="form-footer">
          <p>© 2024 FlashcardApp Inc.</p>
          <div class="footer-links">
            <a href="#">Support</a>
            <a href="#">Status</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; background: #0a1f1c; }
.left-panel {
  background: linear-gradient(160deg, #0d2a22 0%, #0a1f1c 60%, #071a16 100%);
  border-right: 1px solid rgba(0,229,180,0.08);
  display: flex; align-items: center; padding: 60px;
  position: relative; overflow: hidden;
}
.left-panel::before {
  content: ''; position: absolute; top: -200px; left: -200px;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(0,229,180,0.06) 0%, transparent 70%);
  pointer-events: none;
}
.left-content { display: flex; flex-direction: column; gap: 48px; max-width: 480px; }
.brand { display: flex; align-items: center; gap: 12px; }
.brand-icon { width: 44px; height: 44px; background: rgba(0,229,180,0.15); border: 1px solid rgba(0,229,180,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.brand-name { font-size: 20px; font-weight: 700; color: #e8f5f0; }
.left-hero h1 { font-size: 44px; font-weight: 800; color: #fff; line-height: 1.15; letter-spacing: -1px; margin-bottom: 16px; }
.accent { color: #00e5b4; }
.left-hero p { font-size: 16px; color: #8ab8a8; line-height: 1.7; }
.stats { display: flex; align-items: center; gap: 24px; background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.1); border-radius: 16px; padding: 24px 28px; }
.stat-item { text-align: center; flex: 1; }
.stat-value { font-size: 28px; font-weight: 800; color: #00e5b4; letter-spacing: -0.5px; margin-bottom: 4px; }
.stat-label { font-size: 12px; color: #8ab8a8; font-weight: 500; }
.stat-divider { width: 1px; height: 40px; background: rgba(0,229,180,0.1); }
.testimonial { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.1); border-radius: 16px; padding: 28px; }
.testimonial-text { font-size: 15px; color: #c8e8d8; line-height: 1.7; font-style: italic; margin-bottom: 20px; }
.testimonial-author { display: flex; align-items: center; gap: 12px; }
.author-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, #00e5b4, #00c896); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #0a1f1c; }
.author-name { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 2px; }
.author-title { font-size: 12px; color: #8ab8a8; }
.right-panel { display: flex; align-items: center; justify-content: center; padding: 60px 40px; background: #0b2018; }
.form-container { width: 100%; max-width: 440px; display: flex; flex-direction: column; gap: 28px; }
.form-header h2 { font-size: 32px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 10px; }
.form-header p { font-size: 15px; color: #8ab8a8; }
.form-header a { color: #00e5b4; cursor: pointer; text-decoration: none; font-weight: 600; }
.form-header a:hover { text-decoration: underline; }
.error-box { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.3); color: #ff8080; padding: 12px 16px; border-radius: 10px; font-size: 14px; }
.form { display: flex; flex-direction: column; gap: 20px; }
.field { display: flex; flex-direction: column; gap: 8px; }
.label-row { display: flex; justify-content: space-between; align-items: center; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.forgot-link { font-size: 13px; color: #00e5b4; cursor: pointer; text-decoration: none; font-weight: 500; }
.forgot-link:hover { text-decoration: underline; }
.field input { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; padding: 14px 16px; font-size: 15px; color: #e8f5f0; outline: none; transition: all 0.2s; width: 100%; }
.field input::placeholder { color: #4a7a68; }
.field input:focus { border-color: rgba(0,229,180,0.4); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.08); }
.input-wrap { position: relative; }
.input-wrap input { padding-right: 44px; }
.eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px; opacity: 0.6; transition: opacity 0.2s; }
.eye-btn:hover { opacity: 1; }
.submit-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; width: 100%; margin-top: 4px; }
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,229,180,0.3); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.divider { display: flex; align-items: center; gap: 16px; color: #4a7a68; font-size: 13px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(0,229,180,0.08); }
.oauth-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.oauth-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; color: #8ab8a8; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.oauth-btn:hover:not(:disabled) { border-color: rgba(0,229,180,0.3); color: #e8f5f0; }
.oauth-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.oauth-btn.google { cursor: pointer; opacity: 1; }
.form-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 8px; border-top: 1px solid rgba(0,229,180,0.06); }
.form-footer p { font-size: 12px; color: #4a7a68; }
.footer-links { display: flex; gap: 16px; }
.footer-links a { font-size: 12px; color: #4a7a68; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #00e5b4; }
</style>