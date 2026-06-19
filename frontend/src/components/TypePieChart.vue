<template>
  <div class="bg-navy-800 rounded-xl p-6">
    <p class="text-slate-400 text-sm mb-4">Tipos de Transação</p>
    <div class="chart-box">
      <Pie v-if="total" :data="chartData" :options="options" />
      <p v-else class="text-slate-500 text-sm">Nenhuma transação no mês</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Pie } from 'vue-chartjs'

const props = defineProps({
  deposits: { type: Number, default: 0 },
  withdrawals: { type: Number, default: 0 }
})

const total = computed(() => props.deposits + props.withdrawals)

const chartData = computed(() => ({
  labels: ['Entradas', 'Saídas'],
  datasets: [{
    data: [props.deposits, props.withdrawals],
    backgroundColor: ['#22c55e', '#ef4444'],
    borderWidth: 0,
  }]
}))

const options = {
  responsive: true,
  maintainAspectRatio: false,
  resizeDelay: 200,
  plugins: {
    legend: {
      position: 'bottom',
      labels: { color: '#94a3b8', usePointStyle: true, padding: 16 }
    },
    tooltip: {
      callbacks: {
        label: ctx => ` ${ctx.label}: R$ ${ctx.parsed.toFixed(2)}`
      }
    }
  }
}
</script>
