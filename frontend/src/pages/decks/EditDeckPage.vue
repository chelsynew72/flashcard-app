<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getDeck, updateDeck } from '../../api/decks'

const router = useRouter()
const route = useRoute()
const deckId = Number(route.params.id)

const title = ref('')
const description = ref('')
const isPublic = ref(false)
const tagsInput = ref('')
const loading = ref(true)
const saving = ref(false)
const error = ref('')

onMounted(async () => {
  try {
    const res = await getDeck(deckId)
    const deck = res.data
    title.value = deck.title
    description.value = deck.description || ''
    isPublic.value = deck.is_public
    tagsInput.value = deck.tags.map((t: any) => t.name).join(', ')
  } catch (e) {
    error.value = 'Failed to load deck.'
  } finally {
    loading.value = false
  }
})

const handleSubmit = async () => {
  if (!title.value.trim()) { error.value = 'Title is required.'; return }
  saving.value = true
  error.value = ''
  try {
    const tags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t)
    await updateDeck(deckId, { title: title.value, description: description.value, is_public: isPublic.value, tags })
    router.push(`/decks/${deckId}`)
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to save changes.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="page">
    <div class="header">
      <button class="back-btn" @click="router.push(`/decks/${deckId}`)">← Back to Deck</button>
      <h1 class="title">Edit Deck</h1>
      <p class="subtitle">Update your deck details</p>
    </div>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Loading deck...</p>
    </div>

    <div v-else class="form-card">
      <div class="error-box" v-if="error">{{ error }}</div>
      <form @submit.prevent="handleSubmit" class="form">

        <div class="field">
          <label>Deck Title <span class="required">*</span></label>
          <input v-model="title" type="text" placeholder="Deck title..." required />
        </div>

        <div class="field">
          <label>Description</label>
          <textarea v-model="description" placeholder="What is this deck about?" rows="3"></textarea>
        </div>

        <div class="field">
          <label>Tags</label>
          <input v-model="tagsInput" type="text" placeholder="javascript, programming (comma separated)" />
          <p class="field-hint">Separate multiple tags with commas</p>
          <div class="tag-preview" v-if="tagsInput.trim()">
            <span v-for="tag in tagsInput.split(',').map(t => t.trim()).filter(t => t)" :key="tag" class="tag">{{ tag }}</span>
          </div>
        </div>

        <div class="toggle-field">
          <div class="toggle-info">
            <p class="toggle-label">Make Public</p>
            <p class="toggle-desc">Public decks can be discovered and cloned by other users</p>
          </div>
          <button type="button" class="toggle" :class="{ active: isPublic }" @click="isPublic = !isPublic">
            <span class="toggle-thumb"></span>
          </button>
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="router.push(`/decks/${deckId}`)">Cancel</button>
          <button type="submit" class="submit-btn" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save Changes →' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
.page { display: flex; flex-direction: column; gap: 24px; max-width: 640px; }
.header { display: flex; flex-direction: column; gap: 6px; }
.back-btn { background: none; border: none; color: #7aa898; font-size: 13px; cursor: pointer; padding: 0; width: fit-content; transition: color 0.2s; margin-bottom: 6px; }
.back-btn:hover { color: #00e5b4; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.subtitle { font-size: 13px; color: #7aa898; }
.loading { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px 0; color: #7aa898; }
.spinner { width: 32px; height: 32px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.form-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.08); border-radius: 16px; padding: 32px; }
.error-box { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.3); color: #ff8080; padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; }
.form { display: flex; flex-direction: column; gap: 24px; }
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.required { color: #00e5b4; }
.field input, .field textarea { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; padding: 13px 16px; font-size: 14px; color: #e8f5f0; outline: none; transition: all 0.2s; width: 100%; resize: vertical; font-family: inherit; }
.field input::placeholder, .field textarea::placeholder { color: #4a7a68; }
.field input:focus, .field textarea:focus { border-color: rgba(0,229,180,0.35); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.07); }
.field-hint { font-size: 12px; color: #4a7a68; }
.tag-preview { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
.tag { background: rgba(0,229,180,0.1); border: 1px solid rgba(0,229,180,0.2); color: #00e5b4; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.toggle-field { display: flex; justify-content: space-between; align-items: center; background: rgba(0,229,180,0.03); border: 1px solid rgba(0,229,180,0.08); border-radius: 12px; padding: 16px 20px; }
.toggle-label { font-size: 14px; font-weight: 600; color: #e8f5f0; margin-bottom: 4px; }
.toggle-desc { font-size: 12px; color: #7aa898; }
.toggle { width: 48px; height: 26px; background: rgba(0,229,180,0.15); border: 1px solid rgba(0,229,180,0.2); border-radius: 999px; cursor: pointer; transition: all 0.3s; position: relative; flex-shrink: 0; }
.toggle.active { background: #00e5b4; border-color: #00e5b4; }
.toggle-thumb { position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: transform 0.3s; }
.toggle.active .toggle-thumb { transform: translateX(22px); }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid rgba(0,229,180,0.06); }
.cancel-btn { background: none; border: 1px solid rgba(0,229,180,0.12); color: #7aa898; padding: 11px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.cancel-btn:hover { border-color: rgba(0,229,180,0.3); color: #e8f5f0; }
.submit-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 11px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.submit-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,229,180,0.25); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
