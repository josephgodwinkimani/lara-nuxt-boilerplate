<!-- components/NotificationToast.vue -->
<!-- Toast notification display system -->
<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <UNotification v-for="notification in notifications" :key="notification.id" :title="notification.message"
      :color="getNotificationColor(notification.type)" :timeout="notification.duration"
      @close="removeNotification(notification.id)" />
  </div>
</template>

<script setup>
const { notifications, removeNotification } = useNotifications()

const getNotificationColor = (type) => {
  const colors = {
    success: 'green',
    error: 'red',
    warning: 'yellow',
    info: 'blue',
  }
  return colors[type] || 'blue'
}
</script>