import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

export const useTransactionStore = defineStore('transactions', () => {
  const transactions = ref([])
  const meta = ref({})
  const loading = ref(false)

  async function fetchTransactions(params = {}) {
    loading.value = true
    try {
      const { data } = await api.get('/transactions', { params })
      transactions.value = data.transactions
      meta.value = data.meta || {}
    } finally {
      loading.value = false
    }
  }

  return { transactions, meta, loading, fetchTransactions }
})
