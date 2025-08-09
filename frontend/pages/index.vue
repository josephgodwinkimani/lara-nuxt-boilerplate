<template>
  <div>
    <NuxtLayout name="auth">
      <div class="text-center space-y-6">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
          Welcome to SaaS Dashboard
        </h2>
        
        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
          A full-stack dashboard built with Laravel 12 and Nuxt 3, featuring real-time analytics, user management, and data visualization.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <UButton to="/login" size="lg">
            Sign In
          </UButton>
          
          <UButton to="/dashboard" variant="outline" size="lg">
            View Dashboard
          </UButton>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
          <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <UIcon name="i-heroicons-chart-bar" class="h-8 w-8 mx-auto text-primary-600 mb-4" />
            <h3 class="font-semibold mb-2">Analytics</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Real-time dashboard with D3.js visualizations</p>
          </div>
          
          <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <UIcon name="i-heroicons-users" class="h-8 w-8 mx-auto text-primary-600 mb-4" />
            <h3 class="font-semibold mb-2">User Management</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Complete CRUD operations for user management</p>
          </div>
          
          <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <UIcon name="i-heroicons-arrow-down-tray" class="h-8 w-8 mx-auto text-primary-600 mb-4" />
            <h3 class="font-semibold mb-2">Data Export</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Export data in multiple formats (CSV, XML, YAML)</p>
          </div>
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup>
definePageMeta({
  layout: false,
})

// Redirect to dashboard if already authenticated
const authStore = useAuthStore()

onMounted(() => {
  if (authStore.isAuthenticated) {
    navigateTo('/dashboard')
  }
})
</script>