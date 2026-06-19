<template>
  <div class="bg-navy-800 rounded-xl p-6">
    <p class="text-slate-400 text-sm mb-1">Depositos</p>
    <p class="text-3xl font-bold text-white">
      R$ {{(balance.toLocaleString('pt-br', {style: 'decimal', minimumIntegerDigits: 2,minimumIntegerDigits: 2}))}}
    </p>
  </div>
</template>

<script setup>
defineProps({ balance: { type: Number, default: 0 } })
</script>