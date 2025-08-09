<!-- components/ExportDropdown.vue -->
<!-- Data export format selector -->
<template>
  <UDropdown :items="exportItems">
    <UButton icon="i-heroicons-arrow-down-tray" variant="outline">
      Export
    </UButton>
  </UDropdown>
</template>

<script setup>
const emit = defineEmits(['export'])

const exportItems = [
  [
    {
      label: 'CSV',
      icon: 'i-heroicons-document',
      click: () => emit('export', 'csv'),
    },
    {
      label: 'XML',
      icon: 'i-heroicons-code-bracket',
      click: () => emit('export', 'xml'),
    },
    {
      label: 'YAML',
      icon: 'i-heroicons-document-text',
      click: () => emit('export', 'yaml'),
    },
  ],
]
</script>