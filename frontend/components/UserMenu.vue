<!-- components/UserMenu.vue -->
<!-- User profile dropdown menu -->
<template>
  <UDropdown :items="userMenuItems">
    <UButton variant="ghost" class="flex items-center space-x-2">
      <UAvatar :alt="user?.name" size="sm" />
      <span class="hidden md:block">{{ user?.name }}</span>
      <UIcon name="i-heroicons-chevron-down" />
    </UButton>
  </UDropdown>
</template>

<script setup>
const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

const userMenuItems = [
  [
    {
      label: 'Profile',
      icon: 'i-heroicons-user',
      click: () => navigateTo('/profile'),
    },
    {
      label: 'Settings',
      icon: 'i-heroicons-cog-6-tooth',
      click: () => navigateTo('/settings'),
    },
  ],
  [
    {
      label: 'Sign Out',
      icon: 'i-heroicons-arrow-right-on-rectangle',
      click: () => authStore.logout(),
    },
  ],
]
</script>