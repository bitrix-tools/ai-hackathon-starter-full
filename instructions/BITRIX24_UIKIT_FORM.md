# B24Form: Форма редактирования объекта

> **⚠️ ВАЖНО ДЛЯ ИИ АГЕНТОВ**: Используйте **B24Form** и **B24FormField** из Bitrix24 UI Kit, а НЕ UForm/UFormGroup из Nuxt UI!

## 📋 Описание

**B24Form** - компонент для создания и редактирования объектов с валидацией.

### Применение

- 📝 Детальные карточки объектов
- ➕ Формы создания новых записей
- ⚙️ Настройки и конфигурация
- 🔄 Многошаговые формы (wizards)

---

## 🎯 Базовый пример

```vue
<template>
  <B24Form :state="state" @submit="onSubmit">
    <B24FormField label="Название" name="name">
      <B24Input v-model="state.name" />
    </B24FormField>
    
    <B24Button type="submit" color="air-primary">
      Сохранить
    </B24Button>
  </B24Form>
</template>

<script setup>
const state = reactive({
  name: ''
})

const onSubmit = async (data) => {
  console.log('Submitted:', data)
}
</script>
```

---

## 💼 Полный пример: Карточка объекта

```vue
<template>
  <B24App>
    <B24Container class="py-8">
      <div v-if="loading" class="flex items-center justify-center min-h-screen">
        <B24Icon :icon="LoadingIcon" class="w-8 h-8 animate-spin" />
      </div>

      <div v-else class="space-y-6">
        <!-- Header -->
        <B24Card>
          <div class="flex items-start justify-between gap-4 p-4">
            <div class="flex-1">
              <B24Input
                v-model="form.title"
                size="xl"
                placeholder="Название объекта"
                class="text-2xl font-bold"
              />
              <div class="flex items-center gap-4 mt-3 text-sm text-gray-600">
                <B24Icon :icon="CalendarIcon" />
                <span>Создан: {{ formatDate(item.createdAt) }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <B24Button
                :icon="CheckIcon"
                color="air-primary"
                :loading="saving"
                @click="saveItem"
              >
                Сохранить
              </B24Button>

              <B24Button
                :icon="CloseIcon"
                color="air-secondary-no-accent"
                :disabled="saving"
                @click="cancelEdit"
              >
                Отменить
              </B24Button>
            </div>
          </div>
        </B24Card>

        <!-- Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">
            <B24Card>
              <template #header>
                <h3 class="text-lg font-semibold">Основная информация</h3>
              </template>

              <div class="space-y-4 p-4">
                <B24FormField label="Статус" required>
                  <B24Select
                    v-model="form.status"
                    :options="statusOptions"
                    placeholder="Выберите статус"
                  />
                </B24FormField>

                <B24FormField label="Сумма">
                  <div class="flex gap-2">
                    <B24Input
                      v-model="form.amount"
                      type="number"
                      placeholder="0"
                      class="flex-1"
                    />
                    <B24Select
                      v-model="form.currency"
                      :options="currencyOptions"
                      class="w-32"
                    />
                  </div>
                </B24FormField>

                <div class="grid grid-cols-2 gap-4">
                  <B24FormField label="Дата начала">
                    <B24Input
                      v-model="form.startDate"
                      type="date"
                    />
                  </B24FormField>

                  <B24FormField label="Дата завершения">
                    <B24Input
                      v-model="form.endDate"
                      type="date"
                    />
                  </B24FormField>
                </div>
              </div>
            </B24Card>

            <B24Card>
              <template #header>
                <h3 class="text-lg font-semibold">Дополнительно</h3>
              </template>

              <div class="p-4">
                <B24FormField label="Описание">
                  <B24Textarea
                    v-model="form.description"
                    :rows="4"
                    placeholder="Добавьте описание..."
                  />
                </B24FormField>
              </div>
            </B24Card>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <B24Card>
              <template #header>
                <h3 class="text-lg font-semibold">Информация</h3>
              </template>

              <div class="space-y-3 text-sm p-4">
                <div class="flex justify-between">
                  <span class="text-gray-600">ID:</span>
                  <span class="font-semibold">#{{ item.id }}</span>
                </div>
              </div>
            </B24Card>

            <B24Card>
              <template #header>
                <h3 class="text-lg font-semibold">Действия</h3>
              </template>

              <div class="space-y-2 p-4">
                <B24Button
                  block
                  color="air-secondary-no-accent"
                  :icon="CopyIcon"
                  @click="duplicate"
                >
                  Дублировать
                </B24Button>
                
                <B24Button
                  block
                  color="air-secondary-no-accent"
                  :icon="TrashIcon"
                  @click="deleteItem"
                >
                  Удалить
                </B24Button>
              </div>
            </B24Card>
          </div>
        </div>
      </div>
    </B24Container>
  </B24App>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import {
  LoadingIcon,
  CalendarIcon,
  CheckIcon,
  CloseIcon,
  CopyIcon,
  TrashIcon
} from '@bitrix24/b24icons'

const item = ref({ id: 1, createdAt: new Date().toISOString() })
const form = reactive({
  title: '',
  status: '',
  amount: 0,
  currency: 'RUB',
  startDate: '',
  endDate: '',
  description: ''
})

const loading = ref(false)
const saving = ref(false)

const statusOptions = [
  { value: 'NEW', label: 'Новый' },
  { value: 'IN_PROGRESS', label: 'В работе' },
  { value: 'COMPLETED', label: 'Завершён' }
]

const currencyOptions = [
  { value: 'RUB', label: '₽ RUB' },
  { value: 'USD', label: '$ USD' },
  { value: 'EUR', label: '€ EUR' }
]

const saveItem = async () => {
  saving.value = true
  try {
    await fetch(`/api/items/${item.value.id}`, {
      method: 'PUT',
      body: JSON.stringify(form)
    })
    useToast().add({ title: 'Сохранено', color: 'green' })
  } finally {
    saving.value = false
  }
}

const cancelEdit = () => {
  // Reload data
}

const duplicate = () => {
  console.log('Duplicate')
}

const deleteItem = () => {
  console.log('Delete')
}

const formatDate = (date) => new Date(date).toLocaleString('ru-RU')
</script>
```

---

## 🎨 Важные отличия от Nuxt UI

### ⚠️ FormField вместо FormGroup!

```vue
<!-- Nuxt UI (НЕ используйте!) -->
<UFormGroup label="Email">
  <UInput />
</UFormGroup>

<!-- Bitrix24 UI Kit (используйте!) -->
<B24FormField label="Email">
  <B24Input />
</B24FormField>
```

---

## 📚 Ресурсы

- **Bitrix24 UI Kit документация**: https://bitrix24.github.io/b24ui/llms-full.txt
- **B24Form** - см. строку 17044 в полной документации
- **B24FormField** - см. строку 17902 в полной документации
- **REST API**: https://apidocs.bitrix24.ru/

---

**Дата**: Октябрь 2025  
**Версия**: 2.0 (Bitrix24 UI Kit)  
**Компоненты**: B24Form, B24FormField (НЕ UForm/UFormGroup!)
