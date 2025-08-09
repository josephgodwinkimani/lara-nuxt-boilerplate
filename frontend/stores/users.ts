import { defineStore } from 'pinia'
import { ref, computed, readonly } from 'vue'
import { useApi } from '~/composables/useApi'
import { useNotifications } from '~/composables/useNotifications'
import { useAuthStore } from './auth'

// User management CRUD operations
export const useUsersStore = defineStore('users', () => {
  const { apiRequest } = useApi()
  const authStore = useAuthStore()
  const { showNotification } = useNotifications()

  const users = ref([])
  const currentUser = ref(null)
  const pagination = ref({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
  })
  const loading = ref(false)

  const authHeaders = computed(() => ({
    Authorization: `Bearer ${authStore.token}`,
  }))

  const fetchUsers = async (page = 1, perPage = 15) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/users?page=${page}&per_page=${perPage}`, {
        headers: authHeaders.value,
      })

      if (data.value) {
        users.value = data.value.data || data.value
        if (data.value.meta) {
          pagination.value = data.value.meta.pagination || data.value.meta
        }
      }
    } catch (error) {
      console.error('Fetch users error:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchUser = async (id: number) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/users/${id}`, {
        headers: authHeaders.value,
      })

      if (data.value) {
        currentUser.value = data.value.data || data.value
      }
    } catch (error) {
      console.error('Fetch user error:', error)
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData: {
    name: string
    email: string
    password: string
    password_confirmation: string
  }) => {
    loading.value = true
    try {
      const { data } = await apiRequest('/users', {
        method: 'POST',
        headers: authHeaders.value,
        body: userData,
      })

      if (data.value) {
        const newUser = data.value.data || data.value
        users.value.unshift(newUser)
        showNotification('User created successfully', 'success')
        return newUser
      }
    } catch (error) {
      console.error('Create user error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (
    id: number,
    userData: {
      name?: string
      email?: string
      password?: string
      password_confirmation?: string
    }
  ) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/users/${id}`, {
        method: 'PUT',
        headers: authHeaders.value,
        body: userData,
      })

      if (data.value) {
        const updatedUser = data.value.data || data.value
        const index = users.value.findIndex((u) => u.id === id)
        if (index !== -1) {
          users.value[index] = updatedUser
        }
        currentUser.value = updatedUser
        showNotification('User updated successfully', 'success')
        return updatedUser
      }
    } catch (error) {
      console.error('Update user error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const deleteUser = async (id: number) => {
    loading.value = true
    try {
      await apiRequest(`/users/${id}`, {
        method: 'DELETE',
        headers: authHeaders.value,
      })

      users.value = users.value.filter((u) => u.id !== id)
      showNotification('User deleted successfully', 'success')
    } catch (error) {
      console.error('Delete user error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const resetUserPassword = async (
    id: number,
    passwordData: {
      password: string
      password_confirmation: string
    }
  ) => {
    loading.value = true
    try {
      await apiRequest(`/users/${id}/reset-password`, {
        method: 'POST',
        headers: authHeaders.value,
        body: passwordData,
      })

      showNotification('Password reset successfully', 'success')
    } catch (error) {
      console.error('Reset password error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const generateUserPassword = async (id: number) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/users/${id}/generate-password`, {
        method: 'POST',
        headers: authHeaders.value,
      })

      if (data.value) {
        showNotification('Password generated successfully', 'success')
        return data.value.password
      }
    } catch (error) {
      console.error('Generate password error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    users: readonly(users),
    currentUser: readonly(currentUser),
    pagination: readonly(pagination),
    loading: readonly(loading),
    fetchUsers,
    fetchUser,
    createUser,
    updateUser,
    deleteUser,
    resetUserPassword,
    generateUserPassword,
  }
})
