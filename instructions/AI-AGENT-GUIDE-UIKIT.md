# Bitrix24 UI Kit — Руководство для ИИ агентов

## Краткая сводка

**Bitrix24 UI Kit** (`@bitrix24/b24ui-nuxt`) — это UI-библиотека для разработки веб-приложений через REST API Bitrix24 на базе Nuxt и Vue. Библиотека предоставляет готовые компоненты, повторяющие дизайн Bitrix24, что позволяет разработчикам быстро создавать красивые и консистентные приложения.

**Ключевые особенности:**
- Основан на Nuxt UI, но адаптирован под дизайн-систему Bitrix24
- Использует Tailwind CSS 4 и Tailwind Variants для стилизации
- Поддерживает Nuxt 4+ и Vue 3 + Vite
- Включает 80+ компонентов и composables
- Интегрируется с `@bitrix24/b24icons-vue` (1400+ иконок)

**Важно:** Всегда используйте компоненты именно из `@bitrix24/b24ui-nuxt`, а НЕ из Nuxt UI!

## Установка

### Для Nuxt (рекомендуется)

```bash
pnpm add @bitrix24/b24ui-nuxt @bitrix24/b24icons-vue
```

**nuxt.config.ts:**
```ts
export default defineNuxtConfig({
  modules: ['@bitrix24/b24ui-nuxt']
})
```

**assets/css/main.css:**
```css
@import "tailwindcss";
@import "@bitrix24/b24ui-nuxt";
```

**app.vue:**
```vue
<template>
  <B24App>
    <NuxtPage />
  </B24App>
</template>
```

📖 [Детальная инструкция по установке Nuxt](https://github.com/bitrix24/b24ui/blob/main/docs/content/docs/1.getting-started/2.installation/1.nuxt.md)

### Для Vue + Vite

**vite.config.ts:**
```ts
import bitrix24UIPluginVite from '@bitrix24/b24ui-nuxt/vite'

export default defineConfig({
  plugins: [vue(), bitrix24UIPluginVite()]
})
```

**main.ts:**
```ts
import b24UiPlugin from '@bitrix24/b24ui-nuxt/vue-plugin'
const router = createRouter({ routes: [], history: createWebHistory() })

app.use(router)
app.use(b24UiPlugin)
```

📖 [Детальная инструкция по установке Vue](https://github.com/bitrix24/b24ui/blob/main/docs/content/docs/1.getting-started/2.installation/2.vue.md)

## Основные требования

### 1. Обязательное использование B24App

Оборачивайте приложение в `<B24App>` для корректной работы Toast, Tooltip, Modal и программных оверлеев:

```vue
<template>
  <B24App>
    <!-- ваш контент -->
  </B24App>
</template>
```

📖 [Компонент B24App](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/App.vue)

### 2. Префикс компонентов

Все компоненты используют префикс `B24`:
- `<B24Button>`, `<B24Input>`, `<B24Modal>`, `<B24Form>` и т.д.

### 3. Работа с иконками

Иконки импортируются из `@bitrix24/b24icons-vue`:

```vue
<script setup>
import RocketIcon from '@bitrix24/b24icons-vue/main/RocketIcon'
</script>

<template>
  <B24Button :icon="RocketIcon" label="Запуск" />
</template>
```

📖 [Полный список иконок](https://bitrix24.github.io/b24icons/)  
📖 [Метаданные иконок](https://github.com/bitrix24/b24icons-vue/blob/main/dist/metadata.json)  
📖 [Интеграция иконок](https://github.com/bitrix24/b24ui/blob/main/docs/content/docs/1.getting-started/6.integrations/1.icons/1.nuxt.md)

### 4. Стилизация через b24ui и class

Компоненты поддерживают два способа кастомизации:

**Через `b24ui` prop** (для слотов внутри компонента):
```vue
<B24Button 
  :b24ui="{ 
    baseLine: 'justify-center', 
    leadingIcon: 'text-red-500' 
  }" 
/>
```

**Через `class` prop** (для корневого элемента):
```vue
<B24Button class="font-bold rounded-full" />
```

📖 [Кастомизация компонентов](https://github.com/bitrix24/b24ui/blob/main/docs/content/docs/1.getting-started/5.theme/3.components.md)

### 5. Варианты темы (theme variants)

Компоненты используют систему вариантов. Основные параметры:

**color:** `air-primary`, `air-secondary-no-accent`, `air-primary-success`, `air-primary-alert`, `air-primary-warning`, `air-primary-copilot`, `air-secondary-accent`, `air-tertiary` и др.

**size:** `xss`, `xs`, `sm`, `md`, `lg`, `xl` (зависит от компонента)

**Пример:**
```vue
<B24Button 
  color="air-primary" 
  size="md" 
  rounded
  loading-auto
/>
```

📖 [Все theme файлы](https://github.com/bitrix24/b24ui/tree/main/src/theme)

## Основные компоненты

### Кнопки и действия

**B24Button** — основная кнопка  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Button.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/button.ts)  
```vue
<B24Button 
  label="Сохранить"
  color="air-primary"
  size="md"
  :icon="SaveIcon"
  loading-auto
  @click="handleSave"
/>
```

**B24FieldGroup** — группа кнопок  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/FieldGroup.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/field-group.ts)

### Формы и ввод

**B24Input** — текстовый инпут  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Input.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/input.ts)  
```vue
<B24Input 
  v-model.trim="email"
  type="email"
  placeholder="Email"
  color="air-primary"
  size="md"
/>
```

**B24Textarea** — многострочный ввод  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Textarea.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/textarea.ts)

**B24Select** — выпадающий список  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Select.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/select.ts)  
```vue
<B24Select
  v-model="selected"
  :items="[
    { label: 'Опция 1', value: '1' },
    { label: 'Опция 2', value: '2' }
  ]"
  placeholder="Выберите..."
/>
```

**B24Checkbox** — чекбокс  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Checkbox.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/checkbox.ts)

**B24RadioGroup** — группа радио-кнопок  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/RadioGroup.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/radio-group.ts)

**B24Switch** — переключатель  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Switch.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/switch.ts)

**B24Form + B24FormField** — форма с валидацией (Zod, Valibot, Yup и др.)  
📖 [Исходный код Form](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Form.vue)  
📖 [Исходный код FormField](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/FormField.vue)  
📖 [Theme Form](https://github.com/bitrix24/b24ui/blob/main/src/theme/form.ts)  
📖 [Theme FormField](https://github.com/bitrix24/b24ui/blob/main/src/theme/form-field.ts)  
📖 [Документация](https://github.com/bitrix24/b24ui/blob/main/docs/content/docs/2.components/form.md)

```vue
<B24Form :state="state" :schema="schema" @submit="onSubmit">
  <B24FormField name="email" label="Email" required>
    <B24Input v-model="state.email" type="email" />
  </B24FormField>
  
  <B24Button type="submit" color="air-primary" loading-auto>
    Отправить
  </B24Button>
</B24Form>
```

### Оверлеи и уведомления

**B24Modal** — модальное окно  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Modal.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/modal.ts)  
📖 [Документация](https://github.com/bitrix24/b24ui/blob/main/docs/content/docs/2.components/modal.md)

```vue
<B24Modal title="Заголовок" description="Описание">
  <B24Button label="Открыть" />
  
  <template #body>
    <p>Содержимое модалки</p>
  </template>
  
  <template #footer="{ close }">
    <B24Button color="air-primary" @click="save">Сохранить</B24Button>
    <B24Button color="air-tertiary" @click="close">Отмена</B24Button>
  </template>
</B24Modal>
```

**B24Slideover** — боковая панель  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Slideover.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/slideover.ts)

**B24Toast** — уведомления (обычно через `useToast()`)  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Toast.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/toast.ts)

**B24Tooltip** — всплывающие подсказки  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Tooltip.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/tooltip.ts)

**B24Popover** — всплывающий контейнер  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Popover.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/popover.ts)

### Навигация и меню

**B24NavigationMenu** — навигационное меню  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/NavigationMenu.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/navigation-menu.ts)

**B24DropdownMenu** — выпадающее меню  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/DropdownMenu.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/dropdown-menu.ts)

**B24Tabs** — вкладки  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Tabs.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/tabs.ts)

**B24Breadcrumb** — хлебные крошки  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Breadcrumb.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/breadcrumb.ts)

### Макеты (Layouts)

**B24SidebarLayout** — основной layout с боковой панелью  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/SidebarLayout.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/sidebar-layout.ts)

Связанные компоненты:
- **B24Sidebar** — [исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Sidebar.vue)
- **B24SidebarHeader** — [исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/SidebarHeader.vue)
- **B24SidebarBody** — [исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/SidebarBody.vue)
- **B24SidebarFooter** — [исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/SidebarFooter.vue)
- **B24Navbar** — [исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Navbar.vue)

### Отображение данных

**B24Avatar / B24AvatarGroup** — аватары  
📖 [Avatar: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Avatar.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/avatar.ts)  
📖 [AvatarGroup: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/AvatarGroup.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/avatar-group.ts)

**B24Badge** — бейджи  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Badge.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/badge.ts)

**B24Chip** — числовые метки  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Chip.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/chip.ts)

**B24Card** — карточка  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Card.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/card.ts)

**B24Alert / B24Advice** — предупреждения и советы  
📖 [Alert: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Alert.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/alert.ts)  
📖 [Advice: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Advice.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/advice.ts)

**B24Table / B24TableWrapper** — таблицы  
📖 [Table: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Table.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/table.ts)  
📖 [TableWrapper: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/TableWrapper.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/table-wrapper.ts)

**B24DescriptionList** — список описаний  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/DescriptionList.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/description-list.ts)

**B24Skeleton** — skeleton loader  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Skeleton.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/skeleton.ts)

**B24Empty** — пустое состояние  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Empty.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/empty.ts)

### Другие компоненты

**B24Progress** — прогресс-бар  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Progress.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/progress.ts)

**B24Range** — слайдер  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Range.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/range.ts)

**B24Calendar** — календарь  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Calendar.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/calendar.ts)

**B24Countdown** — таймер обратного отсчета  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Countdown.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/countdown.ts)

**B24Accordion / B24Collapsible** — раскрывающиеся блоки  
📖 [Accordion: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Accordion.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/accordion.ts)  
📖 [Collapsible: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Collapsible.vue) / [theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/collapsible.ts)

**B24Separator** — разделитель  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Separator.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/separator.ts)

**B24Kbd** — клавиша клавиатуры  
📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/components/Kbd.vue)  
📖 [Theme](https://github.com/bitrix24/b24ui/blob/main/src/theme/kbd.ts)

## Основные Composables

### useToast()

Управление уведомлениями.

📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/useToast.ts)

```ts
const toast = useToast()

// Добавить уведомление
toast.add({ 
  title: 'Успех', 
  description: 'Данные сохранены',
  color: 'air-primary-success' 
})

// Обновить
toast.update(id, { title: 'Обновлено' })

// Удалить
toast.remove(id)

// Очистить все
toast.clear()
```

### useOverlay()

Программное управление модальными окнами и slideоvers.

📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/useOverlay.ts)

```ts
const overlay = useOverlay()

// Создать экземпляр
const modal = overlay.create(MyModalComponent, { 
  props: { title: 'Заголовок' },
  destroyOnClose: true
})

// Открыть и получить результат
const opened = modal.open({ additionalProp: 'value' })
const result = await opened.result // ждет emit('close', payload)

// Закрыть
modal.close(resultValue)

// Обновить props на лету
modal.patch({ title: 'Новый заголовок' })
```

### defineShortcuts()

Регистрация глобальных горячих клавиш.

📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/defineShortcuts.ts)

```ts
defineShortcuts({
  'meta_k': () => openCommandPalette(),
  'escape': () => closeAllOverlays(),
  'ctrl_s': (e) => { e.preventDefault(); save() }
})
```

### useLocale() / defineLocale()

Интернационализация.

📖 [useLocale: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/useLocale.ts)  
📖 [defineLocale: исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/defineLocale.ts)

### useConfetti()

Конфетти-анимация.

📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/useConfetti.ts)

```ts
const confetti = useConfetti()
confetti.fire()
```

### useFormField()

Интеграция с формой для кастомных компонентов.

📖 [Исходный код](https://github.com/bitrix24/b24ui/blob/main/src/runtime/composables/useFormField.ts)

## Что делать при ошибках

### 1. Проверьте установку и конфигурацию

Убедитесь, что:
- `@bitrix24/b24ui-nuxt` установлен
- Модуль добавлен в `nuxt.config.ts`
- CSS импортирован
- Приложение обернуто в `<B24App>`

### 2. Проверьте правильность использования компонента

1. **Найдите исходный код компонента** в GitHub:
   - Перейдите в [src/runtime/components/](https://github.com/bitrix24/b24ui/tree/main/src/runtime/components)
   - Откройте нужный компонент (например, `Button.vue`)
   - Изучите `Props`, `Slots`, `Emits`

2. **Проверьте theme** компонента:
   - Перейдите в [src/theme/](https://github.com/bitrix24/b24ui/tree/main/src/theme)
   - Откройте файл темы (например, `button.ts`)
   - Проверьте доступные `variants`, `slots`, `defaultVariants`

3. **Изучите документацию** (если есть):
   - [Основная документация](https://bitrix24.github.io/b24ui/)
   - [Файлы документации](https://github.com/bitrix24/b24ui/tree/main/docs/content/docs/2.components)

### 3. Проверьте совместимость версий

```json
{
  "dependencies": {
    "@bitrix24/b24ui-nuxt": "^2.0.0",
    "@bitrix24/b24icons-vue": "^2.0.0",
    "nuxt": "^4.0.0",
    "vue": "^3.0.0"
  }
}
```

### 4. Типичные проблемы и решения

**Проблема:** Компоненты не рендерятся  
**Решение:** Проверьте наличие `<B24App>` в корне приложения

**Проблема:** Toast/Tooltip/Modal не работают  
**Решение:** Убедитесь, что используется `<B24App>` (предоставляет `OverlayProvider`)

**Проблема:** Иконки не отображаются  
**Решение:** Проверьте правильность импорта из `@bitrix24/b24icons-vue/category/IconName`

**Проблема:** Стили не применяются  
**Решение:** Убедитесь, что CSS импортирован (`@import "@bitrix24/b24ui-nuxt"`)

**Проблема:** TypeScript ошибки  
**Решение:** Проверьте типы в исходниках компонента или theme файле

### 5. Ссылки для диагностики

- **Исходный код модуля:** [src/module.ts](https://github.com/bitrix24/b24ui/blob/main/src/module.ts)
- **Все компоненты:** [src/runtime/components/](https://github.com/bitrix24/b24ui/tree/main/src/runtime/components)
- **Все composables:** [src/runtime/composables/](https://github.com/bitrix24/b24ui/tree/main/src/runtime/composables)
- **Все темы:** [src/theme/](https://github.com/bitrix24/b24ui/tree/main/src/theme)
- **Примеры использования:** [playgrounds/demo/app/](https://github.com/bitrix24/b24ui/tree/main/playgrounds/demo/app)
- **Тесты компонентов:** [test/components/](https://github.com/bitrix24/b24ui/tree/main/test/components)

## Полезные ресурсы

- 📚 [Официальная документация](https://bitrix24.github.io/b24ui/)
- 🎨 [Иконки Bitrix24](https://bitrix24.github.io/b24icons/)
- 💾 [GitHub репозиторий](https://github.com/bitrix24/b24ui)
- 🎮 [Demo приложение](https://bitrix24.github.io/b24ui/demo/)
- 🚀 [Starter template](https://github.com/bitrix24/starter-b24ui)
- 📖 [README для ИИ (расширенный)](https://github.com/bitrix24/b24ui/blob/main/README-AI.md)

## Принципы работы с UI Kit

1. **Всегда используйте B24 префикс** — это компоненты Bitrix24 UI, а не Nuxt UI
2. **Оборачивайте в B24App** — обязательно для работы оверлеев и уведомлений
3. **Используйте b24ui prop** — для точной кастомизации слотов компонента
4. **Следуйте дизайн-системе** — используйте предустановленные цвета (`air-*`) и размеры
5. **Проверяйте theme** — для понимания доступных вариантов и слотов
6. **Изучайте исходники** — они хорошо документированы и содержат TypeScript типы

---

**Версия:** 2.0.2  
**Последнее обновление:** Октябрь 2025  
**Лицензия:** MIT

