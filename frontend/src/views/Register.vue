<template>
  <GuestLayout>
    <div class="m-8 bg-navy-800 rounded-xl p-8 border border-navy-700">
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Criar Conta</h2>
      <AlertMessage :message="error" type="error" />
      <form @submit.prevent="handleRegister" class="space-y-4 mt-4">
        <div>
          <label class="block text-sm text-slate-400 mb-1">Nome</label>
          <input
            v-model="name"
            type="text"
            required
            class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500 transition-colors"
            placeholder="Seu nome"
          />
        </div>
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
            minlength="8"
            class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500 transition-colors"
            placeholder="Mínimo 8 caracteres"
          />
        </div>
        <div>
          <label class="block text-sm text-slate-400 mb-1">Confirmar Senha</label>
          <input
            v-model="passwordConfirmation"
            type="password"
            required
            minlength="8"
            class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-white text-sm focus:outline-none focus:border-blue-500 transition-colors"
            placeholder="Repita a senha"
          />
        </div>
        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 rounded-lg bg-blue-600 text-white font-medium text-sm hover:bg-blue-500 disabled:opacity-50 transition-colors"
        >
          {{ loading ? 'Criando...' : 'Cadastrar' }}
        </button>
      </form>
      <p class="text-sm text-slate-500 text-center mt-6">
        Já tem conta?
        <router-link to="/login" class="text-blue-500 hover:text-blue-400">Entrar</router-link>
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

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const error = ref('')

async function handleRegister() {
  if (password.value !== passwordConfirmation.value) {
    error.value = 'Senhas não conferem'
    return
  }
  loading.value = true
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao cadastrar'
  } finally {
    loading.value = false
  }
}
</script>
