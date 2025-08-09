<template>
  <div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <KpiCard
        title="Total Users"
        :value="stats?.total_users || 0"
        icon="i-heroicons-users"
        color="blue"
      />
      <KpiCard
        title="Verified Users"
        :value="stats?.verified_users || 0"
        icon="i-heroicons-check-badge"
        color="green"
      />
      <KpiCard
        title="New Today"
        :value="stats?.users_created_today || 0"
        icon="i-heroicons-plus-circle"
        color="purple"
      />
      <KpiCard
        title="This Month"
        :value="stats?.users_created_this_month || 0"
        icon="i-heroicons-calendar"
        color="orange"
      />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <UCard class="card">
        <template #header>
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">User Growth</h3>
            <ExportDropdown @export="exportUserGrowth" />
          </div>
        </template>
        <LineChart
          v-if="userGrowth?.growth_data"
          :data="userGrowth.growth_data"
          height="300"
        />
      </UCard>

      <UCard class="card">
        <template #header>
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">User Status</h3>
            <ExportDropdown @export="exportUserStatus" />
          </div>
        </template>
        <PieChart
          v-if="usersByStatus?.status_breakdown"
          :data="usersByStatus.status_breakdown"
          height="300"
        />
      </UCard>
    </div>

    <!-- Activity & Recent Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <UCard class="card">
        <template #header>
          <h3 class="text-lg font-semibold">Activity Metrics</h3>
        </template>
        <AreaChart
          v-if="activityMetrics?.metrics"
          :data="activityMetrics.metrics"
          height="300"
        />
      </UCard>

      <UCard class="card">
        <template #header>
          <h3 class="text-lg font-semibold">Recent Users</h3>
        </template>
        <div class="space-y-3">
          <div
            v-for="user in recentUsers?.users || []"
            :key="user.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div>
              <p class="font-medium">{{ user.name }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ user.email }}</p>
            </div>
            <div class="text-right">
              <UBadge :color="user.is_verified ? 'green' : 'orange'">
                {{ user.is_verified ? 'Verified' : 'Unverified' }}
              </UBadge>
              <p class="text-xs text-gray-500 mt-1">{{ user.created_at_human }}</p>
            </div>
          </div>
        </div>
      </UCard>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
})

const dashboardStore = useDashboardStore()
const exportStore = useExportStore()

const { stats, userGrowth, usersByStatus, recentUsers, activityMetrics, loading } =
  storeToRefs(dashboardStore)

onMounted(async () => {
  await dashboardStore.fetchAllDashboardData()
})

const exportUserGrowth = (format) => {
  exportStore.exportUserGrowth(format)
}

const exportUserStatus = (format) => {
  exportStore.exportDashboardStats(format)
}
</script>