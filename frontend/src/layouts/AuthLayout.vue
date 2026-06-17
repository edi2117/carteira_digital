<template>
  <div class="min-h-screen bg-navy-900 flex">
    <Sidebar />
    <main class="flex-1 p-6 overflow-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../components/Sidebar.vue'
</script>
