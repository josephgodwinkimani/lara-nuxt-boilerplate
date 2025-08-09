<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your application users</p>
      </div>
      <div class="flex space-x-3">
        <ExportDropdown @export="exportUsers" />
        <UButton @click="showCreateModal = true" icon="i-heroicons-plus">
          Add User
        </UButton>
      </div>
    </div>

    <!-- Users Table -->
    <UCard class="card">
      <UTable
        :rows="users"
        :columns="columns"
        :loading="loading"
        class="w-full"
      >
        <template #name-data="{ row }">
          <div>
            <p class="font-medium">{{ row.name }}</p>
            <p class="text-sm text-gray-500">{{ row.email }}</p>
          </div>
        </template>

        <template #email_verified_at-data="{ row }">
          <UBadge :color="row.email_verified_at ? 'green' : 'orange'">
            {{ row.email_verified_at ? 'Verified' : 'Unverified' }}
          </UBadge>
        </template>

        <template #created_at-data="{ row }">
          {{ new Date(row.created_at).toLocaleDateString() }}
        </template>

        <template #actions-data="{ row }">
          <UDropdown :items="getActionItems(row)">
            <UButton icon="i-heroicons-ellipsis-horizontal" variant="ghost" />
          </UDropdown>
        </template>
      </UTable>

      <!-- Pagination -->
      <div class="flex justify-center mt-4" v-if="pagination.last_page > 1">
        <UPagination
          v-model="currentPage"
          :page-count="pagination.per_page"
          :total="pagination.total"
          @update:model-value="fetchUsers"
        />
      </div>
    </UCard>

    <!-- Modals -->
    <UserCreateModal v-model="showCreateModal" @created="handleUserCreated" />
    <UserEditModal v-model="showEditModal" :user="selectedUser" @updated="handleUserUpdated" />
    <UserDeleteModal v-model="showDeleteModal" :user="selectedUser" @deleted="handleUserDeleted" />
    <UserPasswordModal v-model="showPasswordModal" :user="selectedUser" />
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
})

const usersStore = useUsersStore()
const exportStore = useExportStore()

const { users, pagination, loading } = storeToRefs(usersStore)

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showPasswordModal = ref(false)
const selectedUser = ref(null)
const currentPage = ref(1)

const columns = [
  { key: 'name', label: 'User' },
  { key: 'email_verified_at', label: 'Status' },
  { key: 'created_at', label: 'Created' },
  { key: 'actions', label: 'Actions' },
]

const getActionItems = (user) => [
  [
    {
      label: 'Edit',
      icon: 'i-heroicons-pencil',
      click: () => editUser(user),
    },
  ],
  [
    {
      label: 'Reset Password',
      icon: 'i-heroicons-key',
      click: () => resetPassword(user),
    },
  ],
  [
    {
      label: 'Delete',
      icon: 'i-heroicons-trash',
      click: () => deleteUser(user),
    },
  ],
]

const fetchUsers = (page = 1) => {
  currentPage.value = page
  usersStore.fetchUsers(page)
}

const editUser = (user) => {
  selectedUser.value = user
  showEditModal.value = true
}

const resetPassword = (user) => {
  selectedUser.value = user
  showPasswordModal.value = true
}

const deleteUser = (user) => {
  selectedUser.value = user
  showDeleteModal.value = true
}

const handleUserCreated = () => {
  showCreateModal.value = false
  fetchUsers()
}

const handleUserUpdated = () => {
  showEditModal.value = false
  fetchUsers()
}

const handleUserDeleted = () => {
  showDeleteModal.value = false
  fetchUsers()
}

const exportUsers = (format) => {
  exportStore.exportUsers(format)
}

onMounted(() => {
  fetchUsers()
})
</script>