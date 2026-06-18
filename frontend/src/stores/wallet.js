import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'

export const useWalletStore = defineStore('wallet', () => {
  const balance = ref(0)
  const loading = ref(false)

  async function fetchBalance() {
    try {
      const { data } = await api.get('/wallet')
      balance.value = data.wallet.balance
    } catch {
    }
  }

  async function deposit(amount, description) {
    const { data } = await api.post('/wallet/deposit', { amount, description })
    await fetchBalance()
    return data
  }

  async function withdraw(amount, description) {
    const { data } = await api.post('/wallet/withdraw', { amount, description })
    await fetchBalance()
    return data
  }

  return { balance, loading, fetchBalance, deposit, withdraw }
})
