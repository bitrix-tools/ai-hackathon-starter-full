<template>
  <div>
    <header class="header">
      <div class="container">
        <nav class="nav">
          <div class="logo">Nuxt3 + Multi-Backend App</div>
          <div>Current Backend: {{ backendType }}</div>
        </nav>
      </div>
    </header>

    <main class="container">
      <BackendStatus />

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-top: 2rem;">
        <UserForm @user-added="refreshUsers" />
        <UserList ref="userListRef" />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
const { backendType, checkBackendHealth } = useBackend()
const userListRef = ref()

// Check backend health on page load
onMounted(async () => {
  await checkBackendHealth()
})

const refreshUsers = () => {
  if (userListRef.value) {
    userListRef.value.loadUsers()
  }
}
</script>
