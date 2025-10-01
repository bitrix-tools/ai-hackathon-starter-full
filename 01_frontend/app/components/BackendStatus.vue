<template>
  <div class="card">
    <h2>Backend Status</h2>
    <div v-if="backendStatus === 'checking'">Checking backend health...</div>
    <div v-else-if="backendStatus === 'healthy'" class="status-healthy">
      ✅ Backend is healthy ({{ backendType }})
    </div>
    <div v-else class="status-error">
      ❌ Backend connection failed
    </div>
    <button @click="checkHealth" class="btn btn-primary" style="margin-top: 1rem;">
      Refresh Status
    </button>
  </div>
</template>

<script setup lang="ts">
const { backendStatus, backendType, checkBackendHealth } = useBackend()

const checkHealth = async () => {
  await checkBackendHealth()
}
</script>
