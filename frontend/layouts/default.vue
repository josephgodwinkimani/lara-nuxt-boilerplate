<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-300 lg:translate-x-0" :class="{ '-translate-x-full': !sidebarOpen }">
      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">SaaS Dashboard</h1>
        <UButton icon="i-heroicons-x-mark" variant="ghost" @click="sidebarOpen = false" class="lg:hidden" />
      </div>
      
      <nav class="mt-4">
        <NuxtLink to="/dashboard" class="sidebar-nav-item" :class="{ active: $route.path === '/dashboard' }">
          <UIcon name="i-heroicons-home" class="mr-3 h-5 w-5" />
          Dashboard
        </NuxtLink>
        
        <NuxtLink to="/users" class="sidebar-nav-item" :class="{ active: $route.path.startsWith('/users') }">
          <UIcon name="i-heroicons-users" class="mr-3 h-5 w-5" />
          Users
        </NuxtLink>
        
        <NuxtLink to="/analytics" class="sidebar-nav-item" :class="{ active: $route.path === '/analytics' }">
          <UIcon name="i-heroicons-chart-bar" class="mr-3 h-5 w-5" />
          Analytics
        </NuxtLink>
        
        <NuxtLink to="/settings" class="sidebar-nav-item" :class="{ active: $route.path === '/settings' }">
          <UIcon name="i-heroicons-cog-6-tooth" class="mr-3 h-5 w-5" />
          Settings
        </NuxtLink>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Top Navigation -->
      <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between px-4 py-3">
          <div class="flex items-center">
            <UButton icon="i-heroicons-bars-3" variant="ghost" @click="sidebarOpen = true" class="lg:hidden mr-2" />
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ pageTitle }}</h2>
          </div>
          
          <div class="flex items-center space-x-4">
            <DarkModeToggle />
            <UserMenu />
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <slot />
      </main>
    </div>

    <!-- Mobile overlay -->
    <div v-if="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" @click="sidebarOpen = false"></div>
  </div>
</template>

<script setup>
const route = useRoute()
const sidebarOpen = ref(false)

const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/users': 'User Management',
    '/analytics': 'Analytics',
    '/settings': 'Settings',
  }
  return titles[route.path] || 'Dashboard'
})

// Close sidebar on route change (mobile)
watch(
  () => route.path,
  () => {
    sidebarOpen.value = false
  }
)
</script>