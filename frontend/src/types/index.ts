export interface User {
  id: number
  name: string
  email: string
  study_streak: number
  daily_card_limit: number
  email_reminders: boolean
  last_studied_at: string | null
}

export interface Tag {
  id: number
  name: string
  color: string | null
}

export interface Deck {
  id: number
  user_id: number
  title: string
  description: string | null
  is_public: boolean
  tags: Tag[]
  cards_count?: number
  created_at: string
  updated_at: string
}

export interface Card {
  id: number
  deck_id: number
  front: string
  back: string
  created_at: string
  updated_at: string
}

export interface DashboardData {
  cards_due_today: number
  study_streak: number
  total_decks: number
  total_cards: number
  cards_learned: number
  cards_mastered: number
  weekly_activity: { date: string; cards_studied: number }[]
  recent_decks: Deck[]
  decks_due_today: Deck[]
}
