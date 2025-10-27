<script setup lang="ts">
import { onMounted, getCurrentInstance } from 'vue'
import SuccessIcon from '@bitrix24/b24icons-vue/button/SuccessIcon'
import UnavailableIcon from '@bitrix24/b24icons-vue/main/UnavailableIcon'
import LoaderClockIcon from '@bitrix24/b24icons-vue/animated/LoaderClockIcon'
import { useDashboard } from '@bitrix24/b24ui-nuxt/utils/dashboard'
import { sleepAction } from '../utils/sleep'

const { t } = useI18n()
const { backendStatus, backendType, checkBackendHealth } = useBackend()

const checkHealth = async () => {
  await checkBackendHealth()
}

const statusIcon = computed(() => {
  switch (backendStatus.value) {
    case 'healthy': return SuccessIcon
    case 'checking': return LoaderClockIcon
    case 'error': return UnavailableIcon
  }

  return undefined
})

const statusColor = computed(() => {
  switch (backendStatus.value) {
    case 'healthy': return 'air-primary-success'
    case 'checking': return 'air-primary-success'
    case 'error': return 'air-primary-alert'
  }

  return 'air-secondary-accent-2'
})

const statusDescription = computed(() => {
  switch (backendStatus.value) {
    case 'healthy': return t('components.backendStatus.description.healthy', { msg: backendType.value })
    case 'checking': return t('components.backendStatus.description.checking')
    case 'error': return t('components.backendStatus.description.error')
  }

  return ''
})

const instance = getCurrentInstance()
console.log('📋 All provides in btn:', instance?.parent?.provides)
const { contextId, isLoading, load } = useDashboard({ isLoading: ref(false), load: () => {} })
const makeToggleLoading = async () => {
  console.log('📍 makeToggleLoading in btn:', contextId)
  load?.(!isLoading?.value, contextId)
  await sleepAction()
  load?.(!isLoading?.value, contextId)
}

onMounted(async () => {
  await checkHealth()
})
</script>

<template>
  <B24Alert
    :title="$t('components.backendStatus.title')"
    :icon="statusIcon"
    :color="statusColor"
    :description="statusDescription"
    size="sm"
    :actions="[
      {
        label: $t('components.backendStatus.action'),
        size: 'sm',
        onClick: makeToggleLoading
      }
    ]"
  />
</template>
