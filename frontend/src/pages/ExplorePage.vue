<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'
import { cloneDeck } from '../api/decks'
import type { Deck } from '../types'

const router = useRouter()
const decks = ref<Deck[]>([])
const loading = ref(true)
const search = ref('')
const searchTimeout = ref<any>(null)
const cloning = ref<number | null>(null)
const clonedIds = ref<number[]>([])
const currentPage = ref(1)
const lastPage = ref(1)

const fetchDecks = async (page = 1) => {
  loading.value = true
  try {
    const res = await api.get('/explore', {
      params: { search: search.value || undefined, page }
    })
    decks.value = res.data.data
    currentPage.value = res.data.current_page
    lastPage.value = res.data.last_page
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchDecks())

const handleSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => fetchDecks(1), 400)
}

const handleClone = async (deck: Deck) => {
  if (cloning.value === deck.id || clonedIds.value.includes(deck.id)) return
  cloning.value = deck.id
  try {
    await cloneDeck(deck.id)
    clonedIds.value.push(deck.id)
  } catch (e) {
    console.error(e)
  } finally {
    cloning.value = null
  }
}
</script>

<template>
  <div class="page">

    <!-- HEADER -->
    <div class="header">
      <div>
        <h1 class="title">Explore</h1>
        <p class="subtitle">Discover and clone public decks from the community</p>
      </div>
    </div>

    <!-- SEARCH -->
    <div class="search-bar">
      <span class="search-icon">🔍</span>
      <input
        v-model="search"
        type="text"
        placeholder="Search public decks..."
        @input="handleSearch"
      />
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Finding decks...</p>
    </div>

    <!-- EMPTY -->
    <div v-else-if="decks.length === 0" class="empty">
      <span>🔍</span>
      <h3>No public decks found</h3>
      <p>{{ search ? 'Try a different search term.' : 'Be the first to publish a deck! Make one of your decks public from the deck settings.' }}</p>
      <button class="primary-btn" @click="router.push('/decks')">Go to My Decks</button>
    </div>

    <!-- DECK GRID -->
    <div v-else>
      <div class="deck-grid">
        <div v-for="deck in decks" :key="deck.id" class="deck-card">

          <div class="deck-top">
            <div class="deck-icon">📦</div>
            <div class="deck-info">
              <h3 class="deck-title">{{ deck.title }}</h3>
              <p class="deck-desc">{{ deck.description || 'No description' }}</p>
            </div>
          </div>

          <div class="deck-tags">
            <span v-for="tag in deck.tags" :key="tag.id" class="tag">{{ tag.name }}</span>
          </div>

          <div class="deck-meta">
            <span class="meta-item">🃏 {{ deck.cards_count ?? 0 }} cards</span>
            <span class="meta-item">📅 {{ new Date(deck.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
          </div>

          <div class="deck-actions">
            <button
              class="clone-btn"
              :class="{ cloned: clonedIds.includes(deck.id) }"
              :disabled="cloning === deck.id || clonedIds.includes(deck.id)"
              @click="handleClone(deck)"
            >
              <span v-if="cloning === deck.id">Cloning...</span>
              <span v-else-if="clonedIds.includes(deck.id)">✓ Cloned!</span>
              <span v-else>⊕ Clone Deck</span>
            </button>
            <button class="view-btn" @click="router.push(`/explore/${deck.id}`)">Preview</button>
          </div>

        </div>
      </div>

      <!-- PAGINATION -->
      <div class="pagination" v-if="lastPage > 1">
        <button
          class="page-btn"
          :disabled="currentPage === 1"
          @click="fetchDecks(currentPage - 1)"
        >← Prev</button>
        <span class="page-info">Page {{ currentPage }} of {{ lastPage }}</span>
        <button
          class="page-btn"
          :disabled="currentPage === lastPage"
          @click="fetchDecks(currentPage + 1)"
        >Next →</button>
      </div>
    </div>

    <!-- TIP -->
    <div class="tip-card" v-if="!loading">
      <span>💡</span>
      <p>Want your decks to appear here? Go to <strong>My Decks</strong>, edit a deck and toggle <strong>Make Public</strong>.</p>
    </div>

  </div>
</template>

<style scoped>
.page { display: flex; flex-direction: column; gap: 24px; }

.header { display: flex; justify-content: space-between; align-items: flex-start; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 4px; }
.subtitle { font-size: 13px; color: #7aa898; }

.search-bar { display: flex; align-items: center; gap: 12px; background: #0d2420; border: 1px solid rgba(0,229,180,0.1); border-radius: 12px; padding: 12px 16px; }
.search-icon { font-size: 16px; opacity: 0.5; }
.search-bar input { background: none; border: none; outline: none; color: #e8f5f0; font-size: 14px; width: 100%; }
.search-bar input::placeholder { color: #4a7a68; }

.loading { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px 0; color: #7aa898; }
.spinner { width: 32px; height: 32px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.empty { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 80px 0; color: #7aa898; text-align: center; }
.empty span { font-size: 52px; }
.empty h3 { font-size: 20px; font-weight: 700; color: #fff; }
.empty p { font-size: 14px; line-height: 1.6; max-width: 360px; }
.primary-btn { background: #00e5b4; color: #0a1f1c; border: none; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; margin-top: 8px; transition: all 0.2s; }
.primary-btn:hover { background: #00c896; transform: translateY(-1px); }

.deck-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

.deck-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.08); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 14px; transition: all 0.2s; }
.deck-card:hover { border-color: rgba(0,229,180,0.2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

.deck-top { display: flex; gap: 14px; align-items: flex-start; }
.deck-icon { font-size: 24px; width: 44px; height: 44px; background: rgba(0,229,180,0.08); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.deck-title { font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.deck-desc { font-size: 12px; color: #7aa898; line-height: 1.5; }

.deck-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.tag { background: rgba(0,229,180,0.08); border: 1px solid rgba(0,229,180,0.15); color: #00e5b4; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; }

.deck-meta { display: flex; gap: 16px; padding: 10px 0; border-top: 1px solid rgba(0,229,180,0.06); border-bottom: 1px solid rgba(0,229,180,0.06); }
.meta-item { font-size: 12px; color: #7aa898; }

.deck-actions { display: flex; gap: 8px; }
.clone-btn { flex: 1; background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 9px; border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.clone-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,229,180,0.25); }
.clone-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.clone-btn.cloned { background: rgba(0,229,180,0.15); color: #00e5b4; border: 1px solid rgba(0,229,180,0.3); }
.view-btn { background: rgba(0,229,180,0.07); color: #7aa898; border: 1px solid rgba(0,229,180,0.12); padding: 9px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.view-btn:hover { color: #00e5b4; border-color: rgba(0,229,180,0.25); }

.pagination { display: flex; align-items: center; justify-content: center; gap: 20px; padding: 16px 0; }
.page-btn { background: #0d2420; border: 1px solid rgba(0,229,180,0.12); color: #7aa898; padding: 8px 18px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.page-btn:hover:not(:disabled) { color: #00e5b4; border-color: rgba(0,229,180,0.3); }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 13px; color: #7aa898; }

.tip-card { display: flex; align-items: center; gap: 14px; background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.1); border-radius: 12px; padding: 16px 20px; font-size: 13px; color: #7aa898; line-height: 1.6; }
.tip-card span { font-size: 20px; flex-shrink: 0; }
.tip-card strong { color: #00e5b4; }
</style>
