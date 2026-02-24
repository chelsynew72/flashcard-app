<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const deck = ref({
  id: '1',
  title: 'Medical Terminology',
  description: 'Comprehensive list of anatomical terms, suffixes, and prefixes used in medical practice.',
  tags: ['Science', 'Anatomy'],
  nextDue: 'Tomorrow',
  totalCards: 120,
  dueToday: 12,
  mastered: 85,
  learning: 23,
  masteryProgress: 85,
})

const cards = ref([
  {
    id: 'c1',
    question: 'What does -itis mean?',
    answer: 'Inflammation or infection of a specific organ...',
  },
  {
    id: 'c2',
    question: 'What does Cardio- mean?',
    answer: 'Relating to the heart and vascular system...',
  },
  {
    id: 'c3',
    question: 'Definition: Hemato-',
    answer: 'Pertaining to blood or the study of blood cells...',
  },
  {
    id: 'c4',
    question: 'Suffix: -ology',
    answer: 'The study of or science of a particular subject...',
  },
])

const handleBack = () => {
  router.push({ name: 'library' })
}

const handleStudy = () => {
  router.push({ name: 'study', params: { id: deck.value.id } })
}

const handleAddCard = () => {
  console.log('Adding new card')
}

const handleEditCard = (id: string) => {
  console.log('Editing card:', id)
}

const handleDeleteCard = (id: string) => {
  cards.value = cards.value.filter((c) => c.id !== id)
}
</script>

<template>
  <div class="deck-detail-view">
    <div class="container">
      <header class="view-header">
        <button class="back-button" @click="handleBack">
          <span class="icon">←</span>
        </button>
        <h1 class="header-title">Deck Detail</h1>
        <button class="menu-button">⋮</button>
      </header>

      <main class="deck-content">
        <section class="deck-info-card">
          <div class="due-badge">Next card due: {{ deck.nextDue }}</div>
          <h2 class="deck-title">{{ deck.title }}</h2>
          <p class="deck-description">{{ deck.description }}</p>
          <div class="deck-tags">
            <span v-for="tag in deck.tags" :key="tag" class="tag">{{ tag }}</span>
          </div>
        </section>

        <button class="study-button" @click="handleStudy">
          <span class="play-icon">▶</span> Study Now
        </button>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-label">TOTAL CARDS</div>
            <div class="stat-value">{{ deck.totalCards }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">DUE TODAY</div>
            <div class="stat-value highlight">{{ deck.dueToday }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">MASTERED</div>
            <div class="stat-value">
              {{ deck.mastered }} <span class="stat-sub">({{ Math.round((deck.mastered / deck.totalCards) * 100) }}%)</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-label">LEARNING</div>
            <div class="stat-value">{{ deck.learning }}</div>
          </div>
        </div>

        <section class="progress-section">
          <div class="progress-header">
            <span class="progress-label">MASTERY PROGRESS</span>
            <span class="progress-percent">{{ deck.masteryProgress }}%</span>
          </div>
          <div class="progress-bar-bg">
            <div class="progress-bar-fill" :style="{ width: deck.masteryProgress + '%' }"></div>
          </div>
        </section>

        <section class="cards-section">
          <div class="section-header">
            <h3 class="section-title">Cards</h3>
            <button class="add-card-button" @click="handleAddCard">+ Add New</button>
          </div>

          <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Search cards..." class="search-input" />
          </div>

          <div class="cards-list">
            <div v-for="card in cards" :key="card.id" class="card-item">
              <div class="card-content">
                <h4 class="card-question">{{ card.question }}</h4>
                <p class="card-answer">{{ card.answer }}</p>
              </div>
              <div class="card-actions">
                <button class="action-btn" @click="handleEditCard(card.id)">✏️</button>
                <button class="action-btn delete" @click="handleDeleteCard(card.id)">🗑️</button>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<style scoped>
.deck-detail-view {
  background-color: #0a1628;
  min-height: 100vh;
  color: #ffffff;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 24px 16px;
}

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.back-button,
.menu-button {
  background: none;
  border: none;
  color: #ffffff;
  font-size: 24px;
  cursor: pointer;
  padding: 8px;
}

.header-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}

.deck-info-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.2) 0%, rgba(0, 100, 100, 0.1) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 20px;
  position: relative;
}

.due-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  background-color: rgba(0, 255, 213, 0.1);
  color: #00ffd5;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.deck-title {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 16px 0;
}

.deck-description {
  font-size: 15px;
  color: #8a9db5;
  line-height: 1.5;
  margin-bottom: 20px;
}

.deck-tags {
  display: flex;
  gap: 8px;
}

.tag {
  background-color: rgba(0, 255, 213, 0.1);
  color: #00ffd5;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  border: 1px solid rgba(0, 255, 213, 0.2);
}

.study-button {
  width: 100%;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  border: none;
  padding: 18px;
  border-radius: 30px;
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 24px;
  transition: transform 0.2s;
}

.study-button:hover {
  transform: translateY(-2px);
}

.play-icon {
  font-size: 14px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}

.stat-card {
  background-color: rgba(0, 255, 213, 0.05);
  border: 1px solid rgba(0, 255, 213, 0.1);
  border-radius: 12px;
  padding: 16px;
}

.stat-label {
  font-size: 12px;
  color: #8a9db5;
  font-weight: 700;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
}

.stat-value.highlight {
  color: #00ffd5;
}

.stat-sub {
  font-size: 14px;
  color: #8a9db5;
  font-weight: 400;
}

.progress-section {
  margin-bottom: 32px;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

.progress-label {
  font-size: 12px;
  font-weight: 700;
  color: #8a9db5;
}

.progress-percent {
  font-size: 14px;
  font-weight: 700;
  color: #00ffd5;
}

.progress-bar-bg {
  height: 12px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background-color: #00ffd5;
  border-radius: 6px;
  transition: width 0.3s ease;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
}

.add-card-button {
  background-color: rgba(0, 255, 213, 0.1);
  border: 1px solid rgba(0, 255, 213, 0.3);
  color: #00ffd5;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
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
  margin-right: 12px;
  color: #8a9db5;
}

.search-input {
  background: none;
  border: none;
  color: #ffffff;
  font-size: 16px;
  flex: 1;
}

.search-input:focus {
  outline: none;
}

.cards-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.card-item {
  background-color: rgba(0, 255, 213, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-content {
  flex: 1;
}

.card-question {
  font-size: 17px;
  font-weight: 600;
  margin: 0 0 8px 0;
}

.card-answer {
  font-size: 14px;
  color: #8a9db5;
  margin: 0;
}

.card-actions {
  display: flex;
  gap: 12px;
}

.action-btn {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding: 8px;
  opacity: 0.7;
  transition: opacity 0.2s;
}

.action-btn:hover {
  opacity: 1;
}
</style>
