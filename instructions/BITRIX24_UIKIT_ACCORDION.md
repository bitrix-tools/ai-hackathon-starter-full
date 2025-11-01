# B24Accordion: Раскрывающийся список объектов

> **⚠️ ВАЖНО ДЛЯ ИИ АГЕНТОВ**: Используйте компонент **B24Accordion** из Bitrix24 UI Kit, а НЕ UAccordion из Nuxt UI!

## 📋 Описание

**B24Accordion** - компонент для отображения раскрывающегося списка элементов.

### Применение

- 📝 FAQ и часто задаваемые вопросы
- 📊 Списки объектов с деталями
- 📁 Группировка данных по категориям
- 🔍 Просмотр детальной информации

---

## 🎯 Базовый пример

### Минимальный код

```vue
<template>
  <B24Accordion :items="items" />
</template>

<script setup>
const items = ref([
  {
    label: 'Объект 1',
    content: 'Описание объекта 1'
  },
  {
    label: 'Объект 2',
    content: 'Описание объекта 2'
  }
])
</script>
```

---

## 💼 Полный пример: Список объектов с данными

```vue
<template>
  <B24App>
    <B24Container class="py-8">
      <!-- Заголовок -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold">📚 Список объектов</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Раскрывающийся список с детальной информацией
        </p>
      </div>

      <!-- Панель управления -->
      <B24Card class="mb-6">
        <div class="flex items-center justify-between">
          <div class="flex gap-2">
            <B24Button 
              :icon="PlusIcon" 
              color="air-primary"
              @click="addItem"
            >
              Добавить
            </B24Button>
            
            <B24Button 
              :icon="RefreshIcon"
              color="air-secondary-no-accent"
              @click="refreshItems"
            >
              Обновить
            </B24Button>
          </div>

          <B24FormField label="" class="m-0">
            <B24Input
              v-model="searchQuery"
              :icon="SearchIcon"
              placeholder="Поиск..."
              class="w-64"
            />
          </B24FormField>
        </div>
      </B24Card>

      <!-- Статистика -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <B24Card>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Всего объектов</p>
              <p class="text-2xl font-bold">{{ totalItems }}</p>
            </div>
            <B24Icon :icon="ChartIcon" class="w-8 h-8 text-primary" />
          </div>
        </B24Card>

        <B24Card>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Активных</p>
              <p class="text-2xl font-bold text-green-600">{{ activeItems }}</p>
            </div>
            <B24Icon :icon="CheckIcon" class="w-8 h-8 text-green-500" />
          </div>
        </B24Card>

        <B24Card>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Найдено</p>
              <p class="text-2xl font-bold">{{ filteredItems.length }}</p>
            </div>
            <B24Icon :icon="SearchIcon" class="w-8 h-8 text-blue-500" />
          </div>
        </B24Card>
      </div>

      <!-- Accordion -->
      <B24Card v-if="loading" class="text-center py-8">
        <B24Icon :icon="LoadingIcon" class="w-8 h-8 animate-spin mx-auto text-primary" />
        <p class="mt-2 text-gray-600">Загрузка...</p>
      </B24Card>

      <B24Accordion
        v-else
        :items="accordionItems"
        multiple
        class="space-y-2"
      >
        <!-- Кастомный контент для каждого элемента -->
        <template v-for="(item, index) in filteredItems" :key="item.id" #[`item-${index}`]>
          <div class="p-4 space-y-4">
            <!-- Основная информация -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">ID</p>
                <p class="font-semibold">#{{ item.id }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Статус</p>
                <B24Badge :color="getStatusColor(item.status)">
                  {{ item.status }}
                </B24Badge>
              </div>
            </div>

            <!-- Описание -->
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Описание</p>
              <p class="text-gray-900 dark:text-white">{{ item.description }}</p>
            </div>

            <!-- Метаданные -->
            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Создан</p>
                <p class="text-sm">{{ formatDate(item.createdAt) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Обновлён</p>
                <p class="text-sm">{{ formatDate(item.updatedAt) }}</p>
              </div>
            </div>

            <!-- Действия -->
            <div class="flex gap-2 pt-4 border-t">
              <B24Button
                :icon="EditIcon"
                color="air-primary"
                size="sm"
                @click="editItem(item)"
              >
                Редактировать
              </B24Button>
              
              <B24Button
                :icon="TrashIcon"
                color="air-secondary-no-accent"
                size="sm"
                @click="deleteItem(item)"
              >
                Удалить
              </B24Button>
              
              <B24Button
                :icon="CopyIcon"
                color="air-tertiary"
                size="sm"
                @click="duplicateItem(item)"
              >
                Дублировать
              </B24Button>
            </div>
          </div>
        </template>
      </B24Accordion>

      <!-- Пусто -->
      <B24Card v-if="!loading && filteredItems.length === 0" class="text-center py-8">
        <B24Icon :icon="InboxIcon" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-semibold mb-2">Нет объектов</h3>
        <p class="text-gray-600 mb-4">Создайте первый объект</p>
        <B24Button :icon="PlusIcon" color="air-primary" @click="addItem">
          Создать
        </B24Button>
      </B24Card>
    </B24Container>
  </B24App>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { 
  PlusIcon, 
  RefreshIcon, 
  SearchIcon, 
  ChartIcon,
  CheckIcon,
  LoadingIcon,
  EditIcon,
  TrashIcon,
  CopyIcon,
  InboxIcon
} from '@bitrix24/b24icons'

interface Item {
  id: number
  name: string
  description: string
  status: string
  createdAt: string
  updatedAt: string
}

// State
const items = ref<Item[]>([])
const searchQuery = ref('')
const loading = ref(false)

// Загрузка данных
const loadItems = async () => {
  loading.value = true
  
  try {
    // Замените на ваш API endpoint
    const response = await fetch('/api/items')
    const data = await response.json()
    
    items.value = data.items || []
  } catch (error) {
    console.error('Error loading items:', error)
    useToast().add({
      title: 'Ошибка',
      description: 'Не удалось загрузить данные',
      color: 'red'
    })
  } finally {
    loading.value = false
  }
}

// Фильтрация
const filteredItems = computed(() => {
  if (!searchQuery.value) return items.value
  
  const query = searchQuery.value.toLowerCase()
  return items.value.filter(item => 
    item.name.toLowerCase().includes(query) ||
    item.description.toLowerCase().includes(query)
  )
})

// Accordion items
const accordionItems = computed(() => {
  return filteredItems.value.map((item, index) => ({
    label: item.name,
    value: `item-${index}`,
    slot: `item-${index}`,
    icon: getStatusIcon(item.status)
  }))
})

// Статистика
const totalItems = computed(() => items.value.length)
const activeItems = computed(() => 
  items.value.filter(item => item.status === 'ACTIVE').length
)

// Утилиты
const formatDate = (dateString: string) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('ru-RU')
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    'ACTIVE': 'green',
    'PENDING': 'yellow',
    'INACTIVE': 'gray',
    'ARCHIVED': 'red'
  }
  return colors[status] || 'gray'
}

const getStatusIcon = (status: string) => {
  const icons: Record<string, any> = {
    'ACTIVE': CheckIcon,
    'PENDING': LoadingIcon,
    'INACTIVE': InboxIcon,
    'ARCHIVED': TrashIcon
  }
  return icons[status] || InboxIcon
}

// Actions
const refreshItems = () => {
  loadItems()
}

const addItem = () => {
  console.log('Add item')
  // Реализуйте создание нового объекта
}

const editItem = (item: Item) => {
  console.log('Edit item:', item)
  // Реализуйте редактирование
}

const deleteItem = async (item: Item) => {
  if (!confirm(`Удалить объект "${item.name}"?`)) return
  
  try {
    await fetch(`/api/items/${item.id}`, {
      method: 'DELETE'
    })
    
    useToast().add({
      title: 'Удалено',
      description: `Объект "${item.name}" удалён`,
      color: 'green'
    })
    
    await loadItems()
  } catch (error) {
    useToast().add({
      title: 'Ошибка',
      description: 'Не удалось удалить объект',
      color: 'red'
    })
  }
}

const duplicateItem = (item: Item) => {
  console.log('Duplicate item:', item)
  // Реализуйте дублирование
}

// Lifecycle
onMounted(() => {
  loadItems()
})
</script>
```

---

## 🎨 Кастомизация

### Множественное раскрытие

```vue
<template>
  <!-- Позволяет открыть несколько элементов одновременно -->
  <B24Accordion :items="items" multiple />
</template>
```

### Кастомные иконки

```vue
<script setup>
import { RocketIcon, StarIcon } from '@bitrix24/b24icons'

const items = ref([
  {
    label: 'С иконкой',
    content: 'Содержимое',
    icon: RocketIcon,
    trailingIcon: StarIcon
  }
])
</script>
```

### Отключенные элементы

```vue
<script setup>
const items = ref([
  {
    label: 'Активный',
    content: 'Можно раскрыть'
  },
  {
    label: 'Отключенный',
    content: 'Нельзя раскрыть',
    disabled: true
  }
])
</script>
```

---

## 💡 Use Cases

### 1. FAQ список

```vue
<template>
  <B24Accordion :items="faqItems" />
</template>

<script setup>
const faqItems = ref([
  {
    label: 'Как начать работу с Bitrix24?',
    content: 'Bitrix24 - это онлайн-сервис для управления компанией...'
  },
  {
    label: 'Какие тарифы доступны?',
    content: 'Bitrix24 предлагает несколько тарифов для разных типов бизнеса...'
  }
])
</script>
```

### 2. Группировка данных

```vue
<template>
  <B24Accordion :items="groupedItems" multiple>
    <template v-for="(group, index) in groups" :key="group.id" #[`group-${index}`]>
      <div class="space-y-2">
        <div v-for="item in group.items" :key="item.id" class="p-2 bg-gray-50 rounded">
          {{ item.name }}
        </div>
      </div>
    </template>
  </B24Accordion>
</template>
```

---

## 🔗 Интеграция с Backend

### Универсальная загрузка данных

```vue
<script setup>
const loadItems = async () => {
  loading.value = true
  
  try {
    // Вариант 1: REST API Bitrix24
    const response = await fetch('https://your-domain.bitrix24.com/rest/method', {
      method: 'POST',
      body: JSON.stringify({ /* params */ })
    })
    
    // Вариант 2: Кастомный API
    const response = await fetch('/api/items')
    
    // Вариант 3: Через SDK endpoint
    const response = await fetch('/api/deals.php?category=1')
    
    const data = await response.json()
    items.value = data.items || data.result || []
  } catch (error) {
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}
</script>
```

---

## 📊 Props

```typescript
interface AccordionItem {
  label?: string              // Заголовок элемента
  content?: string            // Содержимое (или используйте slot)
  value?: string              // Уникальное значение
  icon?: Component            // Иконка слева
  trailingIcon?: Component    // Иконка справа
  disabled?: boolean          // Отключить элемент
  slot?: string               // Имя слота для кастомного контента
  class?: string              // CSS классы
}
```

---

## 🎯 Best Practices

### ✅ Рекомендуется

1. **Используйте slots для сложного контента**
   ```vue
   <template #item-0>
     <CustomComponent />
   </template>
   ```

2. **Добавляйте поиск для больших списков**
   ```vue
   <B24Input v-model="search" placeholder="Поиск..." />
   ```

3. **Показывайте loading состояние**
   ```vue
   <div v-if="loading">Загрузка...</div>
   ```

### ❌ Избегайте

1. **Не создавайте слишком глубокую вложенность** (max 2-3 уровня)
2. **Не размещайте большие объёмы данных** в content prop (используйте slots)
3. **Не забывайте про unique keys** при использовании v-for

---

## 📚 Дополнительные ресурсы

- **Bitrix24 UI Kit документация**: https://bitrix24.github.io/b24ui/llms-full.txt
- **B24Accordion** - см. строку 4020 в полной документации
- **Bitrix24 Icons**: https://bitrix24.github.io/b24icons/
- **REST API**: https://apidocs.bitrix24.ru/

---

**Дата**: Октябрь 2025  
**Версия**: 2.0 (Bitrix24 UI Kit)  
**Компонент**: B24Accordion (НЕ UAccordion!)
