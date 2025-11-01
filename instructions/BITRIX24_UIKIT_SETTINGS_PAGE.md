# Страница настроек с боковым меню

> **⚠️ ВАЖНО ДЛЯ ИИ АГЕНТОВ**: Используйте компоненты **B24*** из Bitrix24 UI Kit, а НЕ U* из Nuxt UI!

## 📋 Описание

Страница настроек с боковым меню навигации - типичный паттерн для мастеров настроек и конфигураций в Bitrix24.

### Применение

- ⚙️ Страницы настроек приложения
- 🧙 Мастера настроек (пошаговые конфигурации)
- 📊 Разделы с подразделами
- 🎛️ Управление модулями и инструментами
- 🔧 Конфигурация параметров системы

---

## 🎯 Базовый пример

### Минимальный код

```vue
<template>
  <div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r">
      <nav class="p-4">
        <div 
          v-for="item in menuItems" 
          :key="item.id"
          @click="currentSection = item.id"
          class="p-2 cursor-pointer rounded"
          :class="currentSection === item.id ? 'bg-blue-500 text-white' : 'text-gray-700'"
        >
          {{ item.label }}
        </div>
      </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-8">
      <h1 class="text-3xl font-bold mb-4">{{ currentTitle }}</h1>
      <p>Контент раздела</p>
    </main>
  </div>
</template>

<script setup>
const menuItems = ref([
  { id: 'settings1', label: 'Settings1' },
  { id: 'settings2', label: 'Settings2' },
  { id: 'settings3', label: 'Settings3' }
])

const currentSection = ref('settings1')
const currentTitle = computed(() => 
  menuItems.value.find(i => i.id === currentSection.value)?.label
)
</script>
```

---

## 💼 Полный пример: Страница настроек как в Bitrix24

```vue
<template>
  <B24App>
    <div class="flex min-h-screen bg-gray-50">
      <!-- Левое боковое меню -->
      <aside class="w-80 bg-white border-r border-gray-200">
        <div class="p-6">
          <!-- Заголовок -->
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">
            Настройки
          </h2>

          <!-- Поиск -->
          <B24Input
            v-model="searchQuery"
            :icon="SearchIcon"
            placeholder="Поиск"
            class="mb-6"
          />

          <!-- Меню навигации -->
          <nav class="space-y-1">
            <div
              v-for="item in filteredMenuItems"
              :key="item.id"
              @click="selectSection(item.id)"
              :class="[
                'px-4 py-2.5 rounded-lg cursor-pointer transition-all',
                currentSection === item.id
                  ? 'bg-blue-500 text-white font-semibold'
                  : 'text-gray-700 hover:bg-gray-100'
              ]"
            >
              {{ item.label }}
            </div>
          </nav>
        </div>
      </aside>

      <!-- Основной контент -->
      <main class="flex-1 overflow-auto">
        <B24Container class="py-8 px-12 max-w-6xl">
          <!-- Заголовок секции -->
          <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-3">
              {{ currentSectionData?.title }}
            </h1>
            <p class="text-gray-600 text-lg">
              {{ currentSectionData?.description }}
            </p>
          </div>

          <!-- Динамический контент в зависимости от секции -->
          <component 
            :is="currentComponent" 
            :section-data="currentSectionData"
            @update="handleUpdate"
          />
        </B24Container>
      </main>
    </div>
  </B24App>
</template>

<script setup lang="ts">
import { ref, computed, defineAsyncComponent } from 'vue'
import { SearchIcon } from '@bitrix24/b24icons'

// Типы
interface MenuItem {
  id: string
  label: string
  component: string
  title: string
  description: string
}

// Динамическая загрузка компонентов секций
const Settings1Section = defineAsyncComponent(() => 
  import('./components/Settings/Settings1Section.vue')
)
const Settings2Section = defineAsyncComponent(() => 
  import('./components/Settings/Settings2Section.vue')
)
const Settings3Section = defineAsyncComponent(() => 
  import('./components/Settings/Settings3Section.vue')
)

// State
const currentSection = ref('settings1')
const searchQuery = ref('')

// Пункты меню
const menuItems = ref<MenuItem[]>([
  {
    id: 'settings1',
    label: 'Settings1',
    component: 'Settings1Section',
    title: 'Settings1',
    description: 'Настройте параметры первого раздела'
  },
  {
    id: 'settings2',
    label: 'Settings2',
    component: 'Settings2Section',
    title: 'Settings2',
    description: 'Управление вторым разделом настроек'
  },
  {
    id: 'settings3',
    label: 'Settings3',
    component: 'Settings3Section',
    title: 'Settings3',
    description: 'Конфигурация третьего раздела'
  }
])

// Фильтрация меню по поиску
const filteredMenuItems = computed(() => {
  if (!searchQuery.value) return menuItems.value
  
  const query = searchQuery.value.toLowerCase()
  return menuItems.value.filter(item =>
    item.label.toLowerCase().includes(query) ||
    item.title.toLowerCase().includes(query)
  )
})

// Текущая секция
const currentSectionData = computed(() => 
  menuItems.value.find(item => item.id === currentSection.value)
)

// Динамический компонент
const currentComponent = computed(() => {
  const componentMap = {
    'Settings1Section': Settings1Section,
    'Settings2Section': Settings2Section,
    'Settings3Section': Settings3Section
  }
  return componentMap[currentSectionData.value?.component] || null
})

// Методы
const selectSection = (sectionId: string) => {
  currentSection.value = sectionId
  
  // Обновление URL (опционально)
  const url = new URL(window.location.href)
  url.searchParams.set('section', sectionId)
  window.history.pushState({}, '', url)
}

const handleUpdate = (data: any) => {
  console.log('Settings updated:', data)
  useToast().add({
    title: 'Сохранено',
    description: 'Настройки успешно обновлены',
    color: 'green'
  })
}

// Восстановление секции из URL при загрузке
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const sectionFromUrl = urlParams.get('section')
  
  if (sectionFromUrl && menuItems.value.some(i => i.id === sectionFromUrl)) {
    currentSection.value = sectionFromUrl
  }
})
</script>
```

---

## 🎨 Компоненты секций

### Settings1Section.vue - Секция с переключателями

```vue
<template>
  <div class="space-y-6">
    <!-- Заголовок подраздела -->
    <div class="flex items-center gap-2 mb-6">
      <B24Icon :icon="SettingsIcon" class="w-5 h-5 text-gray-500" />
      <h2 class="text-xl font-semibold text-gray-800">
        Какие инструменты показывать в меню
      </h2>
    </div>

    <!-- Информационный блок -->
    <B24Card class="bg-blue-50 border-blue-200">
      <div class="p-4">
        <p class="text-sm text-gray-700">
          Выберите, с какими инструментами вы будете работать в Битрикс24.
          Доступ к остальным инструментам будет закрыт для всех сотрудников.
          <a href="#" class="text-blue-600 hover:underline">Подробнее</a>
        </p>
      </div>
    </B24Card>

    <!-- Список настроек с переключателями -->
    <div class="space-y-3">
      <B24Card
        v-for="tool in tools"
        :key="tool.id"
        class="border-2 transition-all"
        :class="tool.enabled ? 'border-blue-500' : 'border-gray-200'"
      >
        <div class="p-5 flex items-center justify-between">
          <!-- Левая часть: Toggle + Название -->
          <div class="flex items-center gap-4">
            <!-- Toggle Switch -->
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="tool.enabled"
                class="sr-only peer"
                @change="updateTool(tool)"
              >
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                <span class="absolute left-1 top-1 text-[10px] font-bold text-white">
                  {{ tool.enabled ? 'ВКЛ' : '' }}
                </span>
              </div>
            </label>

            <!-- Название инструмента -->
            <div class="flex items-center gap-2">
              <span class="text-base font-medium text-gray-900">
                {{ tool.name }}
              </span>
              <B24Icon 
                v-if="tool.hasSubmenu"
                :icon="ChevronDownIcon" 
                class="w-4 h-4 text-gray-400"
              />
            </div>
          </div>

          <!-- Правая часть: Кнопка действия -->
          <B24Button
            v-if="tool.actionLabel"
            size="sm"
            color="air-secondary-no-accent"
            @click="performAction(tool)"
          >
            {{ tool.actionLabel }}
          </B24Button>
        </div>

        <!-- Подменю (если есть) -->
        <div v-if="tool.hasSubmenu && tool.showSubmenu" class="px-5 pb-5 pt-2">
          <div class="pl-12 space-y-2">
            <div 
              v-for="subItem in tool.subItems" 
              :key="subItem.id"
              class="flex items-center gap-2 text-sm text-gray-600"
            >
              <B24Icon :icon="CheckIcon" class="w-4 h-4 text-green-500" />
              {{ subItem.name }}
            </div>
          </div>
        </div>
      </B24Card>
    </div>

    <!-- Кнопка сохранения -->
    <div class="flex gap-3 pt-6 border-t">
      <B24Button
        color="air-primary"
        :loading="saving"
        @click="saveSettings"
      >
        Сохранить изменения
      </B24Button>
      
      <B24Button
        color="air-secondary-no-accent"
        @click="resetSettings"
      >
        Отменить
      </B24Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import {
  SettingsIcon,
  ChevronDownIcon,
  CheckIcon
} from '@bitrix24/b24icons'

interface Tool {
  id: string
  name: string
  enabled: boolean
  actionLabel?: string
  hasSubmenu?: boolean
  showSubmenu?: boolean
  subItems?: Array<{ id: string; name: string }>
}

const tools = ref<Tool[]>([
  {
    id: 'collaboration',
    name: 'Совместная работа',
    enabled: true,
    actionLabel: 'Перейти',
    hasSubmenu: false
  },
  {
    id: 'tasks',
    name: 'Задачи и проекты',
    enabled: true,
    actionLabel: 'Права доступа',
    hasSubmenu: false
  },
  {
    id: 'crm',
    name: 'CRM',
    enabled: true,
    actionLabel: 'Настройки',
    hasSubmenu: true,
    showSubmenu: false,
    subItems: [
      { id: 'leads', name: 'Лиды' },
      { id: 'deals', name: 'Сделки' },
      { id: 'contacts', name: 'Контакты' }
    ]
  },
  {
    id: 'warehouse',
    name: 'Складской учёт',
    enabled: true,
    actionLabel: 'Перейти',
    hasSubmenu: false
  },
  {
    id: 'sites',
    name: 'Сайты и Магазины',
    enabled: true,
    actionLabel: 'Перейти',
    hasSubmenu: false
  }
])

const saving = ref(false)

const updateTool = (tool: Tool) => {
  console.log('Tool updated:', tool)
}

const performAction = (tool: Tool) => {
  if (tool.hasSubmenu) {
    tool.showSubmenu = !tool.showSubmenu
  } else {
    console.log('Navigate to:', tool.id)
  }
}

const saveSettings = async () => {
  saving.value = true
  
  try {
    // Сохранение настроек через API
    await fetch('/api/settings/tools', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        tools: tools.value.map(t => ({
          id: t.id,
          enabled: t.enabled
        }))
      })
    })
    
    useToast().add({
      title: 'Сохранено',
      description: 'Настройки успешно обновлены',
      color: 'green'
    })
  } catch (error) {
    useToast().add({
      title: 'Ошибка',
      description: 'Не удалось сохранить настройки',
      color: 'red'
    })
  } finally {
    saving.value = false
  }
}

const resetSettings = () => {
  // Сброс к исходным значениям
}
</script>
```

### Settings2Section.vue - Секция с формой

```vue
<template>
  <div class="space-y-6">
    <B24Card>
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Основные параметры</h3>
        
        <div class="space-y-4">
          <B24FormField label="Название компании" required>
            <B24Input v-model="form.companyName" />
          </B24FormField>

          <B24FormField label="Email">
            <B24Input v-model="form.email" type="email" />
          </B24FormField>

          <B24FormField label="Часовой пояс">
            <B24Select
              v-model="form.timezone"
              :options="timezoneOptions"
            />
          </B24FormField>

          <B24FormField label="Описание">
            <B24Textarea
              v-model="form.description"
              :rows="4"
            />
          </B24FormField>
        </div>

        <div class="flex gap-3 mt-6 pt-6 border-t">
          <B24Button color="air-primary" @click="saveForm">
            Сохранить
          </B24Button>
          <B24Button color="air-secondary-no-accent" @click="cancelForm">
            Отменить
          </B24Button>
        </div>
      </div>
    </B24Card>
  </div>
</template>

<script setup lang="ts">
const form = reactive({
  companyName: '',
  email: '',
  timezone: 'UTC',
  description: ''
})

const timezoneOptions = [
  { value: 'UTC', label: 'UTC' },
  { value: 'Europe/Moscow', label: 'Москва (UTC+3)' },
  { value: 'America/New_York', label: 'Нью-Йорк (UTC-5)' }
]

const saveForm = () => {
  console.log('Save form:', form)
}

const cancelForm = () => {
  // Reset form
}
</script>
```

### Settings3Section.vue - Секция со списком

```vue
<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h3 class="text-lg font-semibold">Список элементов</h3>
      <B24Button
        :icon="PlusIcon"
        color="air-primary"
        @click="addItem"
      >
        Добавить
      </B24Button>
    </div>

    <B24Table
      :columns="columns"
      :data="items"
      :loading="loading"
    >
      <template #actions="{ row }">
        <div class="flex gap-2">
          <B24Button
            :icon="EditIcon"
            size="xs"
            color="air-tertiary"
            @click="editItem(row)"
          />
          <B24Button
            :icon="TrashIcon"
            size="xs"
            color="air-tertiary"
            @click="deleteItem(row)"
          />
        </div>
      </template>
    </B24Table>
  </div>
</template>

<script setup lang="ts">
import { PlusIcon, EditIcon, TrashIcon } from '@bitrix24/b24icons'

const items = ref([])
const loading = ref(false)

const columns = [
  { key: 'id', header: 'ID' },
  { key: 'name', header: 'Название' },
  { key: 'status', header: 'Статус' },
  { key: 'actions', header: 'Действия' }
]

const addItem = () => {
  console.log('Add item')
}

const editItem = (item) => {
  console.log('Edit:', item)
}

const deleteItem = (item) => {
  console.log('Delete:', item)
}
</script>
```

---

## 🎨 Стилизация под Bitrix24

### Активный пункт меню

```vue
<style scoped>
.menu-item-active {
  @apply bg-blue-500 text-white font-semibold shadow-sm;
}

.menu-item-inactive {
  @apply text-gray-700 hover:bg-gray-100;
}
</style>
```

### Toggle Switch (как в Bitrix24)

```vue
<template>
  <label class="relative inline-flex items-center cursor-pointer">
    <input
      type="checkbox"
      v-model="enabled"
      class="sr-only peer"
    >
    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
      <span class="absolute left-1 top-1 text-[10px] font-bold text-white">
        {{ enabled ? 'ВКЛ' : '' }}
      </span>
    </div>
  </label>
</template>

<script setup>
const enabled = ref(true)
</script>
```

---

## 💡 Use Cases

### 1. Сохранение в localStorage

```typescript
const saveToLocalStorage = () => {
  localStorage.setItem('currentSection', currentSection.value)
}

onMounted(() => {
  const saved = localStorage.getItem('currentSection')
  if (saved) currentSection.value = saved
})
```

### 2. Роутинг с Vue Router

```typescript
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const selectSection = (sectionId: string) => {
  router.push({
    name: 'settings',
    query: { section: sectionId }
  })
}

watch(() => route.query.section, (newSection) => {
  if (newSection) currentSection.value = newSection as string
})
```

### 3. Breadcrumbs

```vue
<template>
  <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
    <span>Настройки</span>
    <B24Icon :icon="ChevronRightIcon" class="w-4 h-4" />
    <span class="font-semibold text-gray-900">
      {{ currentSectionData?.title }}
    </span>
  </div>
</template>
```

---

## 📊 Props и типы

```typescript
interface MenuItem {
  id: string              // Уникальный ID
  label: string           // Текст в меню
  component: string       // Имя компонента
  title: string           // Заголовок секции
  description: string     // Описание секции
  icon?: Component        // Иконка (опционально)
}

interface SectionProps {
  sectionData: MenuItem
}
```

---

## 🔗 Интеграция с Backend

```typescript
// Загрузка настроек
const loadSettings = async (sectionId: string) => {
  const response = await fetch(`/api/settings/${sectionId}`)
  return await response.json()
}

// Сохранение настроек
const saveSettings = async (sectionId: string, data: any) => {
  await fetch(`/api/settings/${sectionId}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
}
```

---

## 🎯 Best Practices

### ✅ Рекомендуется

1. **Lazy loading компонентов секций**
   ```typescript
   const Settings1 = defineAsyncComponent(() => 
     import('./Settings1Section.vue')
   )
   ```

2. **Сохранение текущей секции в URL**
   ```typescript
   window.history.pushState({}, '', `?section=${sectionId}`)
   ```

3. **Показывать индикатор загрузки при смене секций**

### ❌ Избегайте

1. Не загружайте все секции сразу
2. Не забывайте про обработку ошибок
3. Не делайте меню слишком длинным (используйте группировку)

---

## 📚 Ресурсы

- **Bitrix24 UI Kit**: https://bitrix24.github.io/b24ui/llms-full.txt
- **Toggle Switch**: https://flowbite.com/docs/forms/toggle/
- **REST API**: https://apidocs.bitrix24.ru/

---

**Дата**: Октябрь 2025  
**Версия**: 1.0 (Bitrix24 UI Kit)  
**Компоненты**: B24App, B24Card, B24Input, B24Button, B24FormField и др.

