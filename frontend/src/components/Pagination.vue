<template>
  <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 mt-4">
    <button
      :disabled="currentPage <= 1"
      @click="$emit('page-change', currentPage - 1)"
      class="px-3 py-1.5 rounded text-sm bg-navy-800 text-slate-300 hover:bg-navy-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
    >
      Anterior
    </button>
    <span class="text-sm text-slate-400">
      {{ currentPage }} / {{ totalPages }}
    </span>
    <button
      :disabled="currentPage >= totalPages"
      @click="$emit('page-change', currentPage + 1)"
      class="px-3 py-1.5 rounded text-sm bg-navy-800 text-slate-300 hover:bg-navy-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
    >
      Próximo
    </button>
  </div>
</template>

<script setup>
defineProps({ currentPage: { type: Number, default: 1 }, totalPages: { type: Number, default: 1 } })
defineEmits(['page-change'])
</script>
