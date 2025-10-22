<script setup lang="ts">
// region Init ////
useHead({
  bodyAttrs: {
    // 'dark' | 'light' | 'edge-dark' | 'edge-light'
    class: `light light:[--air-theme-bg-color:#f9fafb]`
  }
})
// endregion ////
</script>

<template>
  <B24SidebarLayout
    :use-light-content="false"
    :b24ui="{
      root: 'overflow-y-hidden h-full min-h-full',
      pageWrapper: 'px-0',
      container: 'p-0 lg:p-0 mt-0 h-auto',
      containerWrapper: 'size-auto h-auto',
      containerWrapperInner: 'size-auto h-auto'
    }"
  >
    <!-- Content -->
    <slot />
  </B24SidebarLayout>
</template>

<style scoped>
.--app {
  scrollbar-gutter: auto;
}
</style>
