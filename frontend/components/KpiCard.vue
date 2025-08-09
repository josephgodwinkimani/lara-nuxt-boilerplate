<!-- components/KpiCard.vue -->
<!-- Key Performance Indicator display card -->
<template>
  <UCard class="card">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <UIcon :name="icon" :class="`h-8 w-8 text-${color}-600`" />
      </div>
      <div class="ml-4">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ title }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatValue(value) }}</p>
      </div>
    </div>
  </UCard>
</template>

<script setup>
defineProps({
  title: String,
  value: [Number, String],
  icon: String,
  color: { type: String, default: 'blue' },
})

const formatValue = (value) => {
  if (typeof value === 'number') {
    return value.toLocaleString()
  }
  return value
}
</script>
