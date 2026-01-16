<script setup>
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-100" dir="rtl">
        <Navbar />
        <main class="mx-auto max-w-[1400px] p-6">
            <slot />
        </main>
    </div>
</template>
