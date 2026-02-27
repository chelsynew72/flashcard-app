<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../api'

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const email = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const dailyLimit = ref(20)
const emailReminders = ref(false)

const profileMsg = ref('')
const passwordMsg = ref('')
const prefMsg = ref('')
const profileError = ref('')
const passwordError = ref('')

const savingProfile = ref(false)
const savingPassword = ref(false)
const savingPrefs = ref(false)
const confirmDelete = ref(false)

onMounted(async () => {
  try {
    const res = await api.get('/user')
    name.value = res.data.name
    email.value = res.data.email
    dailyLimit.value = res.data.daily_card_limit
    emailReminders.value = !!res.data.email_reminders
  } catch (e) {
    console.error(e)
  }
})

const saveProfile = async () => {
  savingProfile.value = true
  profileMsg.value = ''
  profileError.value = ''
  try {
    await api.put('/user/profile', { name: name.value, email: email.value })
    profileMsg.value = 'Profile updated successfully!'
    if (auth.user) { auth.user.name = name.value; auth.user.email = email.value }
  } catch (e: any) {
    profileError.value = e?.response?.data?.message || 'Failed to update profile.'
  } finally {
    savingProfile.value = false
  }
}

const savePassword = async () => {
  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = 'Passwords do not match.'
    return
  }
  savingPassword.value = true
  passwordMsg.value = ''
  passwordError.value = ''
  try {
    await api.put('/user/password', {
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: confirmPassword.value,
    })
    passwordMsg.value = 'Password changed successfully!'
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
  } catch (e: any) {
    passwordError.value = e?.response?.data?.message || 'Failed to change password.'
  } finally {
    savingPassword.value = false
  }
}

const savePrefs = async () => {
  savingPrefs.value = true
  prefMsg.value = ''
  try {
    await api.put('/user/preferences', { daily_card_limit: dailyLimit.value, email_reminders: emailReminders.value })
    prefMsg.value = 'Preferences saved!'
  } catch (e) {
    console.error(e)
  } finally {
    savingPrefs.value = false
  }
}

const handleDeleteAccount = async () => {
  if (!confirmDelete.value) { confirmDelete.value = true; return }
  try {
    await api.delete('/user')
    await auth.logout()
    router.push('/login')
  } catch (e) {
    console.error(e)
  }
}
</script>

<template>
  <div class="page">

    <div class="header">
      <h1 class="title">Settings</h1>
      <p class="subtitle">Manage your account and preferences</p>
    </div>

    <!-- PROFILE -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">👤</div>
        <div>
          <h2 class="section-title">Profile</h2>
          <p class="section-desc">Update your name and email address</p>
        </div>
      </div>
      <div class="success-msg" v-if="profileMsg">{{ profileMsg }}</div>
      <div class="error-msg" v-if="profileError">{{ profileError }}</div>
      <form @submit.prevent="saveProfile" class="form">
        <div class="field-row">
          <div class="field">
            <label>Full Name</label>
            <input v-model="name" type="text" placeholder="Your name" />
          </div>
          <div class="field">
            <label>Email Address</label>
            <input v-model="email" type="email" placeholder="your@email.com" />
          </div>
        </div>
        <div class="form-footer">
          <button type="submit" class="save-btn" :disabled="savingProfile">
            {{ savingProfile ? 'Saving...' : 'Save Profile' }}
          </button>
        </div>
      </form>
    </div>

    <!-- PASSWORD -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">🔒</div>
        <div>
          <h2 class="section-title">Change Password</h2>
          <p class="section-desc">Update your password to keep your account secure</p>
        </div>
      </div>
      <div class="success-msg" v-if="passwordMsg">{{ passwordMsg }}</div>
      <div class="error-msg" v-if="passwordError">{{ passwordError }}</div>
      <form @submit.prevent="savePassword" class="form">
        <div class="field">
          <label>Current Password</label>
          <input v-model="currentPassword" type="password" placeholder="••••••••" />
        </div>
        <div class="field-row">
          <div class="field">
            <label>New Password</label>
            <input v-model="newPassword" type="password" placeholder="••••••••" />
          </div>
          <div class="field">
            <label>Confirm New Password</label>
            <input v-model="confirmPassword" type="password" placeholder="••••••••" />
          </div>
        </div>
        <div class="form-footer">
          <button type="submit" class="save-btn" :disabled="savingPassword">
            {{ savingPassword ? 'Saving...' : 'Change Password' }}
          </button>
        </div>
      </form>
    </div>

    <!-- PREFERENCES -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">⚙️</div>
        <div>
          <h2 class="section-title">Study Preferences</h2>
          <p class="section-desc">Customize your daily study goals and notifications</p>
        </div>
      </div>
      <div class="success-msg" v-if="prefMsg">{{ prefMsg }}</div>
      <form @submit.prevent="savePrefs" class="form">
        <div class="field">
          <label>Daily Card Limit</label>
          <div class="range-row">
            <input v-model="dailyLimit" type="range" min="5" max="200" step="5" class="range-input" />
            <span class="range-val">{{ dailyLimit }} cards/day</span>
          </div>
          <p class="field-hint">How many cards to review per day maximum</p>
        </div>
        <div class="toggle-field">
          <div>
            <p class="toggle-label">Email Reminders</p>
            <p class="toggle-desc">Get daily email reminders when you have cards due</p>
          </div>
          <button type="button" class="toggle" :class="{ active: emailReminders }" @click="emailReminders = !emailReminders">
            <span class="toggle-thumb"></span>
          </button>
        </div>
        <div class="form-footer">
          <button type="submit" class="save-btn" :disabled="savingPrefs">
            {{ savingPrefs ? 'Saving...' : 'Save Preferences' }}
          </button>
        </div>
      </form>
    </div>

    <!-- DANGER ZONE -->
    <div class="section-card danger">
      <div class="section-header">
        <div class="section-icon">⚠️</div>
        <div>
          <h2 class="section-title danger-title">Danger Zone</h2>
          <p class="section-desc">Permanently delete your account and all your data</p>
        </div>
      </div>
      <div class="danger-body">
        <p class="danger-warn">This action cannot be undone. All your decks, cards, and progress will be permanently deleted.</p>
        <button
          class="delete-btn"
          :class="{ confirm: confirmDelete }"
          @click="handleDeleteAccount"
        >
          {{ confirmDelete ? '⚠️ Click again to confirm deletion' : 'Delete My Account' }}
        </button>
      </div>
    </div>

  </div>
</template>

<style scoped>
.page { display: flex; flex-direction: column; gap: 24px; max-width: 720px; }
.header { margin-bottom: 4px; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 4px; }
.subtitle { font-size: 13px; color: #7aa898; }

.section-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.08); border-radius: 16px; padding: 28px; display: flex; flex-direction: column; gap: 20px; }
.section-card.danger { border-color: rgba(255,100,100,0.12); }

.section-header { display: flex; align-items: flex-start; gap: 16px; }
.section-icon { width: 44px; height: 44px; background: rgba(0,229,180,0.08); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.section-title { font-size: 17px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.danger-title { color: #ff8080; }
.section-desc { font-size: 13px; color: #7aa898; }

.success-msg { background: rgba(0,229,180,0.1); border: 1px solid rgba(0,229,180,0.25); color: #00e5b4; padding: 10px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; }
.error-msg { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.25); color: #ff8080; padding: 10px 14px; border-radius: 9px; font-size: 13px; }

.form { display: flex; flex-direction: column; gap: 16px; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.field input { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; padding: 12px 14px; font-size: 14px; color: #e8f5f0; outline: none; transition: all 0.2s; width: 100%; }
.field input::placeholder { color: #4a7a68; }
.field input:focus { border-color: rgba(0,229,180,0.35); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.07); }
.field-hint { font-size: 12px; color: #4a7a68; }

.range-row { display: flex; align-items: center; gap: 16px; }
.range-input { flex: 1; accent-color: #00e5b4; }
.range-val { font-size: 14px; font-weight: 700; color: #00e5b4; white-space: nowrap; min-width: 100px; }

.toggle-field { display: flex; justify-content: space-between; align-items: center; background: rgba(0,229,180,0.03); border: 1px solid rgba(0,229,180,0.08); border-radius: 12px; padding: 16px 20px; }
.toggle-label { font-size: 14px; font-weight: 600; color: #e8f5f0; margin-bottom: 4px; }
.toggle-desc { font-size: 12px; color: #7aa898; }
.toggle { width: 48px; height: 26px; background: rgba(0,229,180,0.15); border: 1px solid rgba(0,229,180,0.2); border-radius: 999px; cursor: pointer; transition: all 0.3s; position: relative; flex-shrink: 0; }
.toggle.active { background: #00e5b4; border-color: #00e5b4; }
.toggle-thumb { position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: transform 0.3s; }
.toggle.active .toggle-thumb { transform: translateX(22px); }

.form-footer { display: flex; justify-content: flex-end; padding-top: 4px; }
.save-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.save-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,229,180,0.25); }
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.danger-body { display: flex; justify-content: space-between; align-items: center; gap: 24px; background: rgba(255,100,100,0.04); border: 1px solid rgba(255,100,100,0.1); border-radius: 12px; padding: 20px; }
.danger-warn { font-size: 13px; color: #e87a7a; line-height: 1.6; }
.delete-btn { background: rgba(255,100,100,0.12); color: #ff6464; border: 1px solid rgba(255,100,100,0.25); padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; white-space: nowrap; flex-shrink: 0; }
.delete-btn:hover { background: rgba(255,100,100,0.2); }
.delete-btn.confirm { background: rgba(255,100,100,0.25); border-color: rgba(255,100,100,0.5); }
</style>
