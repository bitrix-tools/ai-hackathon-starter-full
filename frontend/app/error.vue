<script setup lang="ts">
import type { NuxtError } from '#app'

defineProps<{
  error: NuxtError
}>()

useHead({
  meta: [
    { name: 'viewport', content: 'width=device-width, initial-scale=1' }
  ],
  link: [],
  htmlAttrs: { lang: 'en' }
})
</script>

<template>
  <B24App>
    <NuxtLoadingIndicator color="var(--ui-color-accent-main-primary)" :height="2" />

    <B24SidebarLayout :use-light-content="false">
      <B24Card class="mt-[2px]">
        <B24Error
          :error="error"
          :clear="false"
        />
      </B24Card>
    </B24SidebarLayout>
  </B24App>
</template>
