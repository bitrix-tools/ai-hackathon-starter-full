<template>
  <div class="card">
    <h2>Add New User</h2>
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label class="form-label">Name:</label>
        <input v-model="form.name" type="text" class="form-input" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email:</label>
        <input v-model="form.email" type="email" class="form-input" required>
      </div>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Adding...' : 'Add User' }}
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
const form = reactive({
  name: '',
  email: ''
})

const loading = ref(false)
const { addUser, fetchUsers } = useBackend()

const emit = defineEmits(['user-added'])

const submitForm = async () => {
  if (!form.name || !form.email) return

  loading.value = true
  try {
    await addUser(form)
    form.name = ''
    form.email = ''
    emit('user-added')
  } catch (error) {
    console.error('Failed to add user:', error)
  } finally {
    loading.value = false
  }
}
</script>
