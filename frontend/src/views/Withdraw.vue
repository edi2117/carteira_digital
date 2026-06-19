<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-white">Sacar</h2>
    <div class="h-12">
      <AlertMessage :message="success" type="success" />
      <AlertMessage :message="error" type="error" />
    </div>
    <BalanceCard :balance="wallet.balance" />
    <form @submit.prevent="handleWithdraw" class="mt-6 bg-navy-800 rounded-xl p-6 border border-navy-700 max-w-md">
      <div class="mb-4">
        <label class="block text-sm text-slate-400 mb-1">Valor (R$)</label>
        <input
          type="text" inputmode="decimal" required
          :value="amountDisplay" @input="onAmountInput"
          class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-green-500 transition-colors"
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

import { ref, computed, onMounted } from 'vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import BalanceCard from '../components/BalanceCard.vue'
import AlertMessage from '../components/AlertMessage.vue'
import { useWalletStore } from '../stores/wallet'
import { usevalueBRL } from '../composables/usevalueBRL.js'


const wallet = useWalletStore()
const amount = ref('')
const description = ref('')
const loading = ref(false)
const error = ref('')
const success = ref('')

const amountCents = ref(0)

const { formatValueBRL } = usevalueBRL()

const amountDisplay = computed(() => {
  return amountCents.value.toLocaleString('pt-BR', {
    minimumFractionDigits: 2
  })
})

function onAmountInput(e) {
  amountCents.value = formatValueBRL(e.target.value)
}

onMounted(() => wallet.fetchBalance())

async function handleWithdraw() {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    await wallet.withdraw(amountCents.value.replace(/\D/g, '') / 100, description.value)
    success.value = 'Saque realizado com sucesso!'
    amountCents.value = ''
    description.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao sacar'
  } finally {
    loading.value = false
  }
}
</script>
