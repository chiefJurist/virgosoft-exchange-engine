<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import axios from '../axios.js'
import echo from '../echo.js'
import DashboardHeader from '@/components/Dashboard/DashboardHeader.vue'
import DashboardToast from '@/components/Dashboard/DashboardToast.vue'
import DashboardBalances from '@/components/Dashboard/DashboardBalances.vue'
import DashboardOrderbook from '@/components/Dashboard/DashboardOrderbook.vue'
import DashboardOrders from '@/components/Dashboard/DashboardOrders.vue'

const router = useRouter()
const auth = useAuthStore()
const profile = ref(null)
const orders = ref([])
const orderbook = ref({ buys: [], sells: [] })
const selectedSymbol = ref('BTC')
const toast = ref('')

async function fetchProfile() {
    const res = await axios.get('/profile')
    profile.value = res.data.data
}

async function fetchOrders() {
    const res = await axios.get(`/orders?symbol=${selectedSymbol.value}`)
    orderbook.value = res.data
}

async function fetchMyOrders() {
    const res = await axios.get('/orders/my')
    orders.value = res.data.data
}

async function fetchOrderbook() {
    await fetchOrders()
}

async function cancelOrder(id) {
    await axios.post(`/orders/${id}/cancel`)
    orders.value = orders.value.map(o =>
        o.id === id ? { ...o, status: 'cancelled' } : o
    )
    showToast('Order cancelled successfully')
}

async function handleLogout() {
    await auth.logout()
    router.push('/login')
}

function showToast(message) {
    toast.value = message
    setTimeout(() => toast.value = '', 3000)
}

let channel = null

onMounted(async () => {
    await fetchProfile()
    await fetchOrders()
    await fetchMyOrders()

    channel = echo.private(`user.${auth.user.id}`)
        .listen('.OrderMatched', (e) => {
            // Update profile balance and assets
            fetchProfile()

            // Update order statuses
            const buyId = e.trade.buy_order_id
            const sellId = e.trade.sell_order_id
            orders.value = orders.value.map(o => {
                if (o.id === buyId || o.id === sellId) {
                    return { ...o, status: 'filled' }
                }
                return o
            })

            // Refresh orderbook
            fetchOrders()

            showToast(`Trade matched! ${e.trade.amount} ${e.trade.symbol} @ $${e.trade.price}`)
        })
})

onUnmounted(() => {
    if (channel) {
        echo.leave(`user.${auth.user.id}`)
    }
})
</script>

<template>
    <div class="profile-con">
        <DashboardHeader @logout="handleLogout" @place-order="router.push('/order/place')" />
        <DashboardToast :toast="toast" />
        <DashboardBalances :profile="profile" />
        <DashboardOrderbook 
            :selected-symbol="selectedSymbol" 
            @fetch-order-book="fetchOrderbook" 
            :orderbook="orderbook" 
            v-model:selectedSymbol="selectedSymbol" 
        />
        <DashboardOrders @cancel="cancelOrder" :orders="orders"
        />
    </div>
</template>