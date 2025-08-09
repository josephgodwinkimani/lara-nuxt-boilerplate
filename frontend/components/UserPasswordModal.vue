<!-- components/UserPasswordModal.vue -->
<!-- Admin password reset interface -->
<template>
  <UModal v-model="isOpen" :prevent-close="loading">
    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold">Reset Password</h3>
      </template>

      <div class="space-y-4">
        <p>Reset password for <strong>{{ user?.name }}</strong></p>

        <div class="flex space-x-3">
          <UButton @click="generatePassword" :loading="generating">
            Generate Password
          </UButton>
          <UButton variant="outline" @click="showCustomForm = !showCustomForm">
            Set Custom Password
          </UButton>
        </div>

        <form v-if="showCustomForm" @submit.prevent="resetPassword" class="space-y-4">
          <UFormGroup label="New Password" required>
            <UInput v-model="form.password" type="password" :disabled="loading" required />
          </UFormGroup>

          <UFormGroup label="Confirm Password" required>
            <UInput v-model="form.password_confirmation" type="password" :disabled="loading" required />
          </UFormGroup>

          <div class="flex justify-end space-x-3">
            <UButton variant="ghost" @click="close" :disabled="loading">
              Cancel
            </UButton>
            <UButton type="submit" :loading="loading">
              Reset Password
            </UButton>
          </div>
        </form>

        <div v-if="!showCustomForm" class="flex justify-end">
          <UButton variant="ghost" @click="close">
            Close
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

const emit = defineEmits(['update:modelValue'])

const usersStore = useUsersStore()
const { showNotification } = useNotifications()

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const form = reactive({
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const generating = ref(false)
const showCustomForm = ref(false)

const generatePassword = async () => {
  generating.value = true
  try {
    const password = await usersStore.generateUserPassword(props.user.id)
    showNotification(`Generated password: ${password}`, 'success', 10000)
  } catch (error) {
    showNotification(error.message || 'Failed to generate password', 'error')
  } finally {
    generating.value = false
  }
}

const resetPassword = async () => {
  loading.value = true
  try {
    await usersStore.resetUserPassword(props.user.id, form)
    close()
  } catch (error) {
    showNotification(error.message || 'Failed to reset password', 'error')
  } finally {
    loading.value = false
  }
}

const close = () => {
  isOpen.value = false
  showCustomForm.value = false
  form.password = ''
  form.password_confirmation = ''
}
</script>