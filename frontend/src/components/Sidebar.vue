<template>
  <aside class="w-64 bg-navy-800 h-screen p-6 flex flex-col">
    <h1 class="text-xl font-bold text-white mb-8">Carteira Digital</h1>
    <nav class="flex-1 space-y-2">
      <router-link
        v-for="item in menuItems"
        :key="item.to"
        :to="item.to"
        class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors"
        :class="$route.path === item.to ? 'bg-navy-700 text-white' : 'text-slate-400 hover:text-white hover:bg-navy-700'"
      >
        <span class="text-lg">{{ item.icon }}</span>
        {{ item.label }}
      </router-link>
    </nav>
    <button
      @click="handleLogout"
      class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-red-500 hover:bg-red-500/10 transition-colors mt-auto"
    >
      <span class="text-lg">🚪</span>
      Sair
    </button>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const menuItems = [
  { to: '/', label: 'Dashboard', icon: '📊' },
  { to: '/deposit', label: 'Depositar', icon: '💰' },
  { to: '/withdraw', label: 'Sacar', icon: '💳' },
  { to: '/transactions', label: 'Histórico', icon: '📋' },
]

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>
