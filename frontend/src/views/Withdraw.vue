<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-white mb-6">Sacar</h2>
    <AlertMessage :message="success" type="success" />
    <AlertMessage :message="error" type="error" />
    <BalanceCard :balance="wallet.balance" />
    <form @submit.prevent="handleWithdraw" class="mt-6 bg-navy-800 rounded-xl p-6 border border-navy-700 max-w-md">
      <div class="mb-4">
        <label class="block text-sm text-slate-400 mb-1">Valor (R$)</label>
        <input
          v-model="amount" type="number" step="0.01" min="1" required
          class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-red-500 transition-colors"
          placeholder="0,00"
        />
      </div>
      <div class="mb-4">
        <label class="block text-sm text-slate-400 mb-1">Descrição</label>
        <input
          v-model="description"
          class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-red-500 transition-colors"
          placeholder="Ex: Saque para conta bancária"
        />
      </div>
      <button
        type="submit" :disabled="loading"
        class="w-full py-3 rounded-lg bg-red-600 text-white font-medium text-sm hover:bg-red-500 disabled:opacity-50 transition-colors"
      >
        {{ loading ? 'Processando...' : 'Sacar' }}
      </button>
    </form>
  </AuthLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import BalanceCard from '../components/BalanceCard.vue'
import AlertMessage from '../components/AlertMessage.vue'
import { useWalletStore } from '../stores/wallet'

const wallet = useWalletStore()
const amount = ref('')
const description = ref('')
const loading = ref(false)
const error = ref('')
const success = ref('')

onMounted(() => wallet.fetchBalance())

async function handleWithdraw() {
  loading.value = true
  error.value = ''
  success.value = ''
  try {
    await wallet.withdraw(amount.value, description.value)
    success.value = 'Saque realizado com sucesso!'
    amount.value = ''
    description.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao sacar'
  } finally {
    loading.value = false
  }
}
</script>
