export const useBackend = () => {
  const apiStore = useApiStore()
  const backendStatus = ref<'healthy' | 'error' | 'checking'>('checking')
  const backendType = ref<string>('')

  // Check backend health
  const checkBackendHealth = async () => {
    backendStatus.value = 'checking'
    try {
      const health = await apiStore.checkHealth()
      backendStatus.value = 'healthy'
      backendType.value = health.backend
      return health
    } catch (error) {
      backendStatus.value = 'error'
      throw error
    }
  }

  // Get users
  const fetchUsers = async () => {
    return await apiStore.getUsers()
  }

  // Create user
  const addUser = async (userData: { name: string; email: string }) => {
    return await apiStore.createUser(userData)
  }

  return {
    backendStatus,
    backendType,
    checkBackendHealth,
    fetchUsers,
    addUser,
  }
}
