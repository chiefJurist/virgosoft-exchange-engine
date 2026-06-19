<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import router from './router'
import axios from './axios.js'

onMounted(async () => {
    const token = localStorage.getItem('token')
    if (token) {
        try {
            await axios.get('/profile')
        } catch {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            router.push({ name: 'login' })
        }
    }
})
</script>

<template>
    <RouterView />
</template>