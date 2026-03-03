<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { startSession, nextCard, rateCard, completeSession } from '../../api/study'
import { getDeck } from '../../api/decks'
import api from '../../api'
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

// Per-card fail tracking for hints
const cardFailCounts = ref<Record<number, number>>({})
const hint = ref('')
const hintLoading = ref(false)
const showHint = ref(false)

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
  hint.value = ''
  showHint.value = false
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

const handleFlip = () => { if (!flipped.value) flipped.value = true }

const handleRate = async (r: 'again' | 'hard' | 'good' | 'easy') => {
  if (!currentCard.value || !sessionId.value || rating.value) return
  rating.value = true

  if (r === 'again') {
    const id = currentCard.value.id
    cardFailCounts.value[id] = (cardFailCounts.value[id] || 0) + 1
  }

  stats.value[r]++
  try {
    await rateCard(sessionId.value, { card_id: currentCard.value.id, rating: r })
    await loadNextCard()
  } catch (e) {
    console.error(e)
  }
}

const failCount = () => currentCard.value ? (cardFailCounts.value[currentCard.value.id] || 0) : 0

const getHint = async () => {
  if (!currentCard.value || hintLoading.value) return
  hintLoading.value = true
  hint.value = ''
  showHint.value = true
  try {
    const res = await api.post('/ai/hint', {
      front: currentCard.value.front,
      back: currentCard.value.back,
    })
    hint.value = res.data.hint
  } catch (e) {
    hint.value = 'Could not load hint. Try again.'
  } finally {
    hintLoading.value = false
  }
}

const progress = () => total.value === 0 ? 0 : Math.round(((total.value - remaining.value) / total.value) * 100)

// Keyboard shortcuts
const handleKey = (e: KeyboardEvent) => {
  if (e.code === 'Space') { e.preventDefault(); handleFlip() }
  if (!flipped.value) return
  if (e.key === '1') handleRate('again')
  if (e.key === '2') handleRate('hard')
  if (e.key === '3') handleRate('good')
  if (e.key === '4') handleRate('easy')
}

onMounted(() => window.addEventListener('keydown', handleKey))
import { onUnmounted } from 'vue'
onUnmounted(() => window.removeEventListener('keydown', handleKey))
</script>

<template>
  <div class="study-page">

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Preparing your session...</p>
    </div>

    <div v-else-if="finished" class="finished">
      <div class="finished-card">
        <div class="finished-icon">🎉</div>
        <h2>Session Complete!</h2>
        <p>Great work! You reviewed all {{ total }} cards in this session.</p>
        <div class="session-stats">
          <div class="session-stat again"><span class="s-val">{{ stats.again }}</span><span class="s-lbl">Again</span></div>
          <div class="session-stat hard"><span class="s-val">{{ stats.hard }}</span><span class="s-lbl">Hard</span></div>
          <div class="session-stat good"><span class="s-val">{{ stats.good }}</span><span class="s-lbl">Good</span></div>
          <div class="session-stat easy"><span class="s-val">{{ stats.easy }}</span><span class="s-lbl">Easy</span></div>
        </div>
        <div class="finished-actions">
          <button class="btn-secondary" @click="router.push(`/decks/${deckId}`)">Back to Deck</button>
          <button class="btn-primary" @click="router.push('/dashboard')">Dashboard</button>
        </div>
      </div>
    </div>

    <template v-else-if="currentCard">

      <div class="top-bar">
        <button class="exit-btn" @click="router.push(`/decks/${deckId}`)">← Exit</button>
        <div class="deck-name">{{ deck?.title }}</div>
        <div class="card-counter">{{ total - remaining }} / {{ total }}</div>
      </div>

      <div class="progress-bar">
        <div class="progress-fill" :style="{ width: progress() + '%' }"></div>
      </div>

      <!-- KEYBOARD HINT -->
      <div class="keyboard-hint">
        <span>⌨️ <kbd>Space</kbd> flip &nbsp;·&nbsp; <kbd>1</kbd> Again &nbsp;·&nbsp; <kbd>2</kbd> Hard &nbsp;·&nbsp; <kbd>3</kbd> Good &nbsp;·&nbsp; <kbd>4</kbd> Easy</span>
      </div>

      <div class="card-area">
        <div class="flashcard" :class="{ flipped }" @click="handleFlip">
          <div class="card-inner">
            <div class="card-face front">
              <p class="face-label">QUESTION</p>
              <p class="face-text">{{ currentCard.front }}</p>
              <p class="flip-hint" v-if="!flipped">Click or press Space to reveal</p>
            </div>
            <div class="card-face back">
              <p class="face-label">ANSWER</p>
              <p class="face-text">{{ currentCard.back }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- SMART HINT -->
      <div class="hint-area" v-if="failCount() >= 3 && !flipped">
        <div class="hint-trigger" v-if="!showHint">
          <span class="hint-badge">🤖 AI</span>
          <p>You've missed this card {{ failCount() }} times.</p>
          <button class="hint-btn" @click="getHint" :disabled="hintLoading">
            {{ hintLoading ? '✨ Thinking...' : '✨ Get AI Hint' }}
          </button>
        </div>
        <div class="hint-box" v-else>
          <div class="hint-header">
            <span>✨ AI Hint</span>
            <span class="hint-fail-count">Failed {{ failCount() }}×</span>
          </div>
          <p class="hint-text" v-if="!hintLoading">{{ hint }}</p>
          <div class="hint-loading" v-else>
            <div class="spinner-sm"></div>
            <p>Generating hint...</p>
          </div>
        </div>
      </div>

      <div class="rating-area" :class="{ visible: flipped }">
        <p class="rating-label">How well did you know this? &nbsp;<span class="key-labels">1 · 2 · 3 · 4</span></p>
        <div class="rating-btns">
          <button class="r-btn again" @click="handleRate('again')" :disabled="!flipped || rating">
            <span class="r-icon">😞</span><span class="r-name">Again</span><span class="r-desc">Didn't know</span>
          </button>
          <button class="r-btn hard" @click="handleRate('hard')" :disabled="!flipped || rating">
            <span class="r-icon">😕</span><span class="r-name">Hard</span><span class="r-desc">Struggled</span>
          </button>
          <button class="r-btn good" @click="handleRate('good')" :disabled="!flipped || rating">
            <span class="r-icon">🙂</span><span class="r-name">Good</span><span class="r-desc">Got it</span>
          </button>
          <button class="r-btn easy" @click="handleRate('easy')" :disabled="!flipped || rating">
            <span class="r-icon">😄</span><span class="r-name">Easy</span><span class="r-desc">Perfect</span>
          </button>
        </div>
      </div>

    </template>
  </div>
</template>

<style scoped>
.study-page { min-height: calc(100vh - 80px); display: flex; flex-direction: column; gap: 0; }

.loading { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; gap: 16px; color: #7aa898; min-height: 400px; }
.spinner { width: 36px; height: 36px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.exit-btn { background: none; border: none; color: #7aa898; font-size: 13px; cursor: pointer; transition: color 0.2s; padding: 0; }
.exit-btn:hover { color: #00e5b4; }
.deck-name { font-size: 14px; font-weight: 600; color: #c8e8d8; }
.card-counter { font-size: 13px; color: #7aa898; font-weight: 600; }

.progress-bar { height: 6px; background: rgba(0,229,180,0.1); border-radius: 999px; margin-bottom: 16px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #00e5b4, #00c896); border-radius: 999px; transition: width 0.5s ease; }

.keyboard-hint { text-align: center; font-size: 12px; color: #4a7a68; margin-bottom: 20px; }
kbd { background: rgba(0,229,180,0.1); border: 1px solid rgba(0,229,180,0.2); border-radius: 4px; padding: 1px 6px; font-size: 11px; color: #7aa898; font-family: inherit; }

.card-area { display: flex; justify-content: center; align-items: center; perspective: 1200px; margin-bottom: 20px; }
.flashcard { width: 100%; max-width: 680px; height: 300px; cursor: pointer; transform-style: preserve-3d; transition: transform 0.6s cubic-bezier(0.4,0,0.2,1); position: relative; }
.flashcard.flipped { transform: rotateY(180deg); }
.card-inner { width: 100%; height: 100%; position: relative; transform-style: preserve-3d; }
.card-face { position: absolute; inset: 0; backface-visibility: hidden; -webkit-backface-visibility: hidden; border-radius: 20px; padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
.card-face.front { background: linear-gradient(135deg, #0d2a24, #0f2e28); border: 1px solid rgba(0,229,180,0.15); box-shadow: 0 24px 60px rgba(0,0,0,0.4); }
.card-face.back { background: linear-gradient(135deg, #102820, #0d2420); border: 1px solid rgba(0,229,180,0.25); box-shadow: 0 24px 60px rgba(0,0,0,0.4); transform: rotateY(180deg); }
.face-label { font-size: 11px; letter-spacing: 2px; color: #00e5b4; font-weight: 700; margin-bottom: 16px; }
.face-text { font-size: 22px; font-weight: 600; color: #fff; line-height: 1.5; max-width: 480px; }
.flip-hint { position: absolute; bottom: 20px; font-size: 12px; color: #4a7a68; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

/* SMART HINT */
.hint-area { max-width: 680px; margin: 0 auto 16px; width: 100%; }
.hint-trigger { display: flex; align-items: center; gap: 12px; background: rgba(255,165,0,0.06); border: 1px solid rgba(255,165,0,0.2); border-radius: 12px; padding: 14px 18px; }
.hint-badge { background: linear-gradient(135deg, #ffa500, #ff8c00); color: #0a1f1c; padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 800; white-space: nowrap; }
.hint-trigger p { flex: 1; font-size: 13px; color: #ffa500; }
.hint-btn { background: linear-gradient(135deg, #ffa500, #ff8c00); color: #0a1f1c; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; white-space: nowrap; transition: all 0.2s; }
.hint-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(255,165,0,0.3); }
.hint-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.hint-box { background: rgba(255,165,0,0.06); border: 1px solid rgba(255,165,0,0.25); border-radius: 12px; padding: 18px; }
.hint-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13px; font-weight: 700; color: #ffa500; }
.hint-fail-count { font-size: 11px; color: #ff6464; background: rgba(255,100,100,0.1); padding: 2px 8px; border-radius: 999px; }
.hint-text { font-size: 14px; color: #e8f5f0; line-height: 1.7; }
.hint-loading { display: flex; align-items: center; gap: 10px; color: #7aa898; font-size: 13px; }
.spinner-sm { width: 16px; height: 16px; border: 2px solid rgba(255,165,0,0.2); border-top-color: #ffa500; border-radius: 50%; animation: spin 0.7s linear infinite; }

.rating-area { opacity: 0; transform: translateY(12px); transition: all 0.4s ease; pointer-events: none; text-align: center; }
.rating-area.visible { opacity: 1; transform: translateY(0); pointer-events: all; }
.rating-label { font-size: 13px; color: #7aa898; margin-bottom: 16px; font-weight: 500; }
.key-labels { color: #4a7a68; font-size: 12px; }
.rating-btns { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 680px; margin: 0 auto; }
.r-btn { display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 16px 12px; border-radius: 14px; border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; background: #0d2420; }
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

.finished { display: flex; align-items: center; justify-content: center; flex: 1; min-height: 500px; }
.finished-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.15); border-radius: 24px; padding: 56px 48px; text-align: center; max-width: 520px; width: 100%; display: flex; flex-direction: column; align-items: center; gap: 16px; }
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
