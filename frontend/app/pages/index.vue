<script setup lang="ts">
import type { B24Frame } from '@bitrix24/b24jssdk'
import { useDashboard } from '@bitrix24/b24ui-nuxt/utils/dashboard'
import { onMounted } from 'vue'
import { sleepAction } from '../utils/sleep'

const { t, locales: localesI18n, setLocale } = useI18n()
// const page = usePageStore()

useHead({
  title: t('page.index.seo.title')
})

// region Init ////
const { $logger, initApp, processErrorGlobal } = useAppInit('IndexPage')
const { $initializeB24Frame } = useNuxtApp()
let $b24: null | B24Frame = null
// endregion ////

console.error(new Error('@todo test > 2'))
const { contextId, isLoading, load } = useDashboard({ isLoading: ref(false), load: () => {} })
// const isLoading = computed({
//   get: () => isLoadingState?.value === true,
//   set: (value: boolean) => {
//     $logger.info(load, value, contextId, isLoadingState?.value)
//     load?.(value, contextId)
//   }
// })
const makeToggleLoading = async (timeout: number = 1000) => {
  load?.(!isLoading?.value, contextId)
  await sleepAction(timeout)
  load?.(!isLoading?.value, contextId)
}
// region Lifecycle Hooks ////
onMounted(async () => {
  $logger.info('Hi from index page')

  try {
    // isLoading.value = true
    // await sleepAction(10_500)
    $b24 = await $initializeB24Frame()
    await initApp($b24, localesI18n, setLocale)

    // await $b24.parent.setTitle(t('page.index.seo.title'))
  } catch (error) {
    processErrorGlobal(error)
  } finally {
    // isLoading.value = false
  }
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
      <ClientOnly>
        <BackendStatus />
      </ClientOnly>

      <B24Button loading-auto @click="makeToggleLoading(1500)">Test</B24Button>
    </B24Card>
  </div>
</template>
