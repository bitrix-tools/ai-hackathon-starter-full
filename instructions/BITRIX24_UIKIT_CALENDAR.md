# Calendar: Календарная сетка с событиями

> **⚠️ ВАЖНО ДЛЯ ИИ АГЕНТОВ**: Используйте компоненты **B24*** из Bitrix24 UI Kit для создания календарной сетки!

## 📋 Описание

**Calendar Grid** - кастомная календарная сетка для отображения событий и задач.

### Применение

- 📅 События календаря
- ✅ Задачи с датами
- 📊 График работ
- 🤝 Расписание встреч

---

## 🎯 Базовый пример

```vue
<template>
  <div class="calendar-grid">
    <div class="grid grid-cols-7 gap-2">
      <div v-for="day in weekDays" :key="day" class="text-center font-semibold">
        {{ day }}
      </div>
    </div>

    <div class="grid grid-cols-7 gap-2 mt-2">
      <div
        v-for="day in calendarDays"
        :key="day.key"
        class="min-h-24 p-2 border rounded-lg"
      >
        <div class="font-semibold">{{ day.date.getDate() }}</div>
        <div v-for="event in day.events" :key="event.id" 
             class="text-xs p-1 bg-blue-100 rounded mt-1">
          {{ event.name }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']
const calendarDays = computed(() => {
  // Генерация дней месяца
  return []
})
</script>
```

---

## 💼 Полный пример

```vue
<template>
  <B24Container class="py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <B24Card>
          <template #header>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold">{{ monthName }} {{ currentYear }}</h3>
              <div class="flex gap-2">
                <B24Button :icon="ChevronLeftIcon" size="sm" color="air-tertiary" 
                           @click="previousMonth" />
                <B24Button size="sm" color="air-secondary-no-accent" @click="goToToday">
                  Сегодня
                </B24Button>
                <B24Button :icon="ChevronRightIcon" size="sm" color="air-tertiary" 
                           @click="nextMonth" />
              </div>
            </div>
          </template>

          <div class="p-4">
            <div class="grid grid-cols-7 gap-2 mb-2">
              <div v-for="day in weekDays" :key="day" 
                   class="text-center text-sm font-semibold text-gray-600 py-2">
                {{ day }}
              </div>
            </div>

            <div class="grid grid-cols-7 gap-2">
              <div
                v-for="day in calendarDays"
                :key="day.key"
                :class="[
                  'min-h-24 p-2 border rounded-lg cursor-pointer',
                  day.isCurrentMonth ? 'bg-white' : 'bg-gray-50',
                  day.isToday ? 'ring-2 ring-primary' : ''
                ]"
                @click="selectDay(day)"
              >
                <div :class="['text-sm font-semibold', day.isToday ? 'text-primary' : '']">
                  {{ day.date.getDate() }}
                </div>
                <div class="space-y-1 mt-1">
                  <div
                    v-for="event in day.events"
                    :key="event.id"
                    class="text-xs px-2 py-1 bg-blue-100 rounded truncate"
                    @click.stop="openEvent(event)"
                  >
                    {{ event.name }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </B24Card>
      </div>

      <div>
        <B24Card>
          <template #header>
            <h3 class="text-lg font-semibold">События ({{ events.length }})</h3>
          </template>

          <div v-if="events.length === 0" class="text-center py-8 text-gray-500">
            Нет событий
          </div>

          <div v-else class="space-y-3 p-4">
            <div
              v-for="event in sortedEvents"
              :key="event.id"
              class="p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
              @click="openEvent(event)"
            >
              <p class="font-medium">{{ event.name }}</p>
              <p class="text-sm text-gray-600">{{ formatDate(event.date) }}</p>
            </div>
          </div>
        </B24Card>
      </div>
    </div>

    <B24Modal v-model="showModal">
      <B24Card v-if="selectedEvent">
        <template #header>
          <h3 class="text-lg font-semibold">{{ selectedEvent.name }}</h3>
        </template>
        <div class="space-y-3 p-4">
          <p>{{ selectedEvent.description }}</p>
          <p class="text-sm text-gray-600">{{ formatDate(selectedEvent.date) }}</p>
        </div>
      </B24Card>
    </B24Modal>
  </B24Container>
</template>

<script setup lang="ts">
import { ChevronLeftIcon, ChevronRightIcon } from '@bitrix24/b24icons'

const currentDate = ref(new Date())
const events = ref([])
const selectedEvent = ref(null)
const showModal = ref(false)
const weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']

const currentYear = computed(() => currentDate.value.getFullYear())
const currentMonth = computed(() => currentDate.value.getMonth())
const monthName = computed(() => 
  currentDate.value.toLocaleDateString('ru-RU', { month: 'long' })
)

const calendarDays = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const lastDay = new Date(year, month + 1, 0)
  const days = []
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(year, month, i)
    const dayEvents = events.value.filter(event => {
      const eventDate = new Date(event.date)
      return eventDate.toDateString() === date.toDateString()
    })
    
    days.push({
      key: `day-${i}`,
      date,
      isCurrentMonth: true,
      isToday: date.getTime() === today.getTime(),
      events: dayEvents
    })
  }
  
  return days
})

const sortedEvents = computed(() => {
  return [...events.value].sort((a, b) => 
    new Date(a.date).getTime() - new Date(b.date).getTime()
  )
})

const loadEvents = async () => {
  const response = await fetch(`/api/events?year=${currentYear.value}&month=${currentMonth.value + 1}`)
  events.value = await response.json()
}

const previousMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1)
}

const goToToday = () => {
  currentDate.value = new Date()
}

const selectDay = (day) => {
  console.log('Selected:', day.date)
}

const openEvent = (event) => {
  selectedEvent.value = event
  showModal.value = true
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('ru-RU')
}

watch([currentYear, currentMonth], () => {
  loadEvents()
})

onMounted(() => {
  loadEvents()
})
</script>
```

---

## 📚 Ресурсы

- **Bitrix24 UI Kit документация**: https://bitrix24.github.io/b24ui/llms-full.txt
- **Bitrix24 UI Kit**: https://bitrix24.github.io/
- **Calendar REST API**: https://apidocs.bitrix24.ru/calendar/

---

**Дата**: Октябрь 2025  
**Версия**: 2.0 (Bitrix24 UI Kit)  
**Компоненты**: B24* (Calendar Grid - кастомный)
