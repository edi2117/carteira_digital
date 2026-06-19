<template>
  <div class="bg-navy-800 rounded-xl p-6">
    <p class="text-slate-400 text-sm mb-4">Movimentação Mensal</p>
    <div class="chart-box">
      <Bar v-if="chartData" :data="chartData" :options="options" />
      <p v-else class="text-slate-500 text-sm">Nenhum dado disponível</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'

const props = defineProps({
  series: { type: Array, default: () => [] }
})

const monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

const chartData = computed(() => {
  if (!props.series.length) return null
  return {
    labels: props.series.map(s => {
      const [, m] = s.month.split('-')
      return monthNames[parseInt(m) - 1]
    }),
    datasets: [
      {
        label: 'Entradas',
        data: props.series.map(s => s.deposits),
        backgroundColor: 'rgba(34,197,94,0.7)',
        borderColor: '#22c55e',
        borderWidth: 1,
        borderRadius: 4,
      },
      {
        label: 'Saídas',
        data: props.series.map(s => s.withdrawals),
        backgroundColor: 'rgba(239,68,68,0.7)',
        borderColor: '#ef4444',
        borderWidth: 1,
        borderRadius: 4,
      }
    ]
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
