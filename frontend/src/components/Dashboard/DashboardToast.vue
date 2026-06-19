<script setup>
defineProps(['toast'])
</script>
<template>
    <div v-if="toast" class="profile-toast">
        {{ toast }}
    </div>
</template>