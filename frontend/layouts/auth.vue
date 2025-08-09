<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">SaaS Dashboard</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Welcome back! Please sign in to your account.</p>
      </div>
      <slot />
    </div>
  </div>
</template>