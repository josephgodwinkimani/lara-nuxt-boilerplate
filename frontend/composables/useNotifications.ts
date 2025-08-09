import { ref, readonly } from 'vue'

// Toast notification system
export const useNotifications = () => {
  const notifications = ref([])

  const showNotification = (
    message: string,
    type: 'success' | 'error' | 'warning' | 'info' = 'info',
    duration = 5000
  ) => {
    const notification = {
      id: Date.now() + Math.random(),
      message,
      type,
      duration,
    }

    notifications.value.push(notification)

    if (duration > 0) {
      setTimeout(() => {
        removeNotification(notification.id)
      }, duration)
    }

    return notification.id
  }

  const removeNotification = (id: number) => {
    const index = notifications.value.findIndex((n) => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    notifications.value = []
  }

  return {
    notifications: readonly(notifications),
    showNotification,
    removeNotification,
    clearAll,
  }
}
