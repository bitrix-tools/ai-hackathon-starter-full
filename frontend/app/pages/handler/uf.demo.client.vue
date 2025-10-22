<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useAppSettingsStore } from '~/stores/appSettings'
import type { B24Frame, TypePullMessage } from '@bitrix24/b24jssdk'
import TrendUpIcon from '@bitrix24/b24icons-vue/outline/TrendUpIcon'
import TrendDownIcon from '@bitrix24/b24icons-vue/outline/TrendDownIcon'
import Settings5Icon from '@bitrix24/b24icons-vue/editor/Settings5Icon'

definePageMeta({
  layout: 'uf-placement'
})

const { t, locales: localesI18n, setLocale } = useI18n()

// region Init ////
const { $logger, moduleId, initApp, reloadData, b24Helper, destroyB24Helper, usePullClient, useSubscribePullClient, startPullClient, processErrorGlobal } = useAppInit('uf-placement')
const appSettings = useAppSettingsStore()
const { $initializeB24Frame } = useNuxtApp()
let $b24: null | B24Frame = null
const isEditMode = ref(false)
// endregion ////

// region Init Data ////
const dataField = ref(0)
// endregion ////

// region Actions ////
async function setData() {
  if (!$b24) { return }

  await $b24.placement.call('setValue', { value: dataField.value })

  $logger.warn('send >> ', { value: dataField.value })
}

function openSliderForOptions() {
  $b24?.slider.openSliderAppPage({
    place: 'app-options',
    bx24_width: 650,
    bx24_title: t('page.app-options.seo.title'),
  })
}

const makeSendPullCommandHandler = async (message: TypePullMessage) => {
  $logger.warn('<< pull.get <<<', message)

  if (message.command === 'reload.options') {
    $logger.info("Get pull command for update. Reinit the application")
    await reloadData()
  }
}
// endregion ////

// region Tools ////
function getRandomInt(max: number = 5) {
  return Math.floor(Math.random() * max)
}

async function resizeWindow() {
  await $b24?.parent.resizeWindowAuto(
    null,
    isEditMode.value ? 60 : 105
  )
}
// endregion ////

// region Lifecycle Hooks ////
onMounted(async () => {
  try {
    $b24 = await $initializeB24Frame()
    await initApp($b24, localesI18n, setLocale)

    isEditMode.value = $b24.placement.options['MODE'] === 'edit'

    if (isEditMode.value) {
      useHead({
        bodyAttrs: {
          class: `light light:[--air-theme-bg-color:#ffffff]`
        }
      })
    }
    usePullClient()
    useSubscribePullClient(
      makeSendPullCommandHandler.bind( this ),
      moduleId
    )
    startPullClient()
    await resizeWindow()

    $logger.info('Hi from uf-placement', $b24.placement.options)

    dataField.value = Number.parseInt($b24.placement.options?.VALUE || '0')

  } catch (error) {
    processErrorGlobal(error, {
      homePageIsHide: true,
      isShowClearError: true,
      clearErrorHref: '/handler/uf.demo.html'
    })
  }
})

onUnmounted(() => {
  if (b24Helper.value) {
    destroyB24Helper()
  }
})
// endregion ////
</script>

<template>
  <div>
    <template v-if="isEditMode">
      <B24FormField
        :label="$t('uf.demo.field.label')"
      >
        <B24Input
          v-model.number="dataField"
          @keyup="setData"
        />
      </B24FormField>
    </template>
    <template v-else>
      <B24Card
        class="flex-1 w-full"
        :b24ui="{
          header: 'p-4 sm:px-4 sm:py-4',
          body: 'p-4 sm:px-4 py-2',
        }"
      >
        <template #header>
          <div class="flex-1 w-full flex flex-row items-center justify-between gap-[10px]">
            <ProseP class="mb-0">{{ appSettings.configSettings.someValue_2 }}</ProseP>
            <B24Badge
              class="cursor-pointer"
              rounded
              size="md"
              :color="appSettings.configSettings.isSomeOption ? 'air-primary-copilot' : 'air-primary-alert'"
              inverted
              :label="appSettings.configSettings.someValue_1 + dataField"
              :icon="appSettings.configSettings.isSomeOption ? TrendUpIcon : TrendDownIcon"

            />
          </div>
        </template>
        <div class="flex-1 w-full flex flex-row items-center justify-end gap-[10px]">
          <B24Button
            :label="t('uf.demo.action.settings')"
            :icon="Settings5Icon"
            size="sm"
            @click="openSliderForOptions"
          />
        </div>
      </B24Card>
    </template>
  </div>
</template>
