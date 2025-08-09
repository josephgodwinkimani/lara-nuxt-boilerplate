<template>
  <div>
    <UCard class="card">
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <UFormGroup label="Email" required>
            <UInput
              v-model="form.email"
              type="email"
              placeholder="Enter your email"
              :disabled="loading"
              required
            />
          </UFormGroup>
        </div>

        <div>
          <UFormGroup label="Password" required>
            <UInput
              v-model="form.password"
              type="password"
              placeholder="Enter your password"
              :disabled="loading"
              required
            />
          </UFormGroup>
        </div>

        <UButton
          type="submit"
          :loading="loading"
          :disabled="loading"
          block
          size="lg"
        >
          Sign In
        </UButton>
      </form>
    </UCard>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const authStore = useAuthStore()
const { showNotification } = useNotifications()

const form = reactive({
  email: '',
  password: '',
})

const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  try {
    await authStore.login(form)
  } catch (error) {
    showNotification(error.message || 'Login failed', 'error')
  } finally {
    loading.value = false
  }
}
</script>