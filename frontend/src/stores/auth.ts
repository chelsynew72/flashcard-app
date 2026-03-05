import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '../types'
import * as authApi from '../api/auth'
import api from '../api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref<string | null>(localStorage.getItem('token'))
  const isLoggedIn = computed(() => !!token.value)

  const fetchUser = async () => {
    if (!token.value) return
    try {
      const res = await api.get('/user')
      user.value = res.data
      localStorage.setItem('user', JSON.stringify(res.data))
    } catch {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  const login = async (email: string, password: string) => {
    const res = await authApi.login({ email, password })
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(res.data.user))
  }

  const register = async (name: string, email: string, password: string, password_confirmation: string) => {
    const res = await authApi.register({ name, email, password, password_confirmation })
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(res.data.user))
  }

  const logout = async () => {
    try { await authApi.logout() } catch {}
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  if (token.value && !user.value) {
    fetchUser()
  }

  return { user, token, isLoggedIn, login, register, logout, fetchUser }
})
