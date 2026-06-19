<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-white">Depositar</h2>
    <div class="h-12">
      <AlertMessage :message="success" type="success" />
      <AlertMessage :message="error" type="error" />
    </div>
    <BalanceCard :balance="wallet.balance" />
    <form @submit.prevent="handleDeposit" class="mt-6 bg-navy-800 rounded-xl p-6 border border-navy-700 max-w-md">
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
          class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-green-500 transition-colors"
          placeholder="Ex: Depósito em dinheiro"
        />
      </div>
      <button
        type="submit" :disabled="loading"
        class="w-full py-3 rounded-lg bg-green-600 text-white font-medium text-sm hover:bg-green-500 disabled:opacity-50 transition-colors"
      >
        {{ loading ? 'Processando...' : 'Depositar' }}
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


const amountCents = ref(0)
const description = ref('')
const loading = ref(false)
const error = ref('')
const success = ref('')
const wallet = useWalletStore()
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

async function handleDeposit() {
  loading.value = true
  error.value = ''
  
  try {
    await wallet.deposit(amountCents.value.replace(/\D/g, '') / 100, description.value)
    success.value = 'Depósito realizado com sucesso!'
    setTimeout(() => { success.value = false }, 5000);
    amountCents.value = 0
    description.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao depositar'
  } finally {
    loading.value = false
  }
}
</script>
