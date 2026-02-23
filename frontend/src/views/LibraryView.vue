<script setup lang="ts">
import { ref, computed } from 'vue'
import DeckCard from '../components/DeckCard.vue'

interface Deck {
  id: string
  title: string
  description: string
  tags: string[]
  progress: number
  cardsDue: number
  urgent?: boolean
}

const searchQuery = ref('')
const activeFilter = ref('all')

const decks = ref<Deck[]>([
  {
    id: '1',
    title: 'Medical Terminology',
    description: 'Comprehensive list of anatomical terms, suffixes, and prefixes used i...',
    tags: ['Science', 'Anatomy'],
    progress: 85,
    cardsDue: 12,
    urgent: true,
  },
  {
    id: '2',
    title: 'Japanese Kanji N5',
    description: 'The first 100 characters for basic communication and JLPT N5...',
    tags: ['Language', 'JLPT N5'],
    progress: 20,
    cardsDue: 45,
  },
  {
    id: '3',
    title: 'UI Design Principles',
    description: 'Typography, color theory, and layout fundamentals for modern interfaces.',
    tags: ['Design', 'Theory'],
    progress: 50,
    cardsDue: 0,
  },
])

const filters = [
  { id: 'all', label: 'All Decks', icon: '📚' },
  { id: 'due', label: 'Due Soon', icon: '⏰', badge: 12 },
  { id: 'progress', label: 'Progress', icon: '📊' },
]

const filteredDecks = computed(() => {
  let result = decks.value

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(
      (deck) =>
        deck.title.toLowerCase().includes(query) ||
        deck.description.toLowerCase().includes(query) ||
        deck.tags.some((tag) => tag.toLowerCase().includes(query)),
    )
  }

  if (activeFilter.value === 'due') {
    result = result.filter((deck) => deck.cardsDue > 0)
  } else if (activeFilter.value === 'progress') {
    result = result.sort((a, b) => b.progress - a.progress)
  }

  return result
})

const handleStudy = (deckId: string) => {
  console.log('Study deck:', deckId)
}

const handleReview = (deckId: string) => {
  console.log('Review deck:', deckId)
}

const handleAddDeck = () => {
  console.log('Add new deck')
}
</script>

<template>
  <div class="library-view">
    <header class="library-header">
      <div class="header-top">
        <h1 class="header-title">My Decks</h1>
        <button class="add-button" @click="handleAddDeck">➕</button>
      </div>

      <div class="search-bar">
        <span class="search-icon">🔍</span>
        <input
          v-model="searchQuery"
          type="text"
          class="search-input"
          placeholder="Search your library..."
        />
      </div>

      <div class="filters">
        <button
          v-for="filter in filters"
          :key="filter.id"
          class="filter-button"
          :class="{ active: activeFilter === filter.id }"
          @click="activeFilter = filter.id"
        >
          {{ filter.label }}
          <span v-if="filter.badge" class="badge">{{ filter.badge }}</span>
        </button>
      </div>
    </header>

    <main class="library-content">
      <div v-if="filteredDecks.length > 0" class="decks-list">
        <DeckCard
          v-for="deck in filteredDecks"
          :key="deck.id"
          :id="deck.id"
          :title="deck.title"
          :description="deck.description"
          :tags="deck.tags"
          :progress="deck.progress"
          :cards-due="deck.cardsDue"
          :urgent="deck.urgent"
          @study="handleStudy"
          @review="handleReview"
        />
      </div>
      <div v-else class="empty-state">
        <p class="empty-icon">📭</p>
        <p class="empty-message">No decks found</p>
        <p class="empty-hint">Try adjusting your search or filters</p>
      </div>
    </main>
  </div>
</template>

<style scoped>
.library-view {
  padding: 24px 16px 0;
}

.library-header {
  margin-bottom: 24px;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header-title {
  font-size: 32px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.5px;
}

.add-button {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  border: none;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.add-button:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 16px rgba(0, 255, 213, 0.3);
}

.add-button:active {
  transform: scale(0.98);
}

.search-bar {
  display: flex;
  align-items: center;
  background-color: rgba(0, 255, 213, 0.05);
  border: 1px solid rgba(0, 255, 213, 0.1);
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 16px;
}

.search-icon {
  font-size: 18px;
  margin-right: 12px;
  color: #7a8fa3;
}

.search-input {
  flex: 1;
  background: none;
  border: none;
  color: #ffffff;
  font-size: 16px;
  outline: none;
}

.search-input::placeholder {
  color: #5a6f8a;
}

.filters {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  scrollbar-width: none;
}

.filters::-webkit-scrollbar {
  display: none;
}

.filter-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: none;
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 20px;
  color: #8a9db5;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.filter-button:hover {
  border-color: rgba(0, 255, 213, 0.4);
  color: #a0b0c0;
}

.filter-button.active {
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  border-color: #00ffd5;
  color: #0a1628;
}

.badge {
  background-color: rgba(0, 255, 213, 0.3);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.filter-button.active .badge {
  background-color: rgba(10, 22, 40, 0.3);
}

.library-content {
  padding-bottom: 20px;
}

.decks-list {
  display: flex;
  flex-direction: column;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.empty-message {
  font-size: 18px;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 8px;
}

.empty-hint {
  font-size: 14px;
  color: #8a9db5;
}
</style>
