# B24Table: Универсальная таблица с данными

> **⚠️ ВАЖНО ДЛЯ ИИ АГЕНТОВ**: Используйте компонент **B24Table** из Bitrix24 UI Kit, а НЕ UTable из Nuxt UI!

## 📋 Описание

**B24Table** - мощный компонент для отображения табличных данных, построенный на TanStack Table.

### Применение

- 📊 Списки любых CRM объектов
- 📈 Отчёты и аналитика
- 📝 Логи и история операций
- 🛒 Каталоги товаров и услуг

---

## 🎯 Базовый пример

### Минимальный код

```vue
<template>
  <B24Table :columns="columns" :data="data" />
</template>

<script setup>
const columns = [
  { key: 'id', header: 'ID' },
  { key: 'name', header: 'Название' }
]

const data = ref([
  { id: 1, name: 'Объект 1' },
  { id: 2, name: 'Объект 2' }
])
</script>
```

---

## 💼 Полный пример: Таблица с данными

```vue
<template>
  <B24App>
    <B24Container class="py-8">
      <!-- Заголовок -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold">📊 Список объектов</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Табличное представление данных
        </p>
      </div>

      <!-- Панель действий -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex gap-2">
          <B24Button
            :icon="PlusIcon"
            color="air-primary"
            @click="addItem"
          >
            Добавить
          </B24Button>
          
          <B24Button
            :icon="DownloadIcon"
            color="air-secondary-no-accent"
            @click="exportData"
          >
            Экспорт
          </B24Button>
        </div>

        <!-- Поиск -->
        <B24Input
          v-model="searchQuery"
          :icon="SearchIcon"
          placeholder="Поиск..."
          class="w-64"
        />
      </div>

      <!-- Статистика -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <B24Card>
          <div class="flex items-center justify-between p-4">
            <div>
              <p class="text-sm text-gray-600">Всего</p>
              <p class="text-2xl font-bold">{{ total }}</p>
            </div>
            <B24Icon :icon="ChartIcon" class="w-8 h-8 text-primary" />
          </div>
        </B24Card>

        <B24Card>
          <div class="flex items-center justify-between p-4">
            <div>
              <p class="text-sm text-gray-600">На странице</p>
              <p class="text-2xl font-bold">{{ data.length }}</p>
            </div>
            <B24Icon :icon="DocumentIcon" class="w-8 h-8 text-blue-500" />
          </div>
        </B24Card>

        <B24Card>
          <div class="flex items-center justify-between p-4">
            <div>
              <p class="text-sm text-gray-600">Выбрано</p>
              <p class="text-2xl font-bold">{{ selectedRows.length }}</p>
            </div>
            <B24Icon :icon="CheckIcon" class="w-8 h-8 text-green-500" />
          </div>
        </B24Card>
      </div>

      <!-- Таблица -->
      <B24Card>
        <B24Table
          v-model:selection="selectedRows"
          :columns="columns"
          :data="filteredData"
          :loading="loading"
          :enable-row-selection="true"
          class="w-full"
        >
          <!-- ID колонка -->
          <template #id="{ row }">
            <span class="font-mono text-sm text-gray-500">
              #{{ row.original.id }}
            </span>
          </template>

          <!-- Название -->
          <template #name="{ row }">
            <div class="flex items-center gap-2">
              <span class="font-medium">{{ row.original.name }}</span>
            </div>
          </template>

          <!-- Статус -->
          <template #status="{ row }">
            <B24Badge
              :color="getStatusColor(row.original.status)"
            >
              {{ row.original.status }}
            </B24Badge>
          </template>

          <!-- Дата -->
          <template #date="{ row }">
            <span class="text-sm text-gray-600">
              {{ formatDate(row.original.date) }}
            </span>
          </template>

          <!-- Действия -->
          <template #actions="{ row }">
            <div class="flex gap-1">
              <B24Button
                :icon="EyeIcon"
                size="xs"
                color="air-tertiary"
                @click="viewItem(row.original)"
              />
              <B24Button
                :icon="EditIcon"
                size="xs"
                color="air-tertiary"
                @click="editItem(row.original)"
              />
              <B24Button
                :icon="TrashIcon"
                size="xs"
                color="air-tertiary"
                @click="deleteItem(row.original)"
              />
            </div>
          </template>
        </B24Table>

        <!-- Пагинация -->
        <div v-if="total > pageSize" class="flex justify-center mt-6 border-t pt-6">
          <B24Pagination
            v-model="page"
            :total="total"
            :page-size="pageSize"
            show-first
            show-last
          />
        </div>
      </B24Card>

      <!-- Массовые действия -->
      <B24Card v-if="selectedRows.length > 0" class="mt-6">
        <div class="flex items-center justify-between p-4">
          <p class="font-semibold">
            Выбрано: {{ selectedRows.length }} объектов
          </p>
          <div class="flex gap-2">
            <B24Button
              color="air-secondary-no-accent"
              @click="bulkEdit"
            >
              Массовое редактирование
            </B24Button>
            <B24Button
              color="air-secondary-no-accent"
              @click="bulkDelete"
            >
              Удалить выбранные
            </B24Button>
          </div>
        </div>
      </B24Card>
    </B24Container>
  </B24App>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { TableColumn } from '@bitrix24/b24ui-nuxt'
import { 
  PlusIcon, 
  DownloadIcon, 
  SearchIcon,
  ChartIcon,
  DocumentIcon,
  CheckIcon,
  EyeIcon,
  EditIcon,
  TrashIcon
} from '@bitrix24/b24icons'

interface Item {
  id: number
  name: string
  status: string
  date: string
}

// State
const data = ref<Item[]>([])
const selectedRows = ref([])
const loading = ref(false)
const searchQuery = ref('')
const page = ref(1)
const pageSize = ref(20)
const total = ref(0)

// Columns
const columns: TableColumn[] = [
  { key: 'id', header: 'ID', sortable: true },
  { key: 'name', header: 'Название', sortable: true },
  { key: 'status', header: 'Статус' },
  { key: 'date', header: 'Дата', sortable: true },
  { key: 'actions', header: 'Действия' }
]

// Filtered data
const filteredData = computed(() => {
  if (!searchQuery.value) return data.value
  
  const query = searchQuery.value.toLowerCase()
  return data.value.filter(item =>
    item.name.toLowerCase().includes(query)
  )
})

// Methods
const loadData = async () => {
  loading.value = true
  
  try {
    // Замените на ваш API endpoint
    const params = new URLSearchParams({
      page: page.value.toString(),
      pageSize: pageSize.value.toString(),
      search: searchQuery.value
    })
    
    const response = await fetch(`/api/items?${params}`)
    const result = await response.json()
    
    data.value = result.items
    total.value = result.total
  } catch (error) {
    console.error('Error loading data:', error)
    useToast().add({
      title: 'Ошибка',
      description: 'Не удалось загрузить данные',
      color: 'red'
    })
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('ru-RU')
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    'NEW': 'blue',
    'IN_PROGRESS': 'yellow',
    'COMPLETED': 'green',
    'CANCELLED': 'red'
  }
  return colors[status] || 'gray'
}

// Actions
const addItem = () => {
  console.log('Add item')
}

const viewItem = (item: Item) => {
  console.log('View:', item)
}

const editItem = (item: Item) => {
  console.log('Edit:', item)
}

const deleteItem = async (item: Item) => {
  if (!confirm(`Удалить объект #${item.id}?`)) return
  
  try {
    await fetch(`/api/items/${item.id}`, { method: 'DELETE' })
    useToast().add({
      title: 'Удалено',
      color: 'green'
    })
    await loadData()
  } catch (error) {
    useToast().add({
      title: 'Ошибка',
      color: 'red'
    })
  }
}

const bulkEdit = () => {
  console.log('Bulk edit:', selectedRows.value)
}

const bulkDelete = async () => {
  if (!confirm(`Удалить ${selectedRows.value.length} объектов?`)) return
  
  // Реализуйте массовое удаление
}

const exportData = () => {
  // Экспорт в CSV/Excel
  const csv = data.value.map(row => 
    Object.values(row).join(',')
  ).join('\n')
  
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'data.csv'
  link.click()
}

// Watchers
watch([page, searchQuery], () => {
  loadData()
}, { debounce: 500 })

// Lifecycle
onMounted(() => {
  loadData()
})
</script>
```

---

## 🎨 Кастомизация

### Сортировка

```vue
<script setup>
const columns = [
  { key: 'id', header: 'ID', sortable: true },
  { key: 'name', header: 'Название', sortable: true }
]
</script>
```

### Выбор строк

```vue
<template>
  <B24Table
    v-model:selection="selectedRows"
    :columns="columns"
    :data="data"
    :enable-row-selection="true"
  />
</template>
```

### Loading состояние

```vue
<template>
  <B24Table
    :columns="columns"
    :data="data"
    :loading="loading"
  />
</template>
```

---

## 📊 Props

```typescript
interface TableColumn {
  key: string              // Ключ данных
  header: string           // Заголовок колонки
  sortable?: boolean       // Включить сортировку
  cell?: Function          // Кастомная функция рендеринга
}
```

---

## 🔗 Интеграция с Backend

```typescript
const loadData = async () => {
  // Любой backend
  const response = await fetch('/api/items', {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' }
  })
  data.value = await response.json()
}
```

---

## 📚 Ресурсы

- **Bitrix24 UI Kit документация**: https://bitrix24.github.io/b24ui/llms-full.txt
- **B24Table** - см. строку 34488 в полной документации
- **TanStack Table**: https://tanstack.com/table/latest
- **REST API**: https://apidocs.bitrix24.ru/

---

**Дата**: Октябрь 2025  
**Версия**: 2.0 (Bitrix24 UI Kit)  
**Компонент**: B24Table (НЕ UTable!)
