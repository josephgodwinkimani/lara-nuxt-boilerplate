import { defineStore } from 'pinia'
import { ref, readonly } from 'vue'
import { useApi } from '~/composables/useApi'
import { useNotifications } from '~/composables/useNotifications'

// Data export functionality
export const useExportStore = defineStore('export', () => {
  const { downloadFile } = useApi()
  const { showNotification } = useNotifications()

  const exporting = ref(false)

  const exportUsers = async (format: 'csv' | 'xml' | 'yaml') => {
    exporting.value = true
    try {
      await downloadFile('/users', `users-${Date.now()}`, format)
    } catch (error) {
      console.error('Export users error:', error)
      showNotification('Export failed', 'error')
    } finally {
      exporting.value = false
    }
  }

  const exportDashboardStats = async (format: 'csv' | 'xml' | 'yaml') => {
    exporting.value = true
    try {
      await downloadFile('/dashboard/stats', `dashboard-stats-${Date.now()}`, format)
    } catch (error) {
      console.error('Export dashboard stats error:', error)
      showNotification('Export failed', 'error')
    } finally {
      exporting.value = false
    }
  }

  const exportUserGrowth = async (format: 'csv' | 'xml' | 'yaml', period = 30) => {
    exporting.value = true
    try {
      await downloadFile(
        `/dashboard/user-growth?period=${period}`,
        `user-growth-${period}days-${Date.now()}`,
        format
      )
    } catch (error) {
      console.error('Export user growth error:', error)
      showNotification('Export failed', 'error')
    } finally {
      exporting.value = false
    }
  }

  const exportActivityMetrics = async (format: 'csv' | 'xml' | 'yaml', days = 7) => {
    exporting.value = true
    try {
      await downloadFile(
        `/dashboard/activity-metrics?days=${days}`,
        `activity-metrics-${days}days-${Date.now()}`,
        format
      )
    } catch (error) {
      console.error('Export activity metrics error:', error)
      showNotification('Export failed', 'error')
    } finally {
      exporting.value = false
    }
  }

  return {
    exporting: readonly(exporting),
    exportUsers,
    exportDashboardStats,
    exportUserGrowth,
    exportActivityMetrics,
  }
})
