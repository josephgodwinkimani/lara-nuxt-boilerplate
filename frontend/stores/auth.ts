import { useRuntimeConfig, navigateTo } from 'nuxt/app'
import { defineStore } from 'pinia'
import { ref, computed, readonly } from 'vue'
import { useNotifications } from '~/composables/useNotifications'

// Authentication state and operations
export const useAuthStore = defineStore('auth', () => {
  const config = useRuntimeConfig()
  const { showNotification } = useNotifications()

  const user = ref(null)
  const token = ref(null)
  const isAuthenticated = computed(() => !!user.value && !!token.value)

  const login = async (credentials: { email: string; password: string }) => {
    try {
      const response = await $fetch('/auth/login', {
        method: 'POST',
        body: credentials,
        baseURL: config?.public?.apiBase,
      })

      if (response) {
        user.value = response?.user
        token.value = response?.token

        // Store token in localStorage for persistence
        if (process.client) {
          localStorage.setItem('auth_token', response?.token)
        }

        showNotification('Logged in successfully', 'success')
        await navigateTo('/dashboard')
      }
    } catch (error) {
      console.error('Login error:', error)
      throw error
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await $fetch('/auth/logout', {
          method: 'POST',
          baseURL: config?.public?.apiBase,
          headers: {
            Authorization: `Bearer ${token.value}`,
          },
        })
      }
    } catch (error) {
      c
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null

      if (process.client) {
        localStorage.removeItem('auth_token')
      }

      showNotification('Logged out successfully', 'success')
      await navigateTo('/login')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return

    try {
      const response = await $fetch('/auth/user', {
        baseURL: config?.public?.apiBase,
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      if (response) {
        user.value = response.data || response
      }
    } catch (error) {
      console.error('Fetch user error:', error)
      // If token is invalid, logout
      await logout()
    }
  }

  const refreshToken = async () => {
    if (!token.value) return

    try {
      const response = await $fetch('/auth/refresh', {
        method: 'POST',
        baseURL: config?.public?.apiBase,
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      if (response) {
        user.value = response.user
        token.value = response.token

        if (process.client) {
          localStorage.setItem('auth_token', response.token)
        }
      }
    } catch (error) {
      console.error('Refresh token error:', error)
      await logout()
    }
  }

  const initializeAuth = async () => {
    if (process.client) {
      const storedToken = localStorage.getItem('auth_token')
      if (storedToken) {
        token.value = storedToken
        await fetchUser()
      }
    }
  }

  return {
    user: readonly(user),
    token: readonly(token),
    isAuthenticated,
    login,
    logout,
    fetchUser,
    refreshToken,
    initializeAuth,
  }
})
