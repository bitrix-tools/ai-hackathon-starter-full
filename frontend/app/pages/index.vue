<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'
import type { B24Frame } from '@bitrix24/b24jssdk'

const { t, locales: localesI18n, setLocale } = useI18n()

definePageMeta({
  layout: 'index-page'
})
useHead({
  title: t('page.index.seo.title')
})

// region Init ////
const { $logger, initLang, processErrorGlobal } = useAppInit('IndexPage')
const { $initializeB24Frame } = useNuxtApp()
const $b24: B24Frame = await $initializeB24Frame()
await initLang($b24, localesI18n, setLocale)
// endregion ////

// region Lifecycle Hooks ////
onMounted(async () => {
  $logger.info('Hi from index page')

  try {
    await $b24.parent.setTitle(t('page.index.seo.title'))
  } catch (error) {
    processErrorGlobal(error)
  }
})

onUnmounted(() => {
  $b24?.destroy()
})
// endregion ////
</script>

<template>
  <div class="flex flex-col items-center justify-center gap-16 h-[calc(100vh-200px)]">
    <B24Card>
      <template #header>
        <ProseH2>{{ $t('page.index.message.title') }}</ProseH2>
        <ProseP>{{ $t('page.index.message.line1') }}</ProseP>
      </template>
      <BackendStatus />
    </B24Card>
  </div>
</template>
