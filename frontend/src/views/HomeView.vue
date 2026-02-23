<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import DeckCard from '../components/DeckCard.vue'

const router = useRouter()

const userName = ref('Alex')
const currentDate = ref(new Date())
const notificationCount = ref(1)

const formattedDate = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'short',
    day: 'numeric',
  })
})

const dailyGoal = {
  cardsToday: 15,
  message: "You're on a roll! Finish today's review to maintain your streak and reach your weekly goal.",
}

const streak = {
  days: 7,
  status: 'Top 10% of learners this week!',
}

const stats = [
  { label: 'DECKS', value: 12 },
  { label: 'TOTAL CARDS', value: 450 },
  { label: 'LEARNED', value: 320 },
  { label: 'MASTERED', value: 180 },
]

const continueLearning = [
  {
    id: '1',
    title: 'Spanish Vocabulary',
    description: '120 cards • Intermediate',
    tags: ['ACTIVE'],
    progress: 65,
    cardsDue: 8,
  },
  {
    id: '2',
    title: 'Medical Terminology',
    description: '240 cards • Advanced',
    tags: ['REVIEW'],
    progress: 85,
    cardsDue: 0,
  },
]

const handleStartReview = () => {
  if (continueLearning[0]) {
    router.push({ name: 'study', params: { id: continueLearning[0].id } })
  }
}

const handleStudy = (deckId: string) => {
  router.push({ name: 'study', params: { id: deckId } })
}

const handleReview = (deckId: string) => {
  router.push({ name: 'study', params: { id: deckId } })
}

const handleViewCalendar = () => {
  console.log('View calendar')
}

const handleSeeAll = () => {
  router.push({ name: 'library' })
}

const handleNotifications = () => {
  console.log('View notifications')
}
</script>

<template>
  <div class="home-view">
    <header class="home-header">
      <div class="header-top">
        <h1 class="greeting">
          Good morning, {{ userName }}
          <span class="wave">👋</span>
        </h1>
        <button class="notification-button" @click="handleNotifications">
          🔔
          <span v-if="notificationCount > 0" class="notification-badge">{{ notificationCount }}</span>
        </button>
      </div>
      <p class="current-date">{{ formattedDate }}</p>
    </header>

    <main class="home-content">
      <section class="daily-goal-section">
        <div class="daily-goal-card">
          <div class="goal-header">
            <h3 class="goal-title">DAILY GOAL</h3>
            <div class="goal-icon">📚</div>
          </div>
          <div class="goal-content">
            <div class="goal-number">
              <span class="number">{{ dailyGoal.cardsToday }}</span>
              <span class="label">Cards Due Today</span>
            </div>
            <p class="goal-message">{{ dailyGoal.message }}</p>
          </div>
          <button class="btn-action btn-primary" @click="handleStartReview">
            Start Today's Review
            <span class="arrow">→</span>
          </button>
        </div>
      </section>

      <section class="streak-section">
        <div class="streak-card">
          <div class="streak-content">
            <div class="streak-icon">🔥</div>
            <div class="streak-info">
              <div class="streak-number">{{ streak.days }} day streak</div>
              <div class="streak-status">{{ streak.status }}</div>
            </div>
          </div>
          <button class="btn-action btn-secondary" @click="handleViewCalendar">
            View Calendar
            <span class="arrow">→</span>
          </button>
        </div>
      </section>

      <section class="stats-section">
        <div class="stats-grid">
          <div v-for="stat in stats" :key="stat.label" class="stat-card">
            <div class="stat-label">{{ stat.label }}</div>
            <div class="stat-value">{{ stat.value }}</div>
          </div>
        </div>
      </section>

      <section class="continue-learning-section">
        <div class="section-header">
          <h3 class="section-title">Continue Studying</h3>
          <button class="see-all-button" @click="handleSeeAll">See All</button>
        </div>

        <div class="decks-list">
          <DeckCard
            v-for="deck in continueLearning"
            :key="deck.id"
            :id="deck.id"
            :title="deck.title"
            :description="deck.description"
            :tags="deck.tags"
            :progress="deck.progress"
            :cards-due="deck.cardsDue"
            @study="handleStudy"
            @review="handleReview"
          />
        </div>
      </section>
    </main>
  </div>
</template>

<style scoped>
.home-view {
  padding: 24px 16px 0;
}

.home-header {
  margin-bottom: 24px;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 8px;
}

.greeting {
  font-size: 28px;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.wave {
  font-size: 32px;
  display: inline-block;
  animation: wave 1s ease-in-out;
}

@keyframes wave {
  0%,
  100% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(20deg);
  }
  75% {
    transform: rotate(-20deg);
  }
}

.notification-button {
  position: relative;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(0, 255, 213, 0.1);
  border: 1px solid rgba(0, 255, 213, 0.2);
  font-size: 24px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-button:hover {
  background: rgba(0, 255, 213, 0.15);
  border-color: rgba(0, 255, 213, 0.4);
}

.notification-badge {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 20px;
  height: 20px;
  background: #ff6464;
  color: #ffffff;
  border-radius: 50%;
  font-size: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
}

.current-date {
  color: #8a9db5;
  font-size: 14px;
  margin: 0;
}

.home-content {
  padding-bottom: 20px;
}

.daily-goal-section {
  margin-bottom: 20px;
}

.daily-goal-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.15) 0%, rgba(0, 100, 100, 0.08) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 16px;
  padding: 24px;
}

.goal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.goal-title {
  font-size: 12px;
  color: #8a9db5;
  margin: 0;
  font-weight: 600;
  letter-spacing: 1px;
}

.goal-icon {
  font-size: 32px;
}

.goal-content {
  margin-bottom: 20px;
}

.goal-number {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 12px;
}

.number {
  font-size: 36px;
  font-weight: 700;
  color: #00ffd5;
}

.label {
  color: #8a9db5;
  font-size: 14px;
}

.goal-message {
  color: #a0b0c0;
  font-size: 14px;
  line-height: 1.6;
  margin: 0;
}

.streak-section {
  margin-bottom: 20px;
}

.streak-card {
  background: linear-gradient(135deg, rgba(255, 128, 0, 0.1) 0%, rgba(200, 100, 0, 0.05) 100%);
  border: 1px solid rgba(255, 128, 0, 0.2);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.streak-content {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
}

.streak-icon {
  font-size: 48px;
}

.streak-info {
  display: flex;
  flex-direction: column;
}

.streak-number {
  font-size: 18px;
  font-weight: 600;
  color: #ffffff;
}

.streak-status {
  font-size: 12px;
  color: #ff8000;
  font-weight: 500;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  white-space: nowrap;
}

.btn-primary {
  width: 100%;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  justify-content: center;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 255, 213, 0.3);
}

.btn-secondary {
  background: rgba(255, 128, 0, 0.2);
  color: #ff8000;
  border: 1px solid rgba(255, 128, 0, 0.3);
}

.btn-secondary:hover {
  background: rgba(255, 128, 0, 0.3);
  border-color: rgba(255, 128, 0, 0.5);
}

.arrow {
  font-size: 16px;
}

.stats-section {
  margin-bottom: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.stat-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 0%, rgba(0, 100, 100, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.15);
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #8a9db5;
  font-weight: 600;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #ffffff;
}

.continue-learning-section {
  margin-bottom: 20px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
}

.see-all-button {
  background: none;
  border: none;
  color: #8a9db5;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.2s ease;
}

.see-all-button:hover {
  color: #00ffd5;
}

.decks-list {
  display: flex;
  flex-direction: column;
}
</style>
