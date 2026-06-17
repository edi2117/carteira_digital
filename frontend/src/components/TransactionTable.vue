<template>
  <div class="bg-navy-800 rounded-xl overflow-hidden">
    <table class="w-full">
      <thead>
        <tr class="border-b border-navy-700">
          <th class="text-left p-4 text-xs text-slate-400 font-medium uppercase">Data</th>
          <th class="text-left p-4 text-xs text-slate-400 font-medium uppercase">Descrição</th>
          <th class="text-right p-4 text-xs text-slate-400 font-medium uppercase">Valor</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="t in transactions" :key="t.id" class="border-b border-navy-700 last:border-0">
          <td class="p-4 text-sm text-slate-300">{{ formatDate(t.created_at) }}</td>
          <td class="p-4 text-sm text-slate-300">{{ t.description }}</td>
          <td class="p-4 text-sm text-right" :class="t.type === 'deposit' ? 'text-green-500' : 'text-red-500'">
            {{ t.type === 'deposit' ? '+' : '-' }} R$ {{ Number(t.amount).toFixed(2) }}
          </td>
        </tr>
        <tr v-if="!transactions.length">
          <td colspan="3" class="p-8 text-center text-slate-500 text-sm">Nenhuma transação encontrada</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({ transactions: { type: Array, default: () => [] } })

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('pt-BR')
}
</script>
