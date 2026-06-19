<script setup>
defineProps(['selectedSymbol', 'orderbook'])
defineEmits(['update:selectedSymbol', 'fetchOrderBook'])
</script>
<template>
    <div class="profile-orderbook-con">
        <div class="profile-orderbook-head">
            <h2 class="profile-orderbook-title">Orderbook</h2>
            <select :value="selectedSymbol" @change="$emit('update:selectedSymbol', $event.target.value); $emit('fetchOrderBook')" class="profile-orderbook-side">
                <option value="BTC">BTC</option>
                <option value="ETH">ETH</option>
            </select>
        </div>

        <div class="profile-orderbook-body">
            <!--Buy Orders-->
            <div>
                <h3 class="profile-orderbook-side-title">Buy  Orders</h3>
                <div class="profile-orderbook-side-head">
                    <span>Price (USD)</span>
                    <span>Amount ({{ selectedSymbol }})</span>
                </div>
                <div v-for="order in orderbook.buys" :key="order.id" class="profile-orderbook-side-body-two">
                    <span class="text-green-400">{{ order.price }}</span>
                    <span>{{ order.amount }}</span>
                </div>
                <p v-if="!orderbook.buys?.length" class="profile-orderbook-empty">No buy orders</p>
            </div>
            <!--Sell Orders-->
            <div class="mt-10 sm:mt-0">
                <h3 class="profile-orderbook-side-title">Sell Orders</h3>
                <div class="profile-orderbook-side-head">
                    <span>Price (USD)</span>
                    <span>Amount ({{ selectedSymbol }})</span>
                </div>
                <div v-for="order in orderbook.sells" :key="order.id" class="profile-orderbook-side-body">
                    <span class="text-red-700">{{ order.price }}</span>
                    <span>{{ order.amount }}</span>
                </div>
                <p v-if="!orderbook.sells?.length" class="profile-orderbook-empty">No sell orders</p>
            </div>
        </div>
    </div>
</template>