<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-white mb-6">Histórico de Transações</h2>
    <div class="mb-6">
      <BalanceCard :balance="wallet.balance" />
    </div>
    <div class="flex flex-wrap gap-4 mb-6">
      <select
        v-model="filters.type"
        @change="fetchData(1)"
        class="px-4 py-2 rounded-lg bg-navy-800 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500"
      >
        <option value="">Todos</option>
        <option value="deposit">Depósitos</option>
        <option value="withdraw">Saques</option>
      </select>
      <input
        v-model="filters.start_date"
        type="date"
        @change="fetchData(1)"
        class="px-4 py-2 rounded-lg bg-navy-800 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500"
      />
      <input
        v-model="filters.end_date"
        type="date"
        @change="fetchData(1)"
        class="px-4 py-2 rounded-lg bg-navy-800 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500"
      />
      <input
        v-model="filters.search"
        @input="debouncedSearch"
        class="px-4 py-2 rounded-lg bg-navy-800 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500 min-w-[200px]"
        placeholder="Buscar descrição..."
      />
    </div>
    <LoadingSpinner v-if="loading" />
    <template v-else>
      <TransactionTable :transactions="store.transactions" />
      <Pagination
        :current-page="store.meta.current_page || 1"
        :total-pages="store.meta.last_page || 1"
        @page-change="fetchData"
      />
    </template>
  </AuthLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import BalanceCard from '../components/BalanceCard.vue'
import TransactionTable from '../components/TransactionTable.vue'
import Pagination from '../components/Pagination.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useWalletStore } from '../stores/wallet'
import { useTransactionStore } from '../stores/transactions'

const wallet = useWalletStore()
const store = useTransactionStore()
const loading = ref(false)

const filters = reactive({
  type: '',
  start_date: '',
  end_date: '',
  search: '',
})

let debounceTimer
function debouncedSearch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchData(1), 400)
}

async function fetchData(page = 1) {
  loading.value = true
  await store.fetchTransactions({ page, ...filters })
  loading.value = false
}

onMounted(() => {
  wallet.fetchBalance()
  fetchData()
})
</script>
