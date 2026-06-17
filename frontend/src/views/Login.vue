<template>
  <GuestLayout>
    <div class="bg-navy-800 rounded-xl p-8 border border-navy-700">
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Entrar</h2>
      <AlertMessage :message="error" type="error" />
      <form @submit.prevent="handleLogin" class="space-y-4 mt-4">
        <div>
          <label class="block text-sm text-slate-400 mb-1">E-mail</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500 transition-colors"
            placeholder="seu@email.com"
          />
        </div>
        <div>
          <label class="block text-sm text-slate-400 mb-1">Senha</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500 transition-colors"
            placeholder="••••••••"
          />
        </div>
        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 rounded-lg bg-blue-600 text-white font-medium text-sm hover:bg-blue-500 disabled:opacity-50 transition-colors"
        >
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
      <p class="text-sm text-slate-500 text-center mt-6">
        Não tem conta?
        <router-link to="/register" class="text-blue-500 hover:text-blue-400">Cadastre-se</router-link>
      </p>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import GuestLayout from '../layouts/GuestLayout.vue'
import AlertMessage from '../components/AlertMessage.vue'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao fazer login'
  } finally {
    loading.value = false
  }
}
</script>
