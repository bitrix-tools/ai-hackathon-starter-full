export const useApiStore = defineStore('api', () => {
  const config = useRuntimeConfig()
  const apiUrl = config.public.apiUrl

  const $api = $fetch.create({
    baseURL: apiUrl,
    headers: {
      'Content-Type': 'application/json'
    }
  })

  // Health check
  const checkHealth = async (): Promise<{
    status: string
    backend: string
    timestamp: number
  }> => {
    try {
      return await $api('/api/health')
    } catch {
      throw new Error('Backend health check failed')
    }
  }

  // Users API
  const getUsers = async () => {
    return await $api('/api/users')
  }

  const createUser = async (userData: { name: string; email: string }) => {
    return await $api('/api/users', {
      method: 'POST',
      body: JSON.stringify(userData),
    })
  }

  return {
    checkHealth,
    getUsers,
    createUser,
  }
})
