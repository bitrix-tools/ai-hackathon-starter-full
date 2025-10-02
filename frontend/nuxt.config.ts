export default defineNuxtConfig({
  modules: ['@pinia/nuxt'],
  // @todo fix this
  ssr: false,
  devtools: { enabled: false },
  runtimeConfig: {
    /**
     * @memo this will be overwritten from .env or Docker_*
     * @see https://nuxt.com/docs/guide/going-further/runtime-config#example
     */
    public: {
      apiUrl: ''
    }
  },
  compatibilityDate: '2025-07-16',
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
