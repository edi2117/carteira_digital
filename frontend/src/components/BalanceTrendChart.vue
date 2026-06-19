<template>
  <div class="bg-navy-800 rounded-xl p-6">
    <p class="text-slate-400 text-sm mb-4">Evolução do Saldo</p>
    <div class="chart-box">
      <Line v-if="chartData" :data="chartData" :options="options" />
      <p v-else class="text-slate-500 text-sm">Nenhum dado disponível</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'

const props = defineProps({
  transactions: { type: Array, default: () => [] }
})

const chartData = computed(() => {
  const reversed = [...props.transactions].reverse()
  if (!reversed.length) return null
  return {
    labels: reversed.map(t => {
      const d = new Date(t.created_at)
      return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })
    }),
    datasets: [{
      label: 'Saldo após transação',
      data: reversed.map(t => t.balance_after),
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59,130,246,0.1)',
      fill: true,
      tension: 0.3,
    }]
  }
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  resizeDelay: 200,
  plugins: {
    legend: {
      labels: { color: '#94a3b8', usePointStyle: true }
    }
  },
  scales: {
    x: {
      ticks: { color: '#64748b' },
      grid: { color: 'rgba(148,163,184,0.1)' }
    },
    y: {
      ticks: { color: '#64748b', callback: v => 'R$ ' + v.toFixed(0) },
      grid: { color: 'rgba(148,163,184,0.1)' }
    }
  }
}
</script>
