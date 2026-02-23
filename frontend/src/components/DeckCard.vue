<script setup lang="ts">
interface Props {
  id: string
  title: string
  description: string
  tags: string[]
  progress: number
  cardsDue: number
  urgent?: boolean
}

withDefaults(defineProps<Props>(), {
  urgent: false,
})

const emit = defineEmits<{
  study: [id: string]
  review: [id: string]
}>()

const getButtonText = (cardsDue: number) => {
  return cardsDue > 0 ? 'Study' : 'Review'
}

const handleAction = (deckId: string, cardsDue: number) => {
  if (cardsDue > 0) {
    emit('study', deckId)
  } else {
    emit('review', deckId)
  }
}
</script>

<template>
  <div class="deck-card">
    <div class="card-header">
      <div class="title-section">
        <h3 class="deck-title">{{ title }}</h3>
        <p class="deck-description">{{ description }}</p>
      </div>
      <div class="progress-circle">
        <svg class="circle-svg" viewBox="0 0 100 100">
          <circle class="circle-bg" cx="50" cy="50" r="45" />
          <circle
            class="circle-progress"
            cx="50"
            cy="50"
            r="45"
            :style="{
              strokeDasharray: `${(progress / 100) * 283} 283`,
            }"
          />
        </svg>
        <span class="progress-text">{{ progress }}%</span>
      </div>
    </div>

    <div v-if="urgent" class="urgent-badge">URGENT</div>

    <div class="card-tags">
      <span v-for="tag in tags" :key="tag" class="tag">{{ tag }}</span>
    </div>

    <div class="card-footer">
      <div class="cards-due">
        <span class="calendar-icon">📅</span>
        <span class="due-text">{{ cardsDue }} cards due {{ cardsDue === 0 ? '' : 'today' }}</span>
      </div>
      <button
        class="action-button"
        @click="handleAction(id, cardsDue)"
      >
        <span>{{ getButtonText(cardsDue) }}</span>
        <span class="arrow">→</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.deck-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 0%, rgba(0, 100, 100, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  position: relative;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 16px;
}

.title-section {
  flex: 1;
}

.deck-title {
  font-size: 20px;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 8px;
}

.deck-description {
  font-size: 13px;
  color: #8a9db5;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.progress-circle {
  position: relative;
  width: 90px;
  height: 90px;
  flex-shrink: 0;
}

.circle-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.circle-bg {
  fill: none;
  stroke: rgba(0, 255, 213, 0.1);
  stroke-width: 5;
}

.circle-progress {
  fill: none;
  stroke: #00ffd5;
  stroke-width: 5;
  stroke-linecap: round;
  transition: stroke-dasharray 0.3s ease;
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 20px;
  font-weight: 600;
  color: #00ffd5;
}

.urgent-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  background-color: rgba(255, 128, 0, 0.2);
  color: #ff8000;
  padding: 4px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.card-tags {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.tag {
  display: inline-block;
  background-color: rgba(0, 255, 213, 0.1);
  color: #00ffd5;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(0, 255, 213, 0.1);
  padding-top: 16px;
}

.cards-due {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #8a9db5;
}

.calendar-icon {
  font-size: 16px;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  border: none;
  padding: 10px 20px;
  border-radius: 24px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  white-space: nowrap;
}

.action-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 255, 213, 0.3);
}

.action-button:active {
  transform: translateY(0);
}

.arrow {
  font-size: 16px;
}
</style>
