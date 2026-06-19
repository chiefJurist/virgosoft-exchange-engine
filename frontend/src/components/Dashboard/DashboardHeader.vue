<script setup>
    defineEmits(['logout', 'placeOrder'])
</script>

<template>
    <!-- Header -->
    <div class="profile-header-con">
        <h1 class="profile-header-title">Dashboard</h1>
        <div class="flex gap-4">
            <button @click="$emit('placeOrder')" class="profile-header-btn-one">
                Place Order
            </button>
            <button @click="$emit('logout')" class="profile-header-btn-two">
                Logout
            </button>
        </div>
    </div>
</template>