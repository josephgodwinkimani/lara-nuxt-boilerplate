<!-- components/DarkModeToggle.vue -->
<!-- Light/dark mode switcher -->
<template>
  <UButton :icon="isDark ? 'i-heroicons-sun' : 'i-heroicons-moon'" variant="ghost" @click="toggleDark" />
</template>

<script setup>
const colorMode = useColorMode()
const isDark = computed(() => colorMode.value === 'dark')

const toggleDark = () => {
  colorMode.preference = isDark.value ? 'light' : 'dark'
}
</script>