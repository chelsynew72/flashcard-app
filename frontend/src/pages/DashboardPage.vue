<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { getDashboard } from '../api/dashboard'
import type { DashboardData } from '../types'

const router = useRouter()
const auth = useAuthStore()
const data = ref<DashboardData | null>(null)
const loading = ref(true)

const greeting = () => {
  const h = new Date().getHours()
  if (h < 12) return 'Good morning'
  if (h < 18) return 'Good afternoon'
  return 'Good evening'
}

const formatDate = () => new Date().toLocaleDateString('en-US', {
  weekday: 'long', month: 'long', day: 'numeric'
})

onMounted(async () => {
  try {
    const res = await getDashboard()
    data.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="dashboard">

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Loading your dashboard...</p>
    </div>

    <template v-else-if="data">

      <!-- HEADER -->
      <div class="header">
        <div>
          <h1 class="greeting">{{ greeting() }}, {{ auth.user?.name?.split(' ')[0] ?? 'there' }} 👋</h1>
          <p class="date">{{ formatDate() }}</p>
        </div>
        <button class="new-btn" @click="router.push('/decks/create')">+ New Deck</button>
      </div>

      <!-- ROW 1: Due + Streak -->
      <div class="row-2">
        <div class="due-card">
          <div class="due-top">
            <div>
              <p class="section-label">DAILY GOAL</p>
              <div class="due-number">
                <span class="due-count">{{ data.cards_due_today }}</span>
                <span class="due-unit">Cards Due Today</span>
              </div>
              <p class="due-msg">
                {{ data.cards_due_today > 0 ? "Finish today's review to keep your streak going." : "All caught up! Check back tomorrow." }}
              </p>
            </div>
            <span class="due-emoji">📚</span>
          </div>
          <button
            class="start-btn"
            :disabled="data.cards_due_today === 0"
            @click="data.decks_due_today[0] && router.push(`/decks/${data.decks_due_today[0].id}/study`)"
          >
            {{ data.cards_due_today > 0 ? "Start Today's Review →" : "All Done ✓" }}
          </button>
        </div>

        <div class="streak-card">
          <div class="streak-top">
            <span class="fire">🔥</span>
            <div>
              <p class="streak-num">{{ data.study_streak }} day streak</p>
              <p class="streak-sub">Keep it going!</p>
            </div>
          </div>
          <div class="streak-dots">
            <div v-for="i in 7" :key="i" class="dot" :class="{ active: i <= Math.min(data.study_streak, 7) }"></div>
          </div>
        </div>
      </div>

      <!-- ROW 2: Stats -->
      <div class="stats-row">
        <div class="stat-card">
          <p class="stat-val">{{ data.total_decks }}</p>
          <p class="stat-lbl">Total Decks</p>
        </div>
        <div class="stat-card">
          <p class="stat-val">{{ data.total_cards }}</p>
          <p class="stat-lbl">Total Cards</p>
        </div>
        <div class="stat-card">
          <p class="stat-val">{{ data.cards_learned }}</p>
          <p class="stat-lbl">Learned</p>
        </div>
        <div class="stat-card">
          <p class="stat-val">{{ data.cards_mastered }}</p>
          <p class="stat-lbl">Mastered</p>
        </div>
      </div>

      <!-- ROW 3: Chart + Decks Due -->
      <div class="row-2">
        <div class="card">
          <h3 class="card-title">Weekly Activity</h3>
          <div class="chart">
            <div v-for="day in data.weekly_activity" :key="day.date" class="bar-col">
              <div class="bar-wrap">
                <div
                  class="bar"
                  :class="{ active: day.cards_studied > 0 }"
                  :style="{
                    height: day.cards_studied > 0
                      ? `${Math.max(12, (day.cards_studied / Math.max(...data.weekly_activity.map(d => d.cards_studied), 1)) * 100)}px`
                      : '4px'
                  }"
                ></div>
              </div>
              <p class="bar-day">{{ new Date(day.date).toLocaleDateString('en-US', { weekday: 'short' }) }}</p>
              <p class="bar-num">{{ day.cards_studied }}</p>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Decks Due Today</h3>
            <button class="see-all" @click="router.push('/decks')">See All</button>
          </div>
          <div v-if="data.decks_due_today.length === 0" class="empty">
            <span>🎉</span>
            <p>All decks up to date!</p>
          </div>
          <div v-else class="due-list">
            <div v-for="deck in data.decks_due_today" :key="deck.id" class="due-row">
              <div>
                <p class="due-title">{{ deck.title }}</p>
                <div class="tags">
                  <span v-for="tag in deck.tags" :key="tag.id" class="tag">{{ tag.name }}</span>
                </div>
              </div>
              <button class="study-btn" @click="router.push(`/decks/${deck.id}/study`)">Study</button>
            </div>
          </div>
        </div>
      </div>

      <!-- ROW 4: Continue Studying -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Continue Studying</h3>
          <button class="see-all" @click="router.push('/decks')">See All</button>
        </div>

        <div v-if="data.recent_decks.length === 0" class="empty tall">
          <span>📚</span>
          <p>No decks yet. Create your first deck!</p>
          <button class="new-btn" @click="router.push('/decks/create')">Create a Deck</button>
        </div>

        <div v-else class="recent-grid">
          <div v-for="deck in data.recent_decks" :key="deck.id" class="recent-card" @click="router.push(`/decks/${deck.id}`)">
            <div class="recent-top">
              <h4>{{ deck.title }}</h4>
              <span class="chip">{{ deck.cards_count }} cards</span>
            </div>
            <p class="recent-desc">{{ deck.description || 'No description' }}</p>
            <div class="tags">
              <span v-for="tag in deck.tags" :key="tag.id" class="tag">{{ tag.name }}</span>
            </div>
            <div class="recent-btns">
              <button class="study-btn" @click.stop="router.push(`/decks/${deck.id}/study`)">Study</button>
              <button class="view-btn" @click.stop="router.push(`/decks/${deck.id}`)">View</button>
            </div>
          </div>
        </div>
      </div>

    </template>
  </div>
</template>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 20px; }

/* Loading */
.loading { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 400px; gap: 16px; color: #8ab8a8; }
.spinner { width: 36px; height: 36px; border: 3px solid rgba(0,229,180,0.15); border-top-color: #00e5b4; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Header */
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.greeting { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 4px; }
.date { font-size: 13px; color: #7aa898; }
.new-btn { background: #00e5b4; color: #0a1f1c; border: none; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.new-btn:hover { background: #00c896; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,229,180,0.25); }

/* 2-col rows */
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* Due card */
.due-card {
  background: linear-gradient(135deg, rgba(0,229,180,0.1), rgba(0,180,140,0.04));
  border: 1px solid rgba(0,229,180,0.2);
  border-radius: 16px; padding: 24px;
  display: flex; flex-direction: column; gap: 16px;
}
.due-top { display: flex; justify-content: space-between; align-items: flex-start; }
.section-label { font-size: 10px; letter-spacing: 1.5px; color: #7aa898; font-weight: 600; margin-bottom: 8px; }
.due-number { display: flex; align-items: baseline; gap: 8px; margin-bottom: 8px; }
.due-count { font-size: 52px; font-weight: 800; color: #00e5b4; line-height: 1; letter-spacing: -2px; }
.due-unit { font-size: 13px; color: #7aa898; }
.due-msg { font-size: 13px; color: #7aa898; line-height: 1.5; max-width: 220px; }
.due-emoji { font-size: 44px; opacity: 0.5; }
.start-btn { background: linear-gradient(135deg, #00e5b4, #00c896); color: #0a1f1c; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; width: 100%; }
.start-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,229,180,0.3); }
.start-btn:disabled { opacity: 0.45; cursor: default; }

/* Streak */
.streak-card {
  background: linear-gradient(135deg, rgba(255,128,0,0.08), rgba(200,80,0,0.03));
  border: 1px solid rgba(255,128,0,0.18);
  border-radius: 16px; padding: 24px;
  display: flex; flex-direction: column; justify-content: space-between;
}
.streak-top { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
.fire { font-size: 48px; }
.streak-num { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.streak-sub { font-size: 13px; color: #ff8c00; font-weight: 500; }
.streak-dots { display: flex; gap: 8px; }
.dot { flex: 1; height: 6px; border-radius: 3px; background: rgba(255,128,0,0.15); }
.dot.active { background: #ff8c00; }

/* Stats */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.stat-card { background: #0d2420; border: 1px solid rgba(0,229,180,0.07); border-radius: 14px; padding: 20px; text-align: center; transition: all 0.2s; }
.stat-card:hover { border-color: rgba(0,229,180,0.18); transform: translateY(-2px); }
.stat-val { font-size: 30px; font-weight: 800; color: #fff; letter-spacing: -1px; margin-bottom: 4px; }
.stat-lbl { font-size: 12px; color: #7aa898; font-weight: 500; }

/* Generic card */
.card { background: #0d2420; border: 1px solid rgba(0,229,180,0.07); border-radius: 16px; padding: 24px; }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.card-title { font-size: 16px; font-weight: 700; color: #fff; }
.see-all { background: none; border: none; color: #7aa898; font-size: 13px; cursor: pointer; transition: color 0.2s; }
.see-all:hover { color: #00e5b4; }

/* Chart */
.chart { display: flex; align-items: flex-end; gap: 6px; height: 130px; margin-top: 16px; }
.bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; }
.bar-wrap { flex: 1; display: flex; align-items: flex-end; width: 100%; }
.bar { width: 100%; border-radius: 4px 4px 0 0; background: rgba(0,229,180,0.12); transition: height 0.5s ease; min-height: 4px; }
.bar.active { background: linear-gradient(180deg, #00e5b4, #00c896); }
.bar-day { font-size: 11px; color: #7aa898; }
.bar-num { font-size: 10px; color: #4a7a68; }

/* Empty */
.empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 32px 0; color: #7aa898; font-size: 14px; }
.empty span { font-size: 28px; }
.empty.tall { padding: 48px 0; }

/* Due list */
.due-list { display: flex; flex-direction: column; gap: 10px; }
.due-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: rgba(0,229,180,0.03); border: 1px solid rgba(0,229,180,0.07); border-radius: 10px; }
.due-title { font-size: 14px; font-weight: 600; color: #fff; margin-bottom: 4px; }

/* Tags */
.tags { display: flex; gap: 6px; flex-wrap: wrap; }
.tag { background: rgba(0,229,180,0.1); border: 1px solid rgba(0,229,180,0.15); color: #00e5b4; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }

/* Buttons */
.study-btn { background: #00e5b4; color: #0a1f1c; border: none; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.study-btn:hover { background: #00c896; transform: translateY(-1px); }
.view-btn { background: rgba(0,229,180,0.07); color: #7aa898; border: 1px solid rgba(0,229,180,0.12); padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.view-btn:hover { color: #00e5b4; border-color: rgba(0,229,180,0.25); }

/* Recent decks */
.recent-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 4px; }
.recent-card { background: rgba(0,229,180,0.03); border: 1px solid rgba(0,229,180,0.07); border-radius: 12px; padding: 18px; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; gap: 8px; }
.recent-card:hover { border-color: rgba(0,229,180,0.2); transform: translateY(-2px); }
.recent-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
.recent-top h4 { font-size: 14px; font-weight: 700; color: #fff; }
.chip { font-size: 11px; color: #7aa898; background: rgba(0,229,180,0.07); padding: 2px 8px; border-radius: 999px; white-space: nowrap; }
.recent-desc { font-size: 12px; color: #7aa898; line-height: 1.5; }
.recent-btns { display: flex; gap: 8px; margin-top: 4px; }
.recent-btns .study-btn, .recent-btns .view-btn { flex: 1; text-align: center; }
</style>
