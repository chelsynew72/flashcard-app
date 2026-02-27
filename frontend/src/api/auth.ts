import api from './index'

export const register = (data: { name: string; email: string; password: string; password_confirmation: string }) =>
  api.post('/auth/register', data)

export const login = (data: { email: string; password: string }) =>
  api.post('/auth/login', data)

export const logout = () => api.post('/auth/logout')

export const forgotPassword = (email: string) =>
  api.post('/auth/forgot-password', { email })

export const resetPassword = (data: { token: string; email: string; password: string; password_confirmation: string }) =>
  api.post('/auth/reset-password', data)
