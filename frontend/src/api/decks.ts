import api from './index'

export const getDecks = (params?: object) => api.get('/decks', { params })
export const getDeck = (id: number) => api.get(`/decks/${id}`)
export const createDeck = (data: object) => api.post('/decks', data)
export const updateDeck = (id: number, data: object) => api.put(`/decks/${id}`, data)
export const deleteDeck = (id: number) => api.delete(`/decks/${id}`)
export const cloneDeck = (id: number) => api.post(`/decks/${id}/clone`)
