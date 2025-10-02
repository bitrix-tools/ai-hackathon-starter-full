export default defineNuxtConfig({
  devtools: { enabled: true },
  ssr: false, // SPA mode для простоты
  runtimeConfig: {
    public: {
      apiUrl: process.env.NUXT_PUBLIC_API_URL || 'http://localhost:8000'
    }
  },
  modules: ['@pinia/nuxt'],
  app: {
    head: {
      title: 'Multi-Backend Starter',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' }
      ]
    }
  }
})
