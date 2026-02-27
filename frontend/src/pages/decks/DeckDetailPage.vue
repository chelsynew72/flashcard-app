<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getDeck, deleteDeck } from '../../api/decks'
import { createCard, updateCard, deleteCard } from '../../api/cards'
import { startSession } from '../../api/study'
import type { Deck, Card } from '../../types'

const router = useRouter()
const route = useRoute()
const deckId = Number(route.params.id)

const deck = ref<Deck | null>(null)
const cards = ref<Card[]>([])
const loading = ref(true)
const error = ref('')

// Add card
const showAddCard = ref(false)
const newFront = ref('')
const newBack = ref('')
const addingCard = ref(false)

// Edit card
const editingCard = ref<Card | null>(null)
const editFront = ref('')
const editBack = ref('')
const savingEdit = ref(false)

// Delete
const confirmDeleteCard = ref<number | null>(null)
const confirmDeleteDeck = ref(false)

onMounted(async () => {
  try {
    const res = await getDeck(deckId)
    deck.value = res.data
    cards.value = res.data.cards || []
  } catch (e) {
    error.value = 'Failed to load deck.'
  } finally {
    loading.value = false
  }
})

const handleAddCard = async () => {
  if (!newFront.value.trim() || !newBack.value.trim()) return
  addingCard.value = true
  try {
    const res = await createCard(deckId, { front: newFront.value, back: newBack.value })
    cards.value.push(res.data)
    newFront.value = ''
    newBack.value = ''
    showAddCard.value = false
    if (deck.value) deck.value.cards_count = (deck.value.cards_count || 0) + 1
  } catch (e) {
    console.error(e)
  } finally {
    addingCard.value = false
  }
}

const startEdit = (card: Card) => {
  editingCard.value = card
  editFront.value = card.front
  editBack.value = card.back
}

const handleSaveEdit = async () => {
  if (!editingCard.value) return
  savingEdit.value = true
  try {
    const res = await updateCard(deckId, editingCard.value.id, {
      front: editFront.value,
      back: editBack.value,
    })
    const idx = cards.value.findIndex(c => c.id === editingCard.value!.id)
    if (idx !== -1) cards.value[idx] = res.data
    editingCard.value = null
  } catch (e) {
    console.error(e)
  } finally {
    savingEdit.value = false
  }
}

const handleDeleteCard = async (id: number) => {
  if (confirmDeleteCard.value !== id) {
    confirmDeleteCard.value = id
    return
  }
  try {
    await deleteCard(deckId, id)
    cards.value = cards.value.filter(c => c.id !== id)
    if (deck.value) deck.value.cards_count = Math.max(0, (deck.value.cards_count || 1) - 1)
    confirmDeleteCard.value = null
  } catch (e) {
    console.error(e)
  }
}

const handleDeleteDeck = async () => {
  if (!confirmDeleteDeck.value) {
    confirmDeleteDeck.value = true
    return
  }
  try {
    await deleteDeck(deckId)
    router.push('/decks')
  } catch (e) {
    console.error(e)
  }
}

const handleStudy = async () => {
  try {
    const res = await startSession(deckId)
    router.push(`/decks/${deckId}/study`)
  } catch (e) {
    console.error(e)
  }
}
</script>

<template>
  <div class="page">

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Loading deck...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <span>⚠️</span>
      <p>{{ error }}</p>
      <button @click="router.push('/decks')">← Back to Decks</button>
    </div>

    <template v-else-if="deck">

      <!-- HEADER -->
      <div class="header">
        <button class="back-btn" @click="router.push('/decks')">← My Decks</button>
        <div class="header-main">
          <div class="header-info">
            <div class="header-title-row">
              <h1 class="title">{{ deck.title }}</h1>
              <span v-if="deck.is_public" class="public-badge">🌍 Public</span>
            </div>
            <p class="desc">{{ deck.description || 'No description' }}</p>
            <div class="tags">
              <span v-for="tag in deck.tags" :key="tag.id" class="tag">{{ tag.name }}</span>
            </div>
          </div>
          <div class="header-actions">
            <button class="btn-edit" @click="router.push(`/decks/${deckId}/edit`)">✏️ Edit</button>
            <button
              class="btn-delete-deck"
              :class="{ confirm: confirmDeleteDeck }"
              @click="handleDeleteDeck"
            >
              {{ confirmDeleteDeck ? 'Are you sure?' : '🗑 Delete' }}
            </button>
            <button class="btn-study" :disabled="cards.length === 0" @click="handleStudy">
              ▶ Study ({{ cards.length }} cards)
            </button>
          </div>
        </div>
      </div>

      <!-- STATS ROW -->
      <div class="stats-row">
        <div class="stat-card">
          <p class="stat-val">{{ cards.length }}</p>
          <p class="stat-lbl">Total Cards</p>
        </div>
        <div class="stat-card">
          <p class="stat-val">{{ new Date(deck.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</p>
          <p class="stat-lbl">Created</p>
        </div>
        <div class="stat-card">
          <p class="stat-val">{{ deck.is_public ? 'Public' : 'Private' }}</p>
          <p class="stat-lbl">Visibility</p>
        </div>
        <div class="stat-card add-card-stat" @click="showAddCard = true">
          <p class="stat-val">+</p>
          <p class="stat-lbl">Add Card</p>
        </div>
      </div>

      <!-- ADD CARD FORM -->
      <div v-if="showAddCard" class="add-card-form">
        <div class="add-card-header">
          <h3>Add New Card</h3>
          <button class="close-btn" @click="showAddCard = false; newFront = ''; newBack = ''">✕</button>
        </div>
        <div class="card-fields">
          <div class="field">
            <label>Front (Question)</label>
            <textarea v-model="newFront" placeholder="Enter the question or term..." rows="3"></textarea>
          </div>
          <div class="field">
            <label>Back (Answer)</label>
            <textarea v-model="newBack" placeholder="Enter the answer or definition..." rows="3"></textarea>
          </div>
        </div>
        <div class="add-actions">
          <button class="cancel-btn" @click="showAddCard = false; newFront = ''; newBack = ''">Cancel</button>
          <button class="save-btn" :disabled="!newFront.trim() || !newBack.trim() || addingCard" @click="handleAddCard">
            {{ addingCard ? 'Adding...' : 'Add Card' }}
          </button>
        </div>
      </div>

      <!-- CARDS LIST -->
      <div class="cards-section">
        <div class="cards-header">
          <h2 class="cards-title">Cards <span class="cards-count">{{ cards.length }}</span></h2>
          <button class="add-btn" @click="showAddCard = true">+ Add Card</button>
        </div>

        <div v-if="cards.length === 0" class="empty">
          <span>🃏</span>
          <h3>No cards yet</h3>
          <p>Add your first card to start studying this deck.</p>
          <button class="save-btn" @click="showAddCard = true">Add Your First Card</button>
        </div>

        <div v-else class="cards-list">
          <div v-for="card in cards" :key="card.id" class="card-item">

            <!-- VIEW MODE -->
            <template v-if="editingCard?.id !== card.id">
              <div class="card-content">
                <div class="card-side">
                  <p class="side-label">FRONT</p>
                  <p class="side-text">{{ card.front }}</p>
                </div>
                <div class="card-divider"></div>
                <div class="card-side">
                  <p class="side-label">BACK</p>
                  <p class="side-text">{{ card.back }}</p>
                </div>
              </div>
              <div class="card-actions">
                <button class="card-btn edit" @click="startEdit(card)">✏️ Edit</button>
                <button
                  class="card-btn delete"
                  :class="{ confirm: confirmDeleteCard === card.id }"
                  @click="handleDeleteCard(card.id)"
                  @blur="confirmDeleteCard = null"
                >
                  {{ confirmDeleteCard === card.id ? 'Confirm?' : '🗑' }}
                </button>
              </div>
            </template>

            <!-- EDIT MODE -->
            <template v-else>
              <div class="edit-form">
                <div class="card-fields">
                  <div class="field">
                    <label>Front</label>
                    <textarea v-model="editFront" rows="2"></textarea>
                  </div>
                  <div class="field">
                    <label>Back</label>
                    <textarea v-model="editBack" rows="2"></textarea>
                  </div>
                </div>
                <div class="add-actions">
                  <button class="cancel-btn" @click="editingCard = null">Cancel</button>
                  <button class="save-btn" :disabled="savingEdit" @click="handleSaveEdit">
                    {{ savingEdit ? 'Saving...' : 'Save Changes' }}
                  </button>
                </div>
              </div>
            </template>

          </div>
        </div>
      </div>

    </template>
  </div>
</template>

<style scoped>
.page { display: flex; flex-direction: column; gap: 24px; }

/* Loading / Error */
.loading, .error-state { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 80px 0; color: #7aa898; }
.spinner { width: 32px; height: 32px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.error-state span { font-size: 36px; }

/* Header */
.back-btn { background: none; border: none; color: #7aa898; font-size: 13px; cursor: pointer; padding: 0; margin-bottom: 12px; display: block; transition: color 0.2s; }
.back-btn:hover { color: #00e5b4; }
.header-main { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
.header-title-row { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.public-badge { background: rgba(100,149,237,0.12); border: 1px solid rgba(100,149,237,0.25); color: #6495ed; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.desc { font-size: 14px; color: #7aa898; margin-bottom: 10px; line-height: 1.5; }
.tags { display: flex; flex-wrap: wrap; gap: 6px; }
.tag { background: rgba(0,229,180,0.08); border: 1px solid rgba(0,229,180,0.15); color: #00e5b4; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.header-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }
.btn-edit { background: rgba(0,229,180,0.07); color: #7aa898; border: 1px solid rgba(0,229,180,0.12); padding: 9px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-edit:hover { color: #00e5b4; border-color: rgba(0,229,180,0.25); }
.btn-delete-deck { background: rgba(255,100,100,0.08); color: #e87a7a; border: 1px solid rgba(255,100,100,0.15); padding: 9px 14px; border-radius: 9px; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.btn-delete-deck:hover { background: rgba(255,100,100,0.15); }
.btn-delete-deck.confirm { color: #ff6464; border-color: rgba(255,100,100,0.4); font-weight: 700; }
.btn-study { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 9px 18px; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.btn-study:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,229,180,0.25); }
.btn-study:disabled { opacity: 0.4; cursor: not-allowed; }

/* Stats */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.stat-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.07); border-radius: 12px; padding: 18px; text-align: center; transition: all 0.2s; }
.stat-card:hover { border-color: rgba(0,229,180,0.15); }
.add-card-stat { cursor: pointer; border-style: dashed; }
.add-card-stat:hover { border-color: rgba(0,229,180,0.4); background: rgba(0,229,180,0.05); }
.add-card-stat .stat-val { color: #00e5b4; font-size: 28px; }
.stat-val { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 4px; }
.stat-lbl { font-size: 11px; color: #7aa898; }

/* Add card form */
.add-card-form { background: #0d2420; border: 1px solid rgba(0,229,180,0.2); border-radius: 16px; padding: 24px; }
.add-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.add-card-header h3 { font-size: 16px; font-weight: 700; color: #fff; }
.close-btn { background: none; border: none; color: #7aa898; font-size: 18px; cursor: pointer; transition: color 0.2s; }
.close-btn:hover { color: #fff; }

/* Card fields */
.card-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 12px; font-weight: 600; color: #7aa898; letter-spacing: 0.5px; }
.field textarea {
  background: rgba(0,229,180,0.04);
  border: 1px solid rgba(0,229,180,0.12);
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 14px;
  color: #e8f5f0;
  outline: none;
  transition: all 0.2s;
  resize: vertical;
  font-family: inherit;
  line-height: 1.5;
}
.field textarea::placeholder { color: #4a7a68; }
.field textarea:focus { border-color: rgba(0,229,180,0.35); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.07); }

.add-actions { display: flex; gap: 10px; justify-content: flex-end; }
.cancel-btn { background: none; border: 1px solid rgba(0,229,180,0.12); color: #7aa898; padding: 9px 18px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.cancel-btn:hover { border-color: rgba(0,229,180,0.3); color: #e8f5f0; }
.save-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 9px 20px; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.save-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,229,180,0.25); }
.save-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* Cards section */
.cards-section { display: flex; flex-direction: column; gap: 16px; }
.cards-header { display: flex; justify-content: space-between; align-items: center; }
.cards-title { font-size: 18px; font-weight: 700; color: #fff; }
.cards-count { background: rgba(0,229,180,0.1); color: #00e5b4; padding: 2px 10px; border-radius: 999px; font-size: 14px; margin-left: 8px; }
.add-btn { background: rgba(0,229,180,0.08); color: #00e5b4; border: 1px solid rgba(0,229,180,0.2); padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.add-btn:hover { background: rgba(0,229,180,0.14); }

/* Empty */
.empty { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px 0; color: #7aa898; text-align: center; }
.empty span { font-size: 48px; }
.empty h3 { font-size: 18px; font-weight: 700; color: #fff; }
.empty p { font-size: 14px; }

/* Cards list */
.cards-list { display: flex; flex-direction: column; gap: 10px; }
.card-item { background: #0d2420; border: 1px solid rgba(0,229,180,0.07); border-radius: 14px; padding: 20px; transition: all 0.2s; }
.card-item:hover { border-color: rgba(0,229,180,0.15); }
.card-content { display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: start; margin-bottom: 14px; }
.card-side { display: flex; flex-direction: column; gap: 6px; }
.side-label { font-size: 10px; letter-spacing: 1.5px; color: #4a7a68; font-weight: 600; }
.side-text { font-size: 14px; color: #e8f5f0; line-height: 1.6; }
.card-divider { width: 1px; background: rgba(0,229,180,0.08); align-self: stretch; }
.card-actions { display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid rgba(0,229,180,0.06); padding-top: 12px; }
.card-btn { padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.card-btn.edit { background: rgba(0,229,180,0.07); color: #7aa898; border: 1px solid rgba(0,229,180,0.12); }
.card-btn.edit:hover { color: #00e5b4; border-color: rgba(0,229,180,0.25); }
.card-btn.delete { background: rgba(255,100,100,0.07); color: #e87a7a; border: 1px solid rgba(255,100,100,0.12); }
.card-btn.delete:hover { background: rgba(255,100,100,0.14); }
.card-btn.delete.confirm { color: #ff6464; border-color: rgba(255,100,100,0.4); background: rgba(255,100,100,0.15); }
.edit-form { display: flex; flex-direction: column; gap: 16px; }
</style>
