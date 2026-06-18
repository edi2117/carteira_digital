import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

export const useDashboardStore = defineStore('dashboard', () => {
  const summary = ref(null)
  const loading = ref(false)

  async function fetchSummary() {
    loading.value = true
    try {
      const { data } = await api.get('/dashboard/summary')
      summary.value = data
    } finally {
      loading.value = false
    }
  }

  return { summary, loading, fetchSummary }
})
