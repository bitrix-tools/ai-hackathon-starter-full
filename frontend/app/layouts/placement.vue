<script setup lang="ts">
import { usePageStore } from '~/stores/page'
import BtnSpinnerIcon from '@bitrix24/b24icons-vue/button-specialized/BtnSpinnerIcon'

// region Init ////
useHead({
  bodyAttrs: {
    // 'dark' | 'light' | 'edge-dark' | 'edge-light'
    class: `light`
  }
})

const slots = defineSlots()

const page = usePageStore()
useSeoMeta({
  title: page.title,
  description: page.description
})
// endregion ////
</script>

<template>
  <B24SidebarLayout
    :use-light-content="false"
    :b24ui="{
      root: '',
      pageWrapper: 'flex flex-col h-[calc(100vh-0px)] min-h-full lg:grid lg:grid-cols-12 lg:gap-[22px] lg:pt-0 px-0 pb-[calc(53px+20px)]',
      container: 'p-0 lg:p-0 mt-0',
      containerWrapper: '',
      pageBottomWrapper: 'flex-0 relative'
    }"
  >
    <div v-if="page.isLoading">
      <div class="cursor-wait isolate absolute z-1000 inset-0 w-full flex flex-row flex-nowrap items-center justify-center h-[400px] min-h-[400px]">
        <BtnSpinnerIcon
          class="text-(--ui-color-design-plain-content-icon-secondary) size-[110px] animate-spin-slow"
          aria-hidden="true"
        />
      </div>
    </div>

    <!-- Content -->
    <div v-show="!page.isLoading">
      <slot />
    </div>

    <template v-if="!!slots['footer'] && !page.isLoading" #content-bottom>
      <div class="absolute inset-x-0 bottom-0 light bg-(--popup-window-background-color) fixed flex items-center justify-center gap-3 border-t-1 border-t-(--ui-color-divider-less) shadow-top-md py-[9px] px-2 pr-(--scrollbar-width)">
        <div class="flex flex-row gap-[10px]">
          <slot name="footer" />
        </div>
      </div>
    </template>
  </B24SidebarLayout>
</template>

<style scoped>
.--app {
  scrollbar-gutter: auto;
}
</style>
