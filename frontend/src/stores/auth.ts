import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { authApi, type User } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  function setAuthData(newToken: string, newUser: User) {
    token.value = newToken
    user.value = newUser
    localStorage.setItem('auth_token', newToken)
  }

  function clearAuthData() {
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
  }

  async function login(email: string, password: string) {
    loading.value = true
    error.value = null

    try {
      const response = await authApi.login(email, password)
      setAuthData(response.token, response.user)
      return true
    } catch (err: unknown) {
      if (axios.isAxiosError(err) && err.response?.data?.message) {
        error.value = err.response.data.message
      } else if (err instanceof Error) {
        error.value = err.message
      } else {
        error.value = 'Login failed. Please try again.'
      }
      return false
    } finally {
      loading.value = false
    }
  }

  async function register(name: string, email: string, password: string, passwordConfirmation: string) {
    loading.value = true
    error.value = null

    try {
      const response = await authApi.register(name, email, password, passwordConfirmation)
      setAuthData(response.token, response.user)
      return true
    } catch (err: unknown) {
      if (axios.isAxiosError(err) && err.response?.data?.message) {
        error.value = err.response.data.message
      } else if (err instanceof Error) {
        error.value = err.message
      } else {
        error.value = 'Registration failed. Please try again.'
      }
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true

    try {
      await authApi.logout()
    } catch {
      // Ignore logout errors, clear local data anyway
    } finally {
      clearAuthData()
      loading.value = false
    }
  }

  // Initialize user from token on store creation
  if (token.value) {
    // Token exists, user data will be fetched from /user endpoint
    // For now we just keep the token and let the app fetch user data
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
  }
})

