<!-- components/UserDeleteModal.vue -->
<template>
  <UModal v-model="isOpen" :prevent-close="loading">
    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold text-red-600">Delete User</h3>
      </template>

      <div class="space-y-4">
        <p>Are you sure you want to delete <strong>{{ user?.name }}</strong>?</p>
        <p class="text-sm text-gray-600">This action cannot be undone.</p>

        <div class="flex justify-end space-x-3 pt-4">
          <UButton variant="ghost" @click="close" :disabled="loading">
            Cancel
          </UButton>
          <UButton color="red" @click="handleDelete" :loading="loading">
            Delete User
          </UButton>
        </div>
      </div>
    </UCard>
  </UModal>
</template>

<script setup>
const props = defineProps({
  modelValue: Boolean,
  user: Object,
})

const emit = defineEmits(['update:modelValue', 'deleted'])

const usersStore = useUsersStore()

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const loading = ref(false)

const handleDelete = async () => {
  loading.value = true
  try {
    await usersStore.deleteUser(props.user.id)
    emit('deleted')
    close()
  } catch (error) {
    console.error('Delete error:', error)
  } finally {
    loading.value = false
  }
}

const close = () => {
  isOpen.value = false
}
</script>