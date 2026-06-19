<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-white mb-6">Dashboard</h2>
    <LoadingSpinner v-if="loading" />
    <template v-else>
      <div class="grid grid-cols-3  gap-6 mb-6">
          <BalanceCard :balance="wallet.balance" />
          <BalanceCardDeposit :balance="summary.month_deposits" />
          <BalanceCardWithdrawals :balance="summary.month_withdrawals" />
      </div>
      <div class="mb-6 grid grid-cols-2 gap-2">
      <div class="mb-3">
        <TypePieChart :deposits="summary.month_deposits" :withdrawals="summary.month_withdrawals" />
      </div>
       <div>
        <MonthlyChart :series="summary.monthly_series" />
       </div>
      </div>
      <div>
        <BalanceTrendChart :transactions="summary.recent_transactions" />
      </div>
    </template>
  </AuthLayout>
</template>

<script setup>

import { onMounted, ref } from 'vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import BalanceCard from '../components/BalanceCard.vue'
import BalanceCardDeposit from '../components/BalanceCardDeposit.vue'
import BalanceCardWithdrawals from '../components/BalanceCardWithdrawals.vue'
import TypePieChart from '../components/TypePieChart.vue'
import MonthlyChart from '../components/MonthlyChart.vue'
import BalanceTrendChart from '../components/BalanceTrendChart.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useWalletStore } from '../stores/wallet'
import { useDashboardStore } from '../stores/dashboard'

const wallet = useWalletStore()
const dashboard = useDashboardStore()

const loading = ref(true)
const summary = ref(null)

onMounted(async () => {
  await Promise.all([wallet.fetchBalance(), dashboard.fetchSummary()])
  summary.value = dashboard.summary
  loading.value = false
})
</script>
