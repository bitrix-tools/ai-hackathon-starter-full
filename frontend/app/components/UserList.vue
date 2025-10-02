<template>
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h2>Users</h2>
      <button @click="loadUsers" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Loading...' : 'Refresh' }}
      </button>
    </div>

    <div v-if="loading">Loading users...</div>
    <div v-else-if="users.length === 0">No users found</div>
    <div v-else class="users-grid">
      <div v-for="user in users" :key="user.id" class="user-card">
        <h3>{{ user.name }}</h3>
        <p>{{ user.email }}</p>
        <small>ID: {{ user.id }}</small>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const users = ref([])
const loading = ref(false)
const { fetchUsers } = useBackend()

const loadUsers = async () => {
  loading.value = true
  try {
    users.value = await fetchUsers()
  } catch (error) {
    console.error('Failed to load users:', error)
  } finally {
    loading.value = false
  }
}

// Load users on component mount
onMounted(() => {
  loadUsers()
})

// Expose loadUsers for parent component
defineExpose({ loadUsers })
</script>
