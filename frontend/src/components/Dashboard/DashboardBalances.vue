<script setup>
defineProps(['profile'])
</script>

<template>
    <div class="profile-balances-con">
        <div class="profile-balance-con">
            <p class="profile-balance-type">USD Balance</p>
            <p class="profile-balance-amt">${{ profile?.balance }}</p>
        </div>
        <div v-for="asset in profile?.assets" :key="asset.symbol" class="profile-balance-con">
            <p class="profile-balance-type">{{ asset.symbol }}</p>
            <p class="profile-balance-amt">{{ asset.amount }}</p>
            <p class="profile-balance-locked">Locked: {{ asset.locked_amount }}</p>
        </div>
    </div>
</template>