<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { forgotPassword } from '../../api/auth'

const router = useRouter()
const email = ref('')
const loading = ref(false)
const sent = ref(false)
const error = ref('')

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  try {
    await forgotPassword(email.value)
    sent.value = true
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="page">

    <!-- LEFT PANEL -->
    <div class="left-panel">
      <div class="left-content">
        <div class="brand" @click="router.push('/')">
          <div class="brand-icon">🃏</div>
          <span class="brand-name">FlashcardApp</span>
        </div>

        <div class="left-hero">
          <h1>Forgot your<br><span class="accent">password?</span></h1>
          <p>No worries — it happens to everyone. Enter your email and we'll send you a reset link right away.</p>
        </div>

        <div class="steps">
          <div class="step">
            <div class="step-num">1</div>
            <div>
              <p class="step-title">Enter your email</p>
              <p class="step-desc">The one you used to create your account</p>
            </div>
          </div>
          <div class="step">
            <div class="step-num">2</div>
            <div>
              <p class="step-title">Check your inbox</p>
              <p class="step-desc">We'll send a secure reset link within seconds</p>
            </div>
          </div>
          <div class="step">
            <div class="step-num">3</div>
            <div>
              <p class="step-title">Create new password</p>
              <p class="step-desc">Choose something strong and memorable</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
      <div class="form-container">

        <!-- SUCCESS STATE -->
        <div v-if="sent" class="success-state">
          <div class="success-icon">📬</div>
          <h2>Check your inbox</h2>
          <p>We sent a password reset link to <strong>{{ email }}</strong>. It expires in 60 minutes.</p>
          <p class="spam-note">Don't see it? Check your spam folder.</p>
          <div class="success-actions">
            <button class="resend-btn" @click="sent = false">Try a different email</button>
            <button class="login-btn" @click="router.push('/login')">Back to Login →</button>
          </div>
        </div>

        <!-- FORM STATE -->
        <template v-else>
          <div class="form-header">
            <h2>Reset your password</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
          </div>

          <div class="error-box" v-if="error">{{ error }}</div>

          <form @submit.prevent="handleSubmit" class="form">
            <div class="field">
              <label>Email Address</label>
              <input
                v-model="email"
                type="email"
                placeholder="email@example.com"
                required
                autofocus
              />
            </div>

            <button type="submit" class="submit-btn" :disabled="loading">
              {{ loading ? 'Sending...' : 'Send Reset Link →' }}
            </button>
          </form>

          <div class="back-link">
            <button @click="router.push('/login')">← Back to Login</button>
          </div>
        </template>

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
.page {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
  background: #0a1f1c;
}

/* LEFT */
.left-panel {
  background: linear-gradient(160deg, #0d2a22 0%, #0a1f1c 60%, #071a16 100%);
  border-right: 1px solid rgba(0,229,180,0.08);
  display: flex;
  align-items: center;
  padding: 60px;
  position: relative;
  overflow: hidden;
}

.left-panel::before {
  content: '';
  position: absolute;
  top: -200px; left: -200px;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(0,229,180,0.06) 0%, transparent 70%);
  pointer-events: none;
}

.left-content { display: flex; flex-direction: column; gap: 48px; max-width: 440px; }

.brand {
  display: flex; align-items: center; gap: 12px; cursor: pointer; width: fit-content;
}
.brand-icon {
  width: 44px; height: 44px;
  background: rgba(0,229,180,0.15);
  border: 1px solid rgba(0,229,180,0.3);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
}
.brand-name { font-size: 20px; font-weight: 700; color: #e8f5f0; }

.left-hero h1 {
  font-size: 44px; font-weight: 800; color: #fff;
  line-height: 1.15; letter-spacing: -1px; margin-bottom: 16px;
}
.accent { color: #00e5b4; }
.left-hero p { font-size: 16px; color: #8ab8a8; line-height: 1.7; }

.steps { display: flex; flex-direction: column; gap: 20px; }
.step { display: flex; align-items: flex-start; gap: 16px; }
.step-num {
  width: 32px; height: 32px; flex-shrink: 0;
  background: linear-gradient(135deg, #00e5b4, #00c896);
  color: #0a1f1c; border-radius: 50%;
  font-size: 14px; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
}
.step-title { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 2px; }
.step-desc { font-size: 13px; color: #8ab8a8; }

/* RIGHT */
.right-panel {
  display: flex; align-items: center; justify-content: center;
  padding: 60px 40px;
  background: #0b2018;
}

.form-container {
  width: 100%; max-width: 440px;
  display: flex; flex-direction: column; gap: 28px;
}

/* Success */
.success-state {
  display: flex; flex-direction: column; align-items: center;
  text-align: center; gap: 16px;
  background: rgba(0,229,180,0.04);
  border: 1px solid rgba(0,229,180,0.12);
  border-radius: 20px;
  padding: 40px 32px;
}
.success-icon { font-size: 56px; }
.success-state h2 { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.success-state p { font-size: 15px; color: #8ab8a8; line-height: 1.6; }
.success-state strong { color: #00e5b4; }
.spam-note { font-size: 13px !important; color: #4a7a68 !important; }
.success-actions { display: flex; flex-direction: column; gap: 10px; width: 100%; margin-top: 8px; }
.login-btn {
  background: linear-gradient(135deg, #00e5b4, #00c896);
  color: #0a1f1c; border: none;
  padding: 13px; border-radius: 10px;
  font-size: 15px; font-weight: 700;
  cursor: pointer; transition: all 0.2s; width: 100%;
}
.login-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,229,180,0.25); }
.resend-btn {
  background: none;
  border: 1px solid rgba(0,229,180,0.15);
  color: #7aa898; padding: 12px;
  border-radius: 10px; font-size: 14px;
  font-weight: 600; cursor: pointer;
  transition: all 0.2s; width: 100%;
}
.resend-btn:hover { border-color: rgba(0,229,180,0.3); color: #e8f5f0; }

/* Form */
.form-header h2 { font-size: 30px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 10px; }
.form-header p { font-size: 15px; color: #8ab8a8; line-height: 1.6; }

.error-box {
  background: rgba(255,100,100,0.1);
  border: 1px solid rgba(255,100,100,0.3);
  color: #ff8080; padding: 12px 16px;
  border-radius: 10px; font-size: 14px;
}

.form { display: flex; flex-direction: column; gap: 20px; }
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.field input {
  background: rgba(0,229,180,0.04);
  border: 1px solid rgba(0,229,180,0.12);
  border-radius: 10px; padding: 14px 16px;
  font-size: 15px; color: #e8f5f0;
  outline: none; transition: all 0.2s; width: 100%;
}
.field input::placeholder { color: #4a7a68; }
.field input:focus {
  border-color: rgba(0,229,180,0.4);
  background: rgba(0,229,180,0.06);
  box-shadow: 0 0 0 3px rgba(0,229,180,0.08);
}

.submit-btn {
  background: linear-gradient(135deg, #00e5b4, #00c896);
  color: #0a1f1c; border: none;
  padding: 15px; border-radius: 12px;
  font-size: 16px; font-weight: 700;
  cursor: pointer; transition: all 0.2s; width: 100%;
}
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,229,180,0.3); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.back-link { text-align: center; }
.back-link button {
  background: none; border: none;
  color: #7aa898; font-size: 14px;
  cursor: pointer; transition: color 0.2s;
}
.back-link button:hover { color: #00e5b4; }

.form-footer {
  display: flex; align-items: center;
  justify-content: space-between;
  padding-top: 8px;
  border-top: 1px solid rgba(0,229,180,0.06);
}
.form-footer p { font-size: 12px; color: #4a7a68; }
.footer-links { display: flex; gap: 16px; }
.footer-links a { font-size: 12px; color: #4a7a68; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #00e5b4; }
</style>
