<template>
  <div>
    <VitePwaManifest />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <NotificationToast />
  </div>
</template>

<script setup>
// Initialize auth stiore on app start
const authStore = useAuthStore()

onMounted(async () => {
  await authStore.initializeAuth()
})
</script>