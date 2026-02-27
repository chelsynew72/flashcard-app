<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { createDeck } from '../../api/decks'

const router = useRouter()

const title = ref('')
const description = ref('')
const isPublic = ref(false)
const tagsInput = ref('')
const loading = ref(false)
const error = ref('')

const handleSubmit = async () => {
  if (!title.value.trim()) {
    error.value = 'Title is required.'
    return
  }
  loading.value = true
  error.value = ''
  try {
    const tags = tagsInput.value
      .split(',')
      .map(t => t.trim())
      .filter(t => t.length > 0)

    const res = await createDeck({
      title: title.value,
      description: description.value,
      is_public: isPublic.value,
      tags,
    })
    router.push(`/decks/${res.data.id}`)
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to create deck.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="page">

    <!-- HEADER -->
    <div class="header">
      <button class="back-btn" @click="router.push('/decks')">← Back</button>
      <div>
        <h1 class="title">Create New Deck</h1>
        <p class="subtitle">Add a new flashcard deck to your library</p>
      </div>
    </div>

    <!-- FORM -->
    <div class="form-card">
      <div class="error-box" v-if="error">{{ error }}</div>

      <form @submit.prevent="handleSubmit" class="form">

        <div class="field">
          <label>Deck Title <span class="required">*</span></label>
          <input
            v-model="title"
            type="text"
            placeholder="e.g. Spanish Vocabulary, JavaScript Basics..."
            required
            autofocus
          />
        </div>

        <div class="field">
          <label>Description</label>
          <textarea
            v-model="description"
            placeholder="What is this deck about? (optional)"
            rows="3"
          ></textarea>
        </div>

        <div class="field">
          <label>Tags</label>
          <input
            v-model="tagsInput"
            type="text"
            placeholder="javascript, programming, web (comma separated)"
          />
          <p class="field-hint">Separate multiple tags with commas</p>
          <div class="tag-preview" v-if="tagsInput.trim()">
            <span
              v-for="tag in tagsInput.split(',').map(t => t.trim()).filter(t => t)"
              :key="tag"
              class="tag"
            >{{ tag }}</span>
          </div>
        </div>

        <div class="toggle-field">
          <div class="toggle-info">
            <p class="toggle-label">Make Public</p>
            <p class="toggle-desc">Public decks can be discovered and cloned by other users</p>
          </div>
          <button
            type="button"
            class="toggle"
            :class="{ active: isPublic }"
            @click="isPublic = !isPublic"
          >
            <span class="toggle-thumb"></span>
          </button>
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="router.push('/decks')">Cancel</button>
          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Creating...' : 'Create Deck →' }}
          </button>
        </div>

      </form>
    </div>

    <!-- TIP -->
    <div class="tip-card">
      <span class="tip-icon">💡</span>
      <div>
        <p class="tip-title">Pro Tip</p>
        <p class="tip-text">After creating your deck, you can add cards one by one or import them in bulk from a CSV file.</p>
      </div>
    </div>

  </div>
</template>

<style scoped>
.page { display: flex; flex-direction: column; gap: 24px; max-width: 640px; }

.header { display: flex; flex-direction: column; gap: 12px; }
.back-btn { background: none; border: none; color: #7aa898; font-size: 14px; cursor: pointer; padding: 0; width: fit-content; transition: color 0.2s; }
.back-btn:hover { color: #00e5b4; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 4px; }
.subtitle { font-size: 13px; color: #7aa898; }

.form-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.08); border-radius: 16px; padding: 32px; }

.error-box { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.3); color: #ff8080; padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; }

.form { display: flex; flex-direction: column; gap: 24px; }

.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.required { color: #00e5b4; }
.field input, .field textarea {
  background: rgba(0,229,180,0.04);
  border: 1px solid rgba(0,229,180,0.12);
  border-radius: 10px;
  padding: 13px 16px;
  font-size: 14px;
  color: #e8f5f0;
  outline: none;
  transition: all 0.2s;
  width: 100%;
  resize: vertical;
  font-family: inherit;
}
.field input::placeholder, .field textarea::placeholder { color: #4a7a68; }
.field input:focus, .field textarea:focus {
  border-color: rgba(0,229,180,0.35);
  background: rgba(0,229,180,0.06);
  box-shadow: 0 0 0 3px rgba(0,229,180,0.07);
}
.field-hint { font-size: 12px; color: #4a7a68; }

.tag-preview { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
.tag { background: rgba(0,229,180,0.1); border: 1px solid rgba(0,229,180,0.2); color: #00e5b4; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }

.toggle-field {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(0,229,180,0.03);
  border: 1px solid rgba(0,229,180,0.08);
  border-radius: 12px;
  padding: 16px 20px;
}
.toggle-label { font-size: 14px; font-weight: 600; color: #e8f5f0; margin-bottom: 4px; }
.toggle-desc { font-size: 12px; color: #7aa898; }
.toggle {
  width: 48px; height: 26px;
  background: rgba(0,229,180,0.15);
  border: 1px solid rgba(0,229,180,0.2);
  border-radius: 999px;
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
  flex-shrink: 0;
}
.toggle.active { background: #00e5b4; border-color: #00e5b4; }
.toggle-thumb {
  position: absolute;
  top: 3px; left: 3px;
  width: 18px; height: 18px;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.3s;
}
.toggle.active .toggle-thumb { transform: translateX(22px); }

.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid rgba(0,229,180,0.06); }
.cancel-btn { background: none; border: 1px solid rgba(0,229,180,0.12); color: #7aa898; padding: 11px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.cancel-btn:hover { border-color: rgba(0,229,180,0.3); color: #e8f5f0; }
.submit-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 11px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.submit-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,229,180,0.25); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.tip-card { display: flex; gap: 16px; align-items: flex-start; background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.1); border-radius: 12px; padding: 20px; }
.tip-icon { font-size: 24px; flex-shrink: 0; }
.tip-title { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.tip-text { font-size: 13px; color: #7aa898; line-height: 1.6; }
</style>
