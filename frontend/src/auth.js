import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '../axios.js'

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token'))
    const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))

    async function login(email, password) {
        const response = await axios.post('/login', { email, password })
        token.value = response.data.token
        user.value = response.data.user
        localStorage.setItem('token', token.value)
        localStorage.setItem('user', JSON.stringify(user.value))
    }

    async function logout() {
        await axios.post('/logout')
        token.value = null
        user.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
    }

    return { token, user, login, logout }
})