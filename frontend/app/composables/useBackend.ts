export const useBackend = () => {
  const apiStore = useApiStore()
  const backendStatus = ref<'healthy' | 'error' | 'checking' | undefined>(undefined)
  const backendType = ref<string>('')

  // Check backend health
  const checkBackendHealth = async () => {
    backendStatus.value = 'checking'
    try {
      const health = await apiStore.checkHealth()
      backendStatus.value = 'healthy'
      backendType.value = health.backend as string
      return health
    } catch (error) {
      backendStatus.value = 'error'
      throw error
    }
  }

  return {
    backendStatus,
    backendType,
    checkBackendHealth
  }
}
