import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

export const useWalletStore = defineStore('wallet', () => {
  const balance = ref(0)
  const loading = ref(false)

  async function fetchBalance() {
    try {
      const { data } = await api.get('/wallet/balance')
      balance.value = data.balance
    } catch {
    }
  }

  async function deposit(amount, description) {
    const { data } = await api.post('/wallet/deposit', { amount, description })
    balance.value = data.balance
    return data
  }

  async function withdraw(amount, description) {
    const { data } = await api.post('/wallet/withdraw', { amount, description })
    balance.value = data.balance
    return data
  }

  return { balance, loading, fetchBalance, deposit, withdraw }
})
