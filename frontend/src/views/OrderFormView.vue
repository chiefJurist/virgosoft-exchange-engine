<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../axios.js'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const success = ref('')

const form = ref({
    symbol: 'BTC',
    side: 'buy',
    price: '',
    amount: '',
})

const total = computed(() => {
    const t = parseFloat(form.value.price) * parseFloat(form.value.amount)
    return isNaN(t) ? '0.00' : t.toFixed(2)
})

const commission = computed(() => {
    const t = parseFloat(total.value)
    return isNaN(t) ? '0.00' : (t * 0.015).toFixed(2)
})

const assetCommission = computed(() => {
    const a = parseFloat(form.value.amount)
    return isNaN(a) ? '0.00' : (a * 0.015).toFixed(5)
})

async function placeOrder() {
    loading.value = true
    error.value = ''
    success.value = ''
    try {
        await axios.post('/orders', form.value)
        success.value = 'Order placed successfully!'
        setTimeout(() => success.value = '', 3000)
        form.value.price = ''
        form.value.amount = ''
    } catch (e) {
        error.value = e.response?.data?.message || 'Failed to place order'
        setTimeout(() => error.value = '', 3000)
    } finally {
        loading.value = false
    }
}
</script>
<template>
    <div class="order-con">
        <div class="order-con-wrap">
            <div class="order-title-con">
                <h1 class="order-title-text">Place Order</h1>
                <button @click="router.push('/')" class="order-title-link">← Dashboard</button>
            </div>
            <div class="order-body-con">
                <div v-if="success" class="order-success">{{ success }}</div>
                <div v-if="error" class="order-error">{{ error }}</div>
                <div class="mb-6">
                    <label class="order-label">Symbol</label>
                    <select v-model="form.symbol" class="order-select">
                        <option value="BTC">BTC</option>
                        <option value="ETH">ETH</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="order-label">Side</label>
                    <select v-model="form.side" class="order-select">
                        <option value="buy">Buy</option>
                        <option value="sell">Sell</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="order-label">Price (USD)</label>
                    <input v-model="form.price" type="number" class="order-select" />
                </div>
                <div class="mb-6">
                    <label class="order-label">Amount</label>
                    <input v-model="form.amount" type="number" class="order-select" />
                </div>
                <div v-if="form.side == 'buy'" class="order-volume-preview-con">
                    <span class="order-volume-preview-label">Total: </span>
                    <span class="order-volume-preview-total">${{ total }}</span>
                    <span class="ovp-commision">(+ ${{ commission }} commission)</span>
                </div>
                <div v-if="form.side == 'sell'" class="order-volume-preview-con">
                    <span class="order-volume-preview-label">Total: </span>
                    <span class="order-volume-preview-total">{{ form.amount * 1 }} {{ form.symbol }}</span>
                    <span class="ovp-commission">(+ {{ assetCommission }} {{ form.symbol }} asset commission)</span>
                </div>
                <button @click="placeOrder" :disabled="loading"
                    class="order-submit">
                    {{ loading ? 'Placing...' : 'Place Order' }}
                </button>
            </div>
        </div>
    </div>
</template>