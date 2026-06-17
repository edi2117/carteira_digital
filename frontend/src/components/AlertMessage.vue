<template>
  <div
    v-if="visible"
    class="px-4 py-3 rounded-lg text-sm"
    :class="type === 'success' ? 'bg-green-500/10 text-green-500 border border-green-500/30' : 'bg-red-500/10 text-red-500 border border-red-500/30'"
  >
    {{ message }}
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ message: { type: String, default: '' }, type: { type: String, default: 'success' } })
const visible = ref(false)

watch(() => props.message, (val) => {
  visible.value = !!val
})
</script>
