<!-- components/UserCreateModal.vue -->
<!-- New user creation form modal -->
<template>
  <UModal v-model="isOpen" :prevent-close="loading">
    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold">Create New User</h3>
      </template>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <UFormGroup label="Name" required>
          <UInput v-model="form.name" :disabled="loading" required />
        </UFormGroup>

        <UFormGroup label="Email" required>
          <UInput v-model="form.email" type="email" :disabled="loading" required />
        </UFormGroup>

        <UFormGroup label="Password" required>
          <UInput v-model="form.password" type="password" :disabled="loading" required />
        </UFormGroup>

        <UFormGroup label="Confirm Password" required>
          <UInput v-model="form.password_confirmation" type="password" :disabled="loading" required />
        </UFormGroup>

        <div class="flex justify-end space-x-3 pt-4">
          <UButton variant="ghost" @click="close" :disabled="loading">
            Cancel
          </UButton>
          <UButton type="submit" :loading="loading">
            Create User
          </UButton>
        </div>
      </form>
    </UCard>
  </UModal>
</template>

<script setup>
const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue', 'created'])

const usersStore = useUsersStore()
const { showNotification } = useNotifications()

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true
  try {
    await usersStore.createUser(form)
    emit('created')
    resetForm()
  } catch (error) {
    showNotification(error.message || 'Failed to create user', 'error')
  } finally {
    loading.value = false
  }
}

const close = () => {
  isOpen.value = false
  resetForm()
}

const resetForm = () => {
  Object.assign(form, {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  })
}
</script>