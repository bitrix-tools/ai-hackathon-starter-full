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
      <B24Error
        :error="error"
        :clear="false"
        :b24ui="{
          root: 'mt-[22px] min-h-[calc(100vh-200px)] bg-(--ui-color-design-outline-na-bg) h-[calc(100vh-200px)] p-[12px] rounded-[24px]'
        }"
      />
    </B24SidebarLayout>
  </B24App>
</template>
