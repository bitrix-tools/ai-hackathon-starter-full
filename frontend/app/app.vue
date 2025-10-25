<script setup lang="ts">
import * as locales from '@bitrix24/b24ui-nuxt/locale'

// region Init ////
const { locale, defaultLocale } = useI18n()
const lang = computed(() => locales[locale.value]?.code || defaultLocale)
const dir = computed(() => locales[locale.value]?.dir || 'ltr')

useHead({
  htmlAttrs: { lang, dir }
})
// endregion ////
</script>

<template>
  <ClientOnly>
    <B24App :locale="locales[locale]">
      <NuxtLoadingIndicator color="var(--ui-color-design-filled-warning-bg)" :height="3" />
      <B24DashboardGroup class="border border-red-500">
        <NuxtLayout>
          <NuxtPage />
        </NuxtLayout>
      </B24DashboardGroup>
    </B24App>
  </ClientOnly>
</template>
