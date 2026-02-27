<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { startSession, nextCard, rateCard, completeSession } from '../../api/study'
import { getDeck } from '../../api/decks'
import type { Card } from '../../types'

const router = useRouter()
const route = useRoute()
const deckId = Number(route.params.id)

const deck = ref<any>(null)
const sessionId = ref<number | null>(null)
const currentCard = ref<Card | null>(null)
const remaining = ref(0)
const total = ref(0)
const flipped = ref(false)
const loading = ref(true)
const finished = ref(false)
const rating = ref(false)

const stats = ref({ again: 0, hard: 0, good: 0, easy: 0 })

onMounted(async () => {
  try {
    const [deckRes, sessionRes] = await Promise.all([
      getDeck(deckId),
      startSession(deckId)
    ])
    deck.value = deckRes.data
    sessionId.value = sessionRes.data.session_id
    total.value = sessionRes.data.total_cards
    await loadNextCard()
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const loadNextCard = async () => {
  flipped.value = false
  rating.value = false
  try {
    const res = await nextCard(sessionId.value!)
    if (res.data.finished) {
      finished.value = true
      await completeSession(sessionId.value!)
    } else {
      currentCard.value = res.data.card
      remaining.value = res.data.remaining ?? remaining.value
    }
  } catch (e) {
    console.error(e)
  }
}

const handleFlip = () => {
  if (!flipped.value) flipped.value = true
}

const handleRate = async (r: 'again' | 'hard' | 'good' | 'easy') => {
  if (!currentCard.value || !sessionId.value || rating.value) return
  rating.value = true
  stats.value[r]++
  try {
    await rateCard(sessionId.value, { card_id: currentCard.value.id, rating: r })
    await loadNextCard()
  } catch (e) {
    console.error(e)
  }
}

const progress = () => {
  if (total.value === 0) return 0
  return Math.round(((total.value - remaining.value) / total.value) * 100)
}
</script>

<template>
  <div class="study-page">

    <!-- LOADING -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Preparing your session...</p>
    </div>

    <!-- FINISHED -->
    <div v-else-if="finished" class="finished">
      <div class="finished-card">
        <div class="finished-icon">🎉</div>
        <h2>Session Complete!</h2>
        <p>Great work! You reviewed all {{ total }} cards in this session.</p>

        <div class="session-stats">
          <div class="session-stat again">
            <span class="s-val">{{ stats.again }}</span>
            <span class="s-lbl">Again</span>
          </div>
          <div class="session-stat hard">
            <span class="s-val">{{ stats.hard }}</span>
            <span class="s-lbl">Hard</span>
          </div>
          <div class="session-stat good">
            <span class="s-val">{{ stats.good }}</span>
            <span class="s-lbl">Good</span>
          </div>
          <div class="session-stat easy">
            <span class="s-val">{{ stats.easy }}</span>
            <span class="s-lbl">Easy</span>
          </div>
        </div>

        <div class="finished-actions">
          <button class="btn-secondary" @click="router.push(`/decks/${deckId}`)">Back to Deck</button>
          <button class="btn-primary" @click="router.push('/dashboard')">Dashboard</button>
        </div>
      </div>
    </div>

    <!-- STUDY -->
    <template v-else-if="currentCard">

      <!-- TOP BAR -->
      <div class="top-bar">
        <button class="exit-btn" @click="router.push(`/decks/${deckId}`)">← Exit</button>
        <div class="deck-name">{{ deck?.title }}</div>
        <div class="card-counter">{{ total - remaining }} / {{ total }}</div>
      </div>

      <!-- PROGRESS -->
      <div class="progress-bar">
        <div class="progress-fill" :style="{ width: progress() + '%' }"></div>
      </div>

      <!-- FLASHCARD -->
      <div class="card-area">
        <div class="flashcard" :class="{ flipped }" @click="handleFlip">
          <div class="card-inner">

            <!-- FRONT -->
            <div class="card-face front">
              <p class="face-label">QUESTION</p>
              <p class="face-text">{{ currentCard.front }}</p>
              <p class="flip-hint" v-if="!flipped">Click to reveal answer</p>
            </div>

            <!-- BACK -->
            <div class="card-face back">
              <p class="face-label">ANSWER</p>
              <p class="face-text">{{ currentCard.back }}</p>
            </div>

          </div>
        </div>
      </div>

      <!-- RATING BUTTONS -->
      <div class="rating-area" :class="{ visible: flipped }">
        <p class="rating-label">How well did you know this?</p>
        <div class="rating-btns">
          <button class="r-btn again" @click="handleRate('again')" :disabled="!flipped || rating">
            <span class="r-icon">😞</span>
            <span class="r-name">Again</span>
            <span class="r-desc">Didn't know</span>
          </button>
          <button class="r-btn hard" @click="handleRate('hard')" :disabled="!flipped || rating">
            <span class="r-icon">😕</span>
            <span class="r-name">Hard</span>
            <span class="r-desc">Struggled</span>
          </button>
          <button class="r-btn good" @click="handleRate('good')" :disabled="!flipped || rating">
            <span class="r-icon">🙂</span>
            <span class="r-name">Good</span>
            <span class="r-desc">Got it</span>
          </button>
          <button class="r-btn easy" @click="handleRate('easy')" :disabled="!flipped || rating">
            <span class="r-icon">😄</span>
            <span class="r-name">Easy</span>
            <span class="r-desc">Perfect</span>
          </button>
        </div>
      </div>

    </template>

  </div>
</template>

<style scoped>
.study-page {
  min-height: calc(100vh - 80px);
  display: flex;
  flex-direction: column;
  gap: 0;
}

/* Loading */
.loading { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; gap: 16px; color: #7aa898; min-height: 400px; }
.spinner { width: 36px; height: 36px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Top bar */
.top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.exit-btn { background: none; border: none; color: #7aa898; font-size: 13px; cursor: pointer; transition: color 0.2s; padding: 0; }
.exit-btn:hover { color: #00e5b4; }
.deck-name { font-size: 14px; font-weight: 600; color: #c8e8d8; }
.card-counter { font-size: 13px; color: #7aa898; font-weight: 600; }

/* Progress */
.progress-bar { height: 6px; background: rgba(0,229,180,0.1); border-radius: 999px; margin-bottom: 32px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #00e5b4, #00c896); border-radius: 999px; transition: width 0.5s ease; }

/* Card area */
.card-area { display: flex; justify-content: center; align-items: center; flex: 1; perspective: 1200px; margin-bottom: 32px; }

.flashcard {
  width: 100%;
  max-width: 680px;
  height: 340px;
  cursor: pointer;
  transform-style: preserve-3d;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.flashcard.flipped { transform: rotateY(180deg); }

.card-inner {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
}

.card-face {
  position: absolute;
  inset: 0;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  border-radius: 20px;
  padding: 48px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.card-face.front {
  background: linear-gradient(135deg, #0d2a24 0%, #0f2e28 100%);
  border: 1px solid rgba(0,229,180,0.15);
  box-shadow: 0 24px 60px rgba(0,0,0,0.4);
}

.card-face.back {
  background: linear-gradient(135deg, #102820 0%, #0d2420 100%);
  border: 1px solid rgba(0,229,180,0.25);
  box-shadow: 0 24px 60px rgba(0,0,0,0.4);
  transform: rotateY(180deg);
}

.face-label {
  font-size: 11px;
  letter-spacing: 2px;
  color: #00e5b4;
  font-weight: 700;
  margin-bottom: 20px;
}

.face-text {
  font-size: 22px;
  font-weight: 600;
  color: #ffffff;
  line-height: 1.5;
  max-width: 480px;
}

.flip-hint {
  position: absolute;
  bottom: 24px;
  font-size: 12px;
  color: #4a7a68;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* Rating */
.rating-area {
  opacity: 0;
  transform: translateY(12px);
  transition: all 0.4s ease;
  pointer-events: none;
  text-align: center;
}

.rating-area.visible {
  opacity: 1;
  transform: translateY(0);
  pointer-events: all;
}

.rating-label { font-size: 13px; color: #7aa898; margin-bottom: 16px; font-weight: 500; }

.rating-btns { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 680px; margin: 0 auto; }

.r-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 16px 12px;
  border-radius: 14px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  background: #0d2420;
  border: 1px solid transparent;
}

.r-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.r-btn.again { border-color: rgba(255,100,100,0.2); }
.r-btn.again:hover:not(:disabled) { background: rgba(255,100,100,0.12); border-color: rgba(255,100,100,0.4); transform: translateY(-3px); }

.r-btn.hard { border-color: rgba(255,165,0,0.2); }
.r-btn.hard:hover:not(:disabled) { background: rgba(255,165,0,0.1); border-color: rgba(255,165,0,0.4); transform: translateY(-3px); }

.r-btn.good { border-color: rgba(0,229,180,0.2); }
.r-btn.good:hover:not(:disabled) { background: rgba(0,229,180,0.1); border-color: rgba(0,229,180,0.4); transform: translateY(-3px); }

.r-btn.easy { border-color: rgba(100,149,237,0.2); }
.r-btn.easy:hover:not(:disabled) { background: rgba(100,149,237,0.1); border-color: rgba(100,149,237,0.4); transform: translateY(-3px); }

.r-icon { font-size: 24px; }
.r-name { font-size: 14px; font-weight: 700; color: #fff; }
.r-desc { font-size: 11px; color: #7aa898; }

.r-btn.again .r-name { color: #ff6464; }
.r-btn.hard .r-name { color: #ffa500; }
.r-btn.good .r-name { color: #00e5b4; }
.r-btn.easy .r-name { color: #6495ed; }

/* Finished */
.finished { display: flex; align-items: center; justify-content: center; flex: 1; min-height: 500px; }
.finished-card {
  background: #0d2420;
  border: 1px solid rgba(0,229,180,0.15);
  border-radius: 24px;
  padding: 56px 48px;
  text-align: center;
  max-width: 520px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}
.finished-icon { font-size: 64px; }
.finished-card h2 { font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.finished-card p { font-size: 15px; color: #7aa898; line-height: 1.6; }

.session-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; width: 100%; margin: 8px 0; }
.session-stat { border-radius: 12px; padding: 16px 8px; border: 1px solid transparent; }
.session-stat.again { background: rgba(255,100,100,0.08); border-color: rgba(255,100,100,0.15); }
.session-stat.hard { background: rgba(255,165,0,0.08); border-color: rgba(255,165,0,0.15); }
.session-stat.good { background: rgba(0,229,180,0.08); border-color: rgba(0,229,180,0.15); }
.session-stat.easy { background: rgba(100,149,237,0.08); border-color: rgba(100,149,237,0.15); }
.s-val { display: block; font-size: 28px; font-weight: 800; color: #fff; margin-bottom: 4px; }
.s-lbl { font-size: 12px; color: #7aa898; font-weight: 500; }
.session-stat.again .s-lbl { color: #ff6464; }
.session-stat.hard .s-lbl { color: #ffa500; }
.session-stat.good .s-lbl { color: #00e5b4; }
.session-stat.easy .s-lbl { color: #6495ed; }

.finished-actions { display: flex; gap: 12px; margin-top: 8px; }
.btn-primary { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,229,180,0.25); }
.btn-secondary { background: rgba(0,229,180,0.07); color: #7aa898; border: 1px solid rgba(0,229,180,0.15); padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-secondary:hover { color: #00e5b4; border-color: rgba(0,229,180,0.3); }
</style>
