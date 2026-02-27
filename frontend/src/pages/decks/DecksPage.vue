<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getDecks, deleteDeck } from '../../api/decks'
import type { Deck } from '../../types'

const router = useRouter()
const decks = ref<Deck[]>([])
const loading = ref(true)
const search = ref('')
const confirmDelete = ref<number | null>(null)

onMounted(async () => {
  try {
    const res = await getDecks()
    decks.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const filtered = computed(() =>
  decks.value.filter(d =>
    d.title.toLowerCase().includes(search.value.toLowerCase())
  )
)

const handleDelete = async (id: number) => {
  if (confirmDelete.value !== id) {
    confirmDelete.value = id
    return
  }
  try {
    await deleteDeck(id)
    decks.value = decks.value.filter(d => d.id !== id)
    confirmDelete.value = null
  } catch (e) {
    console.error(e)
  }
}
</script>

<template>
  <div class="decks-page">

    <!-- HEADER -->
    <div class="header">
      <div>
        <h1 class="title">My Decks</h1>
        <p class="subtitle">{{ decks.length }} deck{{ decks.length !== 1 ? 's' : '' }} in your library</p>
      </div>
      <button class="new-btn" @click="router.push('/decks/create')">+ New Deck</button>
    </div>

    <!-- SEARCH -->
    <div class="search-bar">
      <span class="search-icon">🔍</span>
      <input
        v-model="search"
        type="text"
        placeholder="Search decks..."
      />
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Loading your decks...</p>
    </div>

    <!-- EMPTY -->
    <div v-else-if="decks.length === 0" class="empty">
      <span>📚</span>
      <h3>No decks yet</h3>
      <p>Create your first deck to start studying with spaced repetition.</p>
      <button class="new-btn" @click="router.push('/decks/create')">Create Your First Deck</button>
    </div>

    <!-- NO RESULTS -->
    <div v-else-if="filtered.length === 0" class="empty">
      <span>🔍</span>
      <h3>No decks found</h3>
      <p>Try a different search term.</p>
    </div>

    <!-- DECK GRID -->
    <div v-else class="deck-grid">
      <div v-for="deck in filtered" :key="deck.id" class="deck-card">

        <div class="deck-card-top" @click="router.push(`/decks/${deck.id}`)">
          <div class="deck-icon">📦</div>
          <div class="deck-meta">
            <h3 class="deck-title">{{ deck.title }}</h3>
            <p class="deck-desc">{{ deck.description || 'No description' }}</p>
          </div>
        </div>

        <div class="deck-tags">
          <span v-for="tag in deck.tags" :key="tag.id" class="tag">{{ tag.name }}</span>
          <span v-if="deck.is_public" class="tag public">🌍 Public</span>
        </div>

        <div class="deck-stats">
          <div class="deck-stat">
            <span class="deck-stat-val">{{ deck.cards_count ?? 0 }}</span>
            <span class="deck-stat-lbl">Cards</span>
          </div>
          <div class="deck-stat-divider"></div>
          <div class="deck-stat">
            <span class="deck-stat-val">{{ new Date(deck.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
            <span class="deck-stat-lbl">Created</span>
          </div>
        </div>

        <div class="deck-actions">
          <button class="btn-study" @click="router.push(`/decks/${deck.id}/study`)">Study</button>
          <button class="btn-edit" @click="router.push(`/decks/${deck.id}/edit`)">Edit</button>
          <button
            class="btn-delete"
            :class="{ confirm: confirmDelete === deck.id }"
            @click="handleDelete(deck.id)"
            @blur="confirmDelete = null"
          >
            {{ confirmDelete === deck.id ? 'Confirm?' : '🗑' }}
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<style scoped>
.decks-page { display: flex; flex-direction: column; gap: 24px; }

/* Header */
.header { display: flex; justify-content: space-between; align-items: flex-start; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 4px; }
.subtitle { font-size: 13px; color: #7aa898; }
.new-btn { background: #00e5b4; color: #0a1f1c; border: none; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
.new-btn:hover { background: #00c896; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,229,180,0.25); }

/* Search */
.search-bar { display: flex; align-items: center; gap: 12px; background: #0d2420; border: 1px solid rgba(0,229,180,0.1); border-radius: 12px; padding: 12px 16px; }
.search-icon { font-size: 16px; opacity: 0.5; }
.search-bar input { background: none; border: none; outline: none; color: #e8f5f0; font-size: 14px; width: 100%; }
.search-bar input::placeholder { color: #4a7a68; }

/* Loading */
.loading { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px 0; color: #7aa898; }
.spinner { width: 32px; height: 32px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Empty */
.empty { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 80px 0; color: #7aa898; text-align: center; }
.empty span { font-size: 52px; }
.empty h3 { font-size: 20px; font-weight: 700; color: #fff; }
.empty p { font-size: 14px; line-height: 1.6; max-width: 300px; }

/* Grid */
.deck-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

/* Deck card */
.deck-card {
  background: #0d2420;
  border: 1px solid rgba(0,229,180,0.08);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  transition: all 0.2s;
}
.deck-card:hover { border-color: rgba(0,229,180,0.2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

.deck-card-top { display: flex; gap: 14px; align-items: flex-start; cursor: pointer; }
.deck-icon { font-size: 28px; width: 48px; height: 48px; background: rgba(0,229,180,0.08); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.deck-title { font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.deck-desc { font-size: 12px; color: #7aa898; line-height: 1.5; }

.deck-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.tag { background: rgba(0,229,180,0.08); border: 1px solid rgba(0,229,180,0.15); color: #00e5b4; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.tag.public { background: rgba(100,149,237,0.1); border-color: rgba(100,149,237,0.2); color: #6495ed; }

.deck-stats { display: flex; align-items: center; gap: 16px; padding: 12px 0; border-top: 1px solid rgba(0,229,180,0.06); border-bottom: 1px solid rgba(0,229,180,0.06); }
.deck-stat { display: flex; flex-direction: column; gap: 2px; }
.deck-stat-val { font-size: 16px; font-weight: 700; color: #fff; }
.deck-stat-lbl { font-size: 11px; color: #7aa898; }
.deck-stat-divider { width: 1px; height: 28px; background: rgba(0,229,180,0.08); }

.deck-actions { display: flex; gap: 8px; }
.btn-study { flex: 1; background: #00e5b4; color: #0a1f1c; border: none; padding: 9px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.btn-study:hover { background: #00c896; }
.btn-edit { flex: 1; background: rgba(0,229,180,0.07); color: #7aa898; border: 1px solid rgba(0,229,180,0.12); padding: 9px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-edit:hover { color: #00e5b4; border-color: rgba(0,229,180,0.25); }
.btn-delete { background: rgba(255,100,100,0.08); color: #e87a7a; border: 1px solid rgba(255,100,100,0.15); padding: 9px 12px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.btn-delete:hover { background: rgba(255,100,100,0.15); }
.btn-delete.confirm { background: rgba(255,100,100,0.2); color: #ff6464; border-color: rgba(255,100,100,0.4); font-size: 12px; font-weight: 700; padding: 9px 8px; }
</style>
