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

    return {
      title,
      description
    }
  }
)
