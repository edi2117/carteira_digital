<template>
  <aside class="w-64 bg-navy-800 h-screen p-6 flex flex-col">
    <div class="flex items-center gap-3 mb-8 px-1">
      <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
        <Wallet :size="18" class="text-white" />
      </div>
      <span class="text-lg font-bold text-white">Carteira Digital</span>
    </div>
    <nav class="flex-1 space-y-1">
      <router-link
        v-for="item in menuItems"
        :key="item.to"
        :to="item.to"
        class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
        :class="$route.path === item.to ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-navy-700/50'"
      >
        <component :is="item.icon" :size="20" />
        {{ item.label }}
      </router-link>
    </nav>
    <button
      @click="handleLogout"
      class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200 mt-auto"
    >
      <LogOut :size="20" />
      Sair
    </button>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { LayoutDashboard, ArrowDownToLine, ArrowUpFromLine, History, Wallet, LogOut } from '@lucide/vue'

const router = useRouter()
const auth = useAuthStore()

const menuItems = [
  { to: '/', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/deposit', label: 'Depositar', icon: ArrowDownToLine },
  { to: '/withdraw', label: 'Sacar', icon: ArrowUpFromLine },
  { to: '/transactions', label: 'Histórico', icon: History },
]

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>
