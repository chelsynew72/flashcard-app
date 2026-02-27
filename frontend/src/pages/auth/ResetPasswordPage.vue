<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { resetPassword } from '../../api/auth'

const router = useRouter()
const route = useRoute()

const token = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirm = ref(false)
const loading = ref(false)
const success = ref(false)
const error = ref('')

onMounted(() => {
  token.value = route.query.token as string || ''
  email.value = route.query.email as string || ''
  if (!token.value) {
    error.value = 'Invalid or missing reset token. Please request a new password reset.'
  }
})

const handleSubmit = async () => {
  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match.'
    return
  }
  if (password.value.length < 8) {
    error.value = 'Password must be at least 8 characters.'
    return
  }
  loading.value = true
  error.value = ''
  try {
    await resetPassword({
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    })
    success.value = true
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to reset password. The link may have expired.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="page">

    <div class="left-panel">
      <div class="left-content">
        <div class="brand" @click="router.push('/')">
          <div class="brand-icon">🃏</div>
          <span class="brand-name">FlashcardApp</span>
        </div>
        <div class="left-hero">
          <h1>Create a new<br><span class="accent">password.</span></h1>
          <p>Choose a strong password that you haven't used before. We recommend using a mix of letters, numbers and symbols.</p>
        </div>
        <div class="tips">
          <p class="tips-title">Password tips:</p>
          <ul>
            <li>At least 8 characters long</li>
            <li>Mix of uppercase and lowercase letters</li>
            <li>Include numbers and symbols</li>
            <li>Avoid common words or phrases</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="right-panel">
      <div class="form-container">

        <!-- SUCCESS -->
        <div v-if="success" class="success-state">
          <div class="success-icon">🎉</div>
          <h2>Password reset!</h2>
          <p>Your password has been successfully changed. You can now log in with your new password.</p>
          <button class="login-btn" @click="router.push('/login')">Go to Login →</button>
        </div>

        <!-- FORM -->
        <template v-else>
          <div class="form-header">
            <h2>Set new password</h2>
            <p v-if="email">Resetting password for <strong>{{ email }}</strong></p>
          </div>

          <div class="error-box" v-if="error">{{ error }}</div>

          <form @submit.prevent="handleSubmit" class="form" v-if="token">
            <div class="field">
              <label>New Password</label>
              <div class="input-wrap">
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  required
                  autofocus
                />
                <button type="button" class="eye-btn" @click="showPassword = !showPassword">
                  {{ showPassword ? '🙈' : '👁️' }}
                </button>
              </div>
              <div class="password-strength" v-if="password">
                <div class="strength-bar">
                  <div
                    class="strength-fill"
                    :class="{
                      weak: password.length < 6,
                      medium: password.length >= 6 && password.length < 10,
                      strong: password.length >= 10
                    }"
                    :style="{ width: Math.min(100, password.length * 10) + '%' }"
                  ></div>
                </div>
                <span class="strength-label"
                  :class="{
                    weak: password.length < 6,
                    medium: password.length >= 6 && password.length < 10,
                    strong: password.length >= 10
                  }"
                >
                  {{ password.length < 6 ? 'Weak' : password.length < 10 ? 'Medium' : 'Strong' }}
                </span>
              </div>
            </div>

            <div class="field">
              <label>Confirm New Password</label>
              <div class="input-wrap">
                <input
                  v-model="confirmPassword"
                  :type="showConfirm ? 'text' : 'password'"
                  placeholder="••••••••"
                  required
                />
                <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">
                  {{ showConfirm ? '🙈' : '👁️' }}
                </button>
              </div>
              <p class="match-msg" v-if="confirmPassword" :class="{ match: password === confirmPassword, nomatch: password !== confirmPassword }">
                {{ password === confirmPassword ? '✓ Passwords match' : '✗ Passwords do not match' }}
              </p>
            </div>

            <button type="submit" class="submit-btn" :disabled="loading || !token">
              {{ loading ? 'Resetting...' : 'Reset Password →' }}
            </button>
          </form>

          <div class="back-link">
            <button @click="router.push('/forgot-password')">← Request new reset link</button>
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
.left-content { display: flex; flex-direction: column; gap: 48px; max-width: 440px; }
.brand { display: flex; align-items: center; gap: 12px; cursor: pointer; width: fit-content; }
.brand-icon { width: 44px; height: 44px; background: rgba(0,229,180,0.15); border: 1px solid rgba(0,229,180,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.brand-name { font-size: 20px; font-weight: 700; color: #e8f5f0; }
.left-hero h1 { font-size: 44px; font-weight: 800; color: #fff; line-height: 1.15; letter-spacing: -1px; margin-bottom: 16px; }
.accent { color: #00e5b4; }
.left-hero p { font-size: 16px; color: #8ab8a8; line-height: 1.7; }
.tips { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.1); border-radius: 14px; padding: 24px; }
.tips-title { font-size: 13px; font-weight: 700; color: #00e5b4; margin-bottom: 12px; }
.tips ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
.tips li { font-size: 14px; color: #8ab8a8; padding-left: 20px; position: relative; }
.tips li::before { content: '✓'; position: absolute; left: 0; color: #00e5b4; font-weight: 700; }

.right-panel { display: flex; align-items: center; justify-content: center; padding: 60px 40px; background: #0b2018; }
.form-container { width: 100%; max-width: 440px; display: flex; flex-direction: column; gap: 28px; }

.success-state { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 16px; background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 20px; padding: 48px 32px; }
.success-icon { font-size: 56px; }
.success-state h2 { font-size: 28px; font-weight: 800; color: #fff; }
.success-state p { font-size: 15px; color: #8ab8a8; line-height: 1.6; }
.login-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 14px 28px; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; margin-top: 8px; }
.login-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,229,180,0.25); }

.form-header h2 { font-size: 30px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 10px; }
.form-header p { font-size: 15px; color: #8ab8a8; }
.form-header strong { color: #00e5b4; }

.error-box { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.3); color: #ff8080; padding: 12px 16px; border-radius: 10px; font-size: 14px; }

.form { display: flex; flex-direction: column; gap: 20px; }
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.input-wrap { position: relative; }
.field input { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; padding: 14px 44px 14px 16px; font-size: 15px; color: #e8f5f0; outline: none; transition: all 0.2s; width: 100%; }
.field input::placeholder { color: #4a7a68; }
.field input:focus { border-color: rgba(0,229,180,0.4); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.08); }
.eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px; opacity: 0.6; transition: opacity 0.2s; }
.eye-btn:hover { opacity: 1; }

.password-strength { display: flex; align-items: center; gap: 10px; margin-top: 4px; }
.strength-bar { flex: 1; height: 4px; background: rgba(0,229,180,0.1); border-radius: 2px; overflow: hidden; }
.strength-fill { height: 100%; border-radius: 2px; transition: all 0.3s; }
.strength-fill.weak { background: #ff6464; }
.strength-fill.medium { background: #ffa500; }
.strength-fill.strong { background: #00e5b4; }
.strength-label { font-size: 12px; font-weight: 600; }
.strength-label.weak { color: #ff6464; }
.strength-label.medium { color: #ffa500; }
.strength-label.strong { color: #00e5b4; }

.match-msg { font-size: 12px; font-weight: 600; }
.match-msg.match { color: #00e5b4; }
.match-msg.nomatch { color: #ff6464; }

.submit-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 15px; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s; width: 100%; }
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,229,180,0.3); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.back-link { text-align: center; }
.back-link button { background: none; border: none; color: #7aa898; font-size: 14px; cursor: pointer; transition: color 0.2s; }
.back-link button:hover { color: #00e5b4; }

.form-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 8px; border-top: 1px solid rgba(0,229,180,0.06); }
.form-footer p { font-size: 12px; color: #4a7a68; }
.footer-links { display: flex; gap: 16px; }
.footer-links a { font-size: 12px; color: #4a7a68; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #00e5b4; }
</style>
