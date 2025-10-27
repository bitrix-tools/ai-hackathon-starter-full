// import { useDashboard } from '@bitrix24/b24ui-nuxt/utils/dashboard'

/**
 * Page title && description
 */
export const usePageStore = defineStore(
  'page',
  () => {
    // region State ////
    const title = ref('')
    const description = ref('')
    // endregion ////

    // const { contextId, isLoading: isLoadingState, load } = useDashboard({ isLoading: ref(false), load: () => {} })
    //
    // const isLoading = computed({
    //   get: () => isLoadingState?.value === true,
    //   set: (value: boolean) => {
    //     load?.(value, contextId)
    //   }
    // })

    return {
      title,
      description
      // isLoading
    }
  }
)
