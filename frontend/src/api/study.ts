import api from './index'

export const startSession = (deckId: number) => api.post(`/decks/${deckId}/study/start`)
export const nextCard = (sessionId: number) => api.get(`/sessions/${sessionId}/next-card`)
export const rateCard = (sessionId: number, data: { card_id: number; rating: string }) =>
  api.post(`/sessions/${sessionId}/rate`, data)
export const completeSession = (sessionId: number) => api.post(`/sessions/${sessionId}/complete`)
