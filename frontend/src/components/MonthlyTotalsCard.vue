<template>
  <div class="bg-navy-800 rounded-xl p-6">
    <p class="text-slate-400 text-sm mb-1">Movimentação do Mês</p>
    <div class="flex justify-between items-end mt-2">
      <div>
        <p class="text-xs text-slate-500">Entradas</p>
        <p class="text-lg font-semibold text-green-500">
          R$ {{ income.toFixed(2) }}
        </p>
      </div>
      <div class="text-right">
        <p class="text-xs text-slate-500">Saídas</p>
        <p class="text-lg font-semibold text-red-500">
          R$ {{ expense.toFixed(2) }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ income: { type: Number, default: 0 }, expense: { type: Number, default: 0 } })
</script>
