<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-white mb-6">Dashboard</h2>
    <LoadingSpinner v-if="loading" />
    <template v-else>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-2">
          <BalanceCard :balance="wallet.balance" />
        </div>
        <MonthlyTotalsCard :income="summary?.monthly_income || 0" :expense="summary?.monthly_expense || 0" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-white mb-4">Transações Recentes</h3>
        <TransactionTable :transactions="transactions" />
      </div>
    </template>
  </AuthLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import BalanceCard from '../components/BalanceCard.vue'
import MonthlyTotalsCard from '../components/MonthlyTotalsCard.vue'
import TransactionTable from '../components/TransactionTable.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useWalletStore } from '../stores/wallet'
import { useDashboardStore } from '../stores/dashboard'
import { useTransactionStore } from '../stores/transactions'

const wallet = useWalletStore()
const dashboard = useDashboardStore()
const tStore = useTransactionStore()

const loading = ref(true)
const summary = ref(null)
const transactions = ref([])

onMounted(async () => {
  await Promise.all([wallet.fetchBalance(), dashboard.fetchSummary(), tStore.fetchTransactions({ per_page: 5 })])
  summary.value = dashboard.summary
  transactions.value = tStore.transactions
  loading.value = false
})
</script>
