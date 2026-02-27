import api from './index'

export const getCards = (deckId: number) => api.get(`/decks/${deckId}/cards`)
export const createCard = (deckId: number, data: object) => api.post(`/decks/${deckId}/cards`, data)
export const updateCard = (deckId: number, cardId: number, data: object) => api.put(`/decks/${deckId}/cards/${cardId}`, data)
export const deleteCard = (deckId: number, cardId: number) => api.delete(`/decks/${deckId}/cards/${cardId}`)
