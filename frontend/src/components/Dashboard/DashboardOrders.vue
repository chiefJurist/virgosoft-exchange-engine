<script setup>
import { ref, computed } from 'vue'

const props = defineProps(['orders'])
defineEmits(['statusFilter', 'cancel'])

const statusFilter = ref('all')

const filteredOrders = computed(() => {
    if (statusFilter.value === 'all') return props.orders
    return props.orders.filter(o => o.status === statusFilter.value)
})
</script>
<template>
    <div class="profile-orders-con">
        <h2 class="profile-orders-title">My Orders</h2>
        <div class="profile-orders-btncon">
            <button v-for="s in ['all', 'open', 'filled', 'cancelled']" :key="s" 
                @click="statusFilter = s"
                :class="statusFilter === s? 'border-blue-600 text-blue-600' : 'text-gray-700 border-white'"
                class="profile-orders-btn">
                {{ s }}
            </button>
        </div>
        <div>
            <div v-for="order in filteredOrders" :key="order.id" class="mb-10 border border-gray-200 shadow px-4 py-2 rounded-lg">
                <div class="profile-orders-symbol-con">
                    <div class="profile-orders-symbol">{{ order.symbol }}/USD</div>
                    <div class="profile-orders-date">{{ new Date(order.created_at).toLocaleString() }}</div>
                </div>
                <div class="capitalize" :class="order.side === 'buy' ? 'text-green-400' : 'text-red-400'">
                    {{ order.side }} Limit Order
                </div>
                <div>
                    <div class="profile-orders-detail-con">
                        <div class="profile-orders-detail-title">Amount</div>
                        <div class="profile-orders-detail-body">{{ order.amount }} {{ order.symbol }}</div>
                    </div>
                    <div class="profile-orders-detail-con">
                        <div class="profile-orders-detail-title">Side</div>
                        <div class="profile-orders-detail-body">{{ order.side }}</div>
                    </div>
                    <div class="profile-orders-detail-con">
                        <div class="profile-orders-detail-title">Price</div>
                        <div class="profile-orders-detail-body">
                            {{ new Number(order.price).toLocaleString('en-US', { minimumFractionDigits: 2 }) }} USD
                        </div>
                    </div>
                    <div class="profile-orders-detail-con">
                        <div class="profile-orders-detail-title">Status</div>
                        <div class="profile-orders-detail-status">{{ order.status }}</div>
                    </div>
                </div>
                <div v-if="order.status==='open'" class="flex justify-center">
                    <button @click="$emit('cancel', order.id)" class="profile-orders-cancel-btn ">Cancel</button>
                </div>
            </div>
        </div>
        <p v-if="!filteredOrders.length" class="profile-orders-empty">No orders found</p>
    </div>
</template>