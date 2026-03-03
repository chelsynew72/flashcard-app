<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getDecks, createDeck } from '../api/decks'
import { createCard } from '../api/cards'
import api from '../api'
import type { Deck } from '../types'

const router = useRouter()

const topic = ref('')
const numCards = ref(10)
const difficulty = ref<'beginner' | 'intermediate' | 'advanced'>('intermediate')
const selectedDeckId = ref<number | null>(null)
const newDeckName = ref('')
const decks = ref<Deck[]>([])
const deckChoice = ref<'existing' | 'new'>('new')

const loading = ref(false)
const saving = ref(false)
const error = ref('')
const saveMsg = ref('')

interface GeneratedCard { front: string; back: string }
const generatedCards = ref<GeneratedCard[]>([])
const selectedCards = ref<Set<number>>(new Set())

onMounted(async () => {
  try {
    const res = await getDecks()
    decks.value = res.data
    if (decks.value.length > 0) selectedDeckId.value = decks.value[0]?.id ?? null
  } catch (e) { console.error(e) }
})

const generate = async () => {
  if (!topic.value.trim()) { error.value = 'Please enter a topic.'; return }
  loading.value = true
  error.value = ''
  generatedCards.value = []
  selectedCards.value = new Set()

  try {
    const res = await api.post('/ai/generate', {
      topic: topic.value,
      num_cards: numCards.value,
      difficulty: difficulty.value,
    })
    generatedCards.value = res.data.cards
    selectedCards.value = new Set(res.data.cards.map((_: any, i: number) => i))
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to generate cards. Please try again.'
  } finally {
    loading.value = false
  }
}

const toggleCard = (i: number) => {
  if (selectedCards.value.has(i)) selectedCards.value.delete(i)
  else selectedCards.value.add(i)
  selectedCards.value = new Set(selectedCards.value)
}

const toggleAll = () => {
  if (selectedCards.value.size === generatedCards.value.length)
    selectedCards.value = new Set()
  else
    selectedCards.value = new Set(generatedCards.value.map((_, i) => i))
}

const saveCards = async () => {
  if (selectedCards.value.size === 0) { error.value = 'Select at least one card to save.'; return }
  saving.value = true
  saveMsg.value = ''
  error.value = ''

  try {
    let deckId = selectedDeckId.value

    if (deckChoice.value === 'new') {
      if (!newDeckName.value.trim()) { error.value = 'Enter a name for the new deck.'; saving.value = false; return }
      const res = await createDeck({
        title: newDeckName.value,
        description: `AI-generated deck about ${topic.value}`,
        is_public: false,
        tags: [topic.value.toLowerCase().replace(/\s+/g, '-'), 'ai-generated']
      })
      deckId = res.data.id
    }

    if (!deckId) { error.value = 'Please select a deck.'; saving.value = false; return }

    const toSave = generatedCards.value.filter((_, i) => selectedCards.value.has(i))
    await Promise.all(toSave.map(card => createCard(deckId!, { front: card.front, back: card.back })))

    saveMsg.value = `✓ ${toSave.length} cards saved successfully!`
    setTimeout(() => router.push(`/decks/${deckId}`), 1500)
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to save cards.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="page">
    <div class="header">
      <div>
        <div class="title-row">
          <h1 class="title">AI Card Generator</h1>
         
        </div>
        <p class="subtitle">Enter any topic and it will generate flashcards for you instantly</p>
      </div>
    </div>

    <div class="generate-card">
      <div class="form-grid">
        <div class="field topic-field">
          <label>Topic or Subject <span class="req">*</span></label>
          <input v-model="topic" type="text" placeholder="e.g. JavaScript Promises, French Revolution, Human Anatomy..." @keydown.enter="generate" />
        </div>
        <div class="field">
          <label>Number of Cards</label>
          <select v-model="numCards">
            <option :value="5">5 cards</option>
            <option :value="10">10 cards</option>
            <option :value="15">15 cards</option>
            <option :value="20">20 cards</option>
          </select>
        </div>
        <div class="field">
          <label>Difficulty</label>
          <div class="difficulty-btns">
            <button v-for="d in ['beginner', 'intermediate', 'advanced']" :key="d" type="button" class="diff-btn" :class="{ active: difficulty === d, [d]: true }" @click="difficulty = d as any">{{ d }}</button>
          </div>
        </div>
      </div>

      <div class="error-box" v-if="error">{{ error }}</div>

      <button class="generate-btn" :disabled="loading || !topic.trim()" @click="generate">
        <span v-if="loading" class="loading-dots">
          <span class="spinner-sm"></span>
          Generating {{ numCards }} cards...
        </span>
        <span v-else>✨ Generate {{ numCards }} Cards</span>
      </button>
    </div>

    <template v-if="generatedCards.length > 0">
      <div class="results-header">
        <div>
          <h2 class="results-title">Generated Cards</h2>
          <p class="results-sub">{{ selectedCards.size }} of {{ generatedCards.length }} selected</p>
        </div>
        <button class="toggle-all-btn" @click="toggleAll">
          {{ selectedCards.size === generatedCards.length ? 'Deselect All' : 'Select All' }}
        </button>
      </div>

      <div class="cards-grid">
        <div v-for="(card, i) in generatedCards" :key="i" class="gen-card" :class="{ selected: selectedCards.has(i) }" @click="toggleCard(i)">
          <div class="gen-card-check">
            <div class="check-box" :class="{ checked: selectedCards.has(i) }">
              <span v-if="selectedCards.has(i)">✓</span>
            </div>
          </div>
          <div class="gen-card-content">
            <div class="gen-card-side">
              <p class="side-lbl">FRONT</p>
              <p class="side-txt">{{ card.front }}</p>
            </div>
            <div class="gen-card-divider"></div>
            <div class="gen-card-side">
              <p class="side-lbl">BACK</p>
              <p class="side-txt">{{ card.back }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="save-card">
        <h3 class="save-title">Save {{ selectedCards.size }} Cards to a Deck</h3>
        <div class="deck-choice">
          <button class="choice-btn" :class="{ active: deckChoice === 'new' }" @click="deckChoice = 'new'">Create New Deck</button>
          <button class="choice-btn" :class="{ active: deckChoice === 'existing' }" @click="deckChoice = 'existing'" :disabled="decks.length === 0">Add to Existing</button>
        </div>
        <div class="field" v-if="deckChoice === 'new'">
          <label>New Deck Name</label>
          <input v-model="newDeckName" type="text" :placeholder="`e.g. ${topic || 'My New Deck'}`" />
        </div>
        <div class="field" v-else>
          <label>Select Deck</label>
          <select v-model="selectedDeckId">
            <option v-for="deck in decks" :key="deck.id" :value="deck.id">{{ deck.title }} ({{ deck.cards_count }} cards)</option>
          </select>
        </div>
        <div class="success-msg" v-if="saveMsg">{{ saveMsg }}</div>
        <button class="save-btn" :disabled="saving || selectedCards.size === 0" @click="saveCards">
          {{ saving ? 'Saving...' : `Save ${selectedCards.size} Cards →` }}
        </button>
      </div>
    </template>

    <div class="tip-card" v-if="generatedCards.length === 0 && !loading">
      <div class="tip-grid">
        <div class="tip-item"><span class="tip-icon">🧠</span><h4>Any Subject</h4><p>History, science, languages, programming, medicine — Claude knows it all</p></div>
        <div class="tip-item"><span class="tip-icon">⚡</span><h4>Instant Results</h4><p>Get a full set of high-quality flashcards in seconds, not hours</p></div>
        <div class="tip-item"><span class="tip-icon">✏️</span><h4>Review & Edit</h4><p>Pick only the cards you want, then save them to any deck</p></div>
        <div class="tip-item"><span class="tip-icon">🎯</span><h4>Your Level</h4><p>Choose beginner, intermediate or advanced to match your knowledge</p></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page { display: flex; flex-direction: column; gap: 24px; }
.header { display: flex; justify-content: space-between; align-items: flex-start; }
.title-row { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; }
.title { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.ai-chip { background: linear-gradient(135deg, rgba(0,229,180,0.15), rgba(0,200,150,0.1)); border: 1px solid rgba(0,229,180,0.3); color: #00e5b4; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.subtitle { font-size: 13px; color: #7aa898; }
.generate-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.1); border-radius: 16px; padding: 28px; display: flex; flex-direction: column; gap: 20px; }
.form-grid { display: grid; grid-template-columns: 1fr 160px 1fr; gap: 16px; align-items: end; }
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-size: 13px; font-weight: 600; color: #c8e8d8; }
.req { color: #00e5b4; }
.field input, .field select { background: rgba(0,229,180,0.04); border: 1px solid rgba(0,229,180,0.12); border-radius: 10px; padding: 12px 14px; font-size: 14px; color: #e8f5f0; outline: none; transition: all 0.2s; width: 100%; font-family: inherit; }
.field input::placeholder { color: #4a7a68; }
.field input:focus, .field select:focus { border-color: rgba(0,229,180,0.35); background: rgba(0,229,180,0.06); box-shadow: 0 0 0 3px rgba(0,229,180,0.07); }
.field select option { background: #0d2420; }
.difficulty-btns { display: flex; gap: 6px; }
.diff-btn { flex: 1; padding: 10px 6px; border-radius: 8px; border: 1px solid rgba(0,229,180,0.12); background: rgba(0,229,180,0.03); color: #7aa898; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-transform: capitalize; }
.diff-btn.active.beginner { background: rgba(0,229,180,0.12); border-color: rgba(0,229,180,0.3); color: #00e5b4; }
.diff-btn.active.intermediate { background: rgba(255,165,0,0.12); border-color: rgba(255,165,0,0.3); color: #ffa500; }
.diff-btn.active.advanced { background: rgba(255,100,100,0.12); border-color: rgba(255,100,100,0.3); color: #ff6464; }
.diff-btn:hover:not(.active) { border-color: rgba(0,229,180,0.25); color: #e8f5f0; }
.error-box { background: rgba(255,100,100,0.1); border: 1px solid rgba(255,100,100,0.25); color: #ff8080; padding: 12px 16px; border-radius: 10px; font-size: 14px; }
.generate-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 14px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; width: 100%; }
.generate-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,229,180,0.3); }
.generate-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.loading-dots { display: flex; align-items: center; justify-content: center; gap: 10px; }
.spinner-sm { width: 16px; height: 16px; border: 2px solid rgba(10,31,28,0.3); border-top-color: #0a1f1c; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.results-header { display: flex; justify-content: space-between; align-items: center; }
.results-title { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.results-sub { font-size: 13px; color: #7aa898; }
.toggle-all-btn { background: rgba(0,229,180,0.07); border: 1px solid rgba(0,229,180,0.15); color: #7aa898; padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.toggle-all-btn:hover { color: #00e5b4; border-color: rgba(0,229,180,0.3); }
.cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
.gen-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.07); border-radius: 14px; padding: 18px; cursor: pointer; transition: all 0.2s; display: flex; gap: 14px; align-items: flex-start; }
.gen-card:hover { border-color: rgba(0,229,180,0.2); }
.gen-card.selected { border-color: rgba(0,229,180,0.3); background: rgba(0,229,180,0.04); }
.check-box { width: 20px; height: 20px; border-radius: 6px; border: 2px solid rgba(0,229,180,0.25); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s; font-size: 12px; font-weight: 700; color: #0a1f1c; }
.check-box.checked { background: #00e5b4; border-color: #00e5b4; }
.gen-card-content { display: grid; grid-template-columns: 1fr auto 1fr; gap: 14px; flex: 1; }
.gen-card-side { display: flex; flex-direction: column; gap: 4px; }
.side-lbl { font-size: 10px; letter-spacing: 1.5px; color: #4a7a68; font-weight: 700; }
.side-txt { font-size: 13px; color: #e8f5f0; line-height: 1.5; }
.gen-card-divider { width: 1px; background: rgba(0,229,180,0.07); align-self: stretch; }
.save-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.12); border-radius: 16px; padding: 28px; display: flex; flex-direction: column; gap: 16px; }
.save-title { font-size: 18px; font-weight: 700; color: #fff; }
.deck-choice { display: flex; gap: 8px; }
.choice-btn { flex: 1; padding: 10px; border-radius: 9px; border: 1px solid rgba(0,229,180,0.12); background: rgba(0,229,180,0.03); color: #7aa898; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.choice-btn.active { background: rgba(0,229,180,0.1); border-color: rgba(0,229,180,0.3); color: #00e5b4; }
.choice-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.success-msg { background: rgba(0,229,180,0.1); border: 1px solid rgba(0,229,180,0.25); color: #00e5b4; padding: 10px 14px; border-radius: 9px; font-size: 14px; font-weight: 600; }
.save-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 14px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; width: 100%; }
.save-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,229,180,0.3); }
.save-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.tip-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.07); border-radius: 16px; padding: 32px; }
.tip-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.tip-item { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 10px; }
.tip-icon { font-size: 32px; }
.tip-item h4 { font-size: 14px; font-weight: 700; color: #fff; }
.tip-item p { font-size: 13px; color: #7aa898; line-height: 1.6; }
</style>
