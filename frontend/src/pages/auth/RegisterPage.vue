<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import api from '../../api'

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const showPassword = ref(false)
const showConfirm = ref(false)
const agreed = ref(false)
const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  if (!agreed.value) {
    error.value = 'Please agree to the Terms of Service and Privacy Policy.'
    return
  }
  if (password.value !== password_confirmation.value) {
    error.value = 'Passwords do not match.'
    return
  }
  loading.value = true
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value, password_confirmation.value)
    router.push('/dashboard')
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Registration failed. Please try again.'
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

    <!-- LEFT PANEL -->
    <div class="left-panel">
      <div class="left-content">
        <div class="brand">
          <div class="brand-icon">🃏</div>
          <span class="brand-name">FlashcardApp</span>
        </div>

        <div class="left-hero">
          <h1>Study smarter<br>with <span class="accent">science.</span></h1>
          <p>Join over 1,000,000 students using spaced repetition to master any subject faster than ever before.</p>
        </div>

        <div class="features">
          <div class="feature-row">
            <div class="feature-item">
              <span class="feature-icon">🧠</span>
              <span>SM-2 Algorithm</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">⚡</span>
              <span>Fast Setup</span>
            </div>
          </div>
          <div class="feature-row">
            <div class="feature-item">
              <span class="feature-icon">🔄</span>
              <span>Cloud Sync</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">📊</span>
              <span>Progress Tracking</span>
            </div>
          </div>
        </div>

        <div class="testimonial">
          <p class="testimonial-text">"The best study tool I've ever used. My exam scores improved dramatically after just two weeks."</p>
          <div class="testimonial-author">
            <div class="author-avatar">S</div>
            <div>
              <p class="author-name">Sarah Chen</p>
              <p class="author-title">Medical Student at Johns Hopkins</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
      <div class="form-container">
        <div class="form-header">
          <h2>Create your account</h2>
          <p>Join our community today. Already have an account?
            <a @click="router.push('/login')">Log in</a>
          </p>
        </div>

        <!-- GOOGLE BUTTON -->
        <button class="google-btn" @click="handleGoogleLogin">
          <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          Continue with Google
        </button>

        <div class="divider"><span>or sign up with email</span></div>

        <div class="error-box" v-if="error">{{ error }}</div>

        <form @submit.prevent="handleRegister" class="form">
          <div class="field">
            <label>Full Name</label>
            <input v-model="name" type="text" placeholder="John Doe" required />
          </div>

          <div class="field">
            <label>Email Address</label>
            <input v-model="email" type="email" placeholder="email@example.com" required />
          </div>

          <div class="field-row">
            <div class="field">
              <label>Password</label>
              <div class="input-wrap">
                <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" required />
                <button type="button" class="eye-btn" @click="showPassword = !showPassword">
                  {{ showPassword ? '🙈' : '👁️' }}
                </button>
              </div>
            </div>
            <div class="field">
              <label>Confirm Password</label>
              <div class="input-wrap">
                <input v-model="password_confirmation" :type="showConfirm ? 'text' : 'password'" placeholder="••••••••" required />
                <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">
                  {{ showConfirm ? '🙈' : '👁️' }}
                </button>
              </div>
            </div>
          </div>

          <div class="checkbox-row">
            <input type="checkbox" id="agree" v-model="agreed" />
            <label for="agree">
              By creating an account, you agree to our
              <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
            </label>
          </div>

          <button type="submit" class="submit-btn" :disabled="loading">
            <span v-if="loading">Creating account...</span>
            <span v-else>Create Account →</span>
          </button>
        </form>

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
.features { display: flex; flex-direction: column; gap: 16px; }
.feature-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.feature-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #8ab8a8; font-weight: 500; }
.feature-icon { font-size: 18px; }
.testimonial { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.1); border-radius: 16px; padding: 28px; }
.testimonial-text { font-size: 15px; color: #c8e8d8; line-height: 1.7; font-style: italic; margin-bottom: 20px; }
.testimonial-author { display: flex; align-items: center; gap: 12px; }
.author-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, #00e5b4, #00c896); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #0a1f1c; }
.author-name { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 2px; }
.author-title { font-size: 12px; color: #8ab8a8; }

.right-panel { display: flex; align-items: center; justify-content: center; padding: 60px 40px; background: #0b2018; }
.form-container { width: 100%; max-width: 480px; display: flex; flex-direction: column; gap: 24px; }
.form-header h2 { font-size: 32px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 10px; }
.form-header p { font-size: 15px; color: #8ab8a8; }
.form-header a { color: #00e5b4; cursor: pointer; text-decoration: none; font-weight: 600; }
.form-header a:hover { text-decoration: underline; }

/* Google button */
.google-btn {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  width: 100%; padding: 13px;
  background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12);
  border-radius: 12px; font-size: 15px; font-weight: 600;
  color: #8ab8a8; cursor: pointer; transition: all 0.2s;
}
.google-btn:hover { border-color: rgba(0,229,180,0.3); color: #e8f5f0; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,229,180,0.1); }

/* Divider */
.divider { display: flex; align-items: center; gap: 14px; color: #4a7a68; font-size: 13px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(0,229,180,0.08); }

.error-box { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.3); color: #ff8080; padding: 12px 16px; border-radius: 10px; font-size: 14px; }

.form { display: flex; flex-direction: column; gap: 18px; }
.field { display: flex; flex-direction: column; gap: 8px; flex: 1; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; letter-spacing: 0.3px; }
.field input { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; padding: 13px 16px; font-size: 15px; color: #e8f5f0; outline: none; transition: all 0.2s; width: 100%; }
.field input::placeholder { color: #4a7a68; }
.field input:focus { border-color: rgba(0,229,180,0.4); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.08); }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.input-wrap { position: relative; }
.input-wrap input { padding-right: 44px; }
.eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px; opacity: 0.6; transition: opacity 0.2s; }
.eye-btn:hover { opacity: 1; }

.checkbox-row { display: flex; align-items: flex-start; gap: 10px; }
.checkbox-row input[type="checkbox"] { width: 16px; height: 16px; margin-top: 2px; accent-color: #00e5b4; flex-shrink: 0; cursor: pointer; }
.checkbox-row label { font-size: 13px; color: #8ab8a8; line-height: 1.5; cursor: pointer; }
.checkbox-row a { color: #00e5b4; text-decoration: none; font-weight: 600; }

.submit-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 14px; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; width: 100%; }
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,229,180,0.3); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.form-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 8px; border-top: 1px solid rgba(0,229,180,0.06); }
.form-footer p { font-size: 12px; color: #4a7a68; }
.footer-links { display: flex; gap: 16px; }
.footer-links a { font-size: 12px; color: #4a7a68; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #00e5b4; }
</style>