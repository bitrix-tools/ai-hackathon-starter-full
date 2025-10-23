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

  // API
  const getEnum = async (): Promise<string[]> => {
    return await $api('/api/enum')
  }

  const getList = async (): Promise<string[]> => {
    return await $api('/api/enum')
  }

  const postInstall = async (data: Record<string, any>) => {
    return await $api('/api/install', {
      method: 'POST',
      body: JSON.stringify(data),
    })
  }

  return {
    checkHealth,
    getEnum,
    getList,
    postInstall,
  }
})
