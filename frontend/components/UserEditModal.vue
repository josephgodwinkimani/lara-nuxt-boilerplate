<!-- User information editing modal -->
<!-- components/UserEditModal.vue -->
 <template>
  <UModal v-model="isOpen" :prevent-close="loading">
    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold">Edit User</h3>
      </template>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <UFormGroup label="Name" required>
          <UInput v-model="form.name" :disabled="loading" required />
        </UFormGroup>

        <UFormGroup label="Email" required>
          <UInput v-model="form.email" type="email" :disabled="loading" required />
        </UFormGroup>

        <UFormGroup label="New Password" help="Leave blank to keep current password">
          <UInput v-model="form.password" type="password" :disabled="loading" />
        </UFormGroup>

        <UFormGroup label="Confirm New Password" v-if="form.password">
          <UInput v-model="form.password_confirmation" type="password" :disabled="loading" />
        </UFormGroup>

        <div class="flex justify-end space-x-3 pt-4">
          <UButton variant="ghost" @click="close" :disabled="loading">
            Cancel
          </UButton>
          <UButton type="submit" :loading="loading">
            Update User
          </UButton>
        </div>
      </form>
    </UCard>
  </UModal>
</template>

<script setup>
const props = defineProps({
  modelValue: Boolean,
  user: Object,
})

const emit = defineEmits(['update:modelValue', 'updated'])

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
    const updateData = {
      name: form.name,
      email: form.email,
    }

    if (form.password) {
      updateData.password = form.password
      updateData.password_confirmation = form.password_confirmation
    }

    await usersStore.updateUser(props.user.id, updateData)
    emit('updated')
    close()
  } catch (error) {
    showNotification(error.message || 'Failed to update user', 'error')
  } finally {
    loading.value = false
  }
}

const close = () => {
  isOpen.value = false
}

watch(
  () => props.user,
  (user) => {
    if (user) {
      form.name = user.name
      form.email = user.email
      form.password = ''
      form.password_confirmation = ''
    }
  },
  { immediate: true }
)
</script>