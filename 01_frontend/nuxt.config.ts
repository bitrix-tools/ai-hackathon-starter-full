export default defineNuxtConfig({
  devtools: { enabled: false },
  runtimeConfig: {
    public: {
      apiUrl: process.env.NUXT_PUBLIC_API_URL || 'http://localhost:3000'
    }
  },
  css: ['~/app/assets/css/main.css'],
  modules: ['@pinia/nuxt']
})
