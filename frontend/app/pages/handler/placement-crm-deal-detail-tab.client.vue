<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useAppSettingsStore } from '~/stores/appSettings'
import type { B24Frame, TypePullMessage } from '@bitrix24/b24jssdk'
import TrendUpIcon from '@bitrix24/b24icons-vue/outline/TrendUpIcon'

type DataRecord = { x: number, y: number }

definePageMeta({
  layout: 'placement'
})

const { t, locales: localesI18n, setLocale } = useI18n()

// region Init ////
const { $logger, moduleId, initApp, reloadData, b24Helper, destroyB24Helper, usePullClient, useSubscribePullClient, startPullClient, processErrorGlobal } = useAppInit('crm_deal_detail_tab')
const appSettings = useAppSettingsStore()
const { $initializeB24Frame } = useNuxtApp()
let $b24: null | B24Frame = null
// endregion ////

// region Init ////
const data = ref<DataRecord[]>([])

const x = (d: DataRecord) => d.x
const y = (d: DataRecord) => d.y
const dropdownItems = ref(['CRM settings', 'My company details', 'Access permissions', 'CRM Payment', 'CRM.Delivery', 'Scripts', 'Create script', 'Install from Bitrix24.Market'])
const dropdownValue = ref('CRM Payment')
// endregion ////

// region Actions ////
async function refreshData() {
  let i = 0
  data.value = [
    { x: ++i, y: getRandomInt() },
    { x: ++i, y: getRandomInt() },
    { x: ++i, y: getRandomInt() },
    { x: ++i, y: getRandomInt() },
    { x: ++i, y: getRandomInt() },
    { x: ++i, y: getRandomInt() },
    { x: ++i, y: getRandomInt() }
  ]
}

function openSliderDemo() {
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
  await $b24?.parent.fitWindow()
}
// endregion ////

// region Lifecycle Hooks ////
onMounted(async () => {
  try {

    $b24 = await $initializeB24Frame()
    await initApp($b24, localesI18n, setLocale)

    usePullClient()
    useSubscribePullClient(
      makeSendPullCommandHandler.bind( this ),
      moduleId
    )
    startPullClient()
    await refreshData()
    await resizeWindow()

    $logger.info('Hi from crm-deal-detail-tab')

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

const dataList = ref([
  {
    id: '4600',
    date: '2024-03-11T15:30:00',
    status: 'paid',
    email: 'james.anderson@example.com',
    amount: 594
  },
  {
    id: '4599',
    date: '2024-03-11T10:10:00',
    status: 'failed',
    email: 'mia.white@example.com',
    amount: 276
  },
  {
    id: '4598',
    date: '2024-03-11T08:50:00',
    status: 'refunded',
    email: 'william.brown@example.com',
    amount: 315
  },
  {
    id: '4597',
    date: '2024-03-10T19:45:00',
    status: 'paid',
    email: 'emma.davis@example.com',
    amount: 529
  },
  {
    id: '4596',
    date: '2024-03-10T15:55:00',
    status: 'paid',
    email: 'ethan.harris@example.com',
    amount: 639
  }
])
</script>

<template>
  <div>
    <B24Card
      variant="outline"
      class="flex-1 w-full"
      :b24ui="{
        body: 'p-[8px] sm:px-[8px] sm:py-[8px]'
      }"
    >
      <div class="flex flex-row items-center justify-star gap-[8px]">
        <B24Badge
          rounded
          size="xl"
          color="air-primary-copilot"
          inverted
          :label="appSettings.configSettings.deviceHistoryCleanupDays"
          :icon="TrendUpIcon"
        />
        <div>
          <B24Select
            id="select"
            v-model="dropdownValue"
            class="w-[200px]"
            value-key="value"
            :items="dropdownItems"
            :content="{
              sideOffset: 4,
              collisionPadding: 1
            }"
          />
        </div>
      </div>
    </B24Card>
    <B24Card
      variant="outline"
      class="flex-1 w-full mt-[12px]"
      :b24ui="{
        header: 'p-[12px] px-[14px] py-[14px] sm:px-[14px] sm:py-[14px]',
        body: 'p-0 sm:px-0 sm:py-0'
      }"
    >
      <B24Table
        loading
        loading-color="air-primary"
        loading-animation="loading"
        :data="dataList"
        class="flex-1"
      />
    </B24Card>
  </div>
</template>
