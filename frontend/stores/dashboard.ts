import { defineStore } from 'pinia'
import { ref, computed, readonly } from 'vue'
import { useApi } from '~/composables/useApi'
import { useAuthStore } from './auth'

// Dashboard data management
export const useDashboardStore = defineStore('dashboard', () => {
  const { apiRequest } = useApi()
  const authStore = useAuthStore()

  const stats = ref(null)
  const userGrowth = ref(null)
  const usersByStatus = ref(null)
  const recentUsers = ref(null)
  const activityMetrics = ref(null)
  const loading = ref(false)

  const authHeaders = computed(() => ({
    Authorization: `Bearer ${authStore.token}`,
  }))

  const fetchStats = async () => {
    loading.value = true
    try {
      const { data } = await apiRequest('/dashboard/stats', {
        headers: authHeaders.value,
      })

      if (data.value) {
        stats.value = data.value
      }
    } catch (error) {
      console.error('Fetch stats error:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchUserGrowth = async (period = 30) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/dashboard/user-growth?period=${period}`, {
        headers: authHeaders.value,
      })

      if (data.value) {
        userGrowth.value = data.value
      }
    } catch (error) {
      console.error('Fetch user growth error:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchUsersByStatus = async () => {
    loading.value = true
    try {
      const { data } = await apiRequest('/dashboard/users-by-status', {
        headers: authHeaders.value,
      })

      if (data.value) {
        usersByStatus.value = data.value
      }
    } catch (error) {
      console.error('Fetch users by status error:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchRecentUsers = async (limit = 10) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/dashboard/recent-users?limit=${limit}`, {
        headers: authHeaders.value,
      })

      if (data.value) {
        recentUsers.value = data.value
      }
    } catch (error) {
      console.error('Fetch recent users error:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchActivityMetrics = async (days = 7) => {
    loading.value = true
    try {
      const { data } = await apiRequest(`/dashboard/activity-metrics?days=${days}`, {
        headers: authHeaders.value,
      })

      if (data.value) {
        activityMetrics.value = data.value
      }
    } catch (error) {
      console.error('Fetch activity metrics error:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchAllDashboardData = async () => {
    await Promise.all([
      fetchStats(),
      fetchUserGrowth(),
      fetchUsersByStatus(),
      fetchRecentUsers(),
      fetchActivityMetrics(),
    ])
  }

  return {
    stats: readonly(stats),
    userGrowth: readonly(userGrowth),
    usersByStatus: readonly(usersByStatus),
    recentUsers: readonly(recentUsers),
    activityMetrics: readonly(activityMetrics),
    loading: readonly(loading),
    fetchStats,
    fetchUserGrowth,
    fetchUsersByStatus,
    fetchRecentUsers,
    fetchActivityMetrics,
    fetchAllDashboardData,
  }
})
