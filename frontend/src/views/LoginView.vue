<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

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
    } catch (e) {
        error.value = e.response?.data?.message || 'Login failed'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="login-con">
        <div class="login-subcon">
            <img src="../assets/images/virgosoft-exchange-logo.png" alt="logo" class=" mb-6">
            <h1 class="login-title">Login</h1>
            <div v-if="error" class="login-error">{{ error }}</div>
            <div class="mb-4">
                <label class="login-input-label">Email</label>
                <input v-model="email" type="email" class="login-input" />
            </div>
            <div class="mb-6">
                <label class="login-input-label">Password</label>
                <input v-model="password" type="password" class="login-input" />
            </div>
            <button @click="handleLogin" :disabled="loading" class="login-btn">
                {{ loading ? 'Logging in...' : 'Login' }}
            </button>
        </div>
    </div>
</template>