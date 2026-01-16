<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    hotels: Array,
});

const format = (value) => JSON.stringify(value, null, 2);
</script>

<template>
    <Head title="Trip Hotels" />

    <main class="p-6">
        <h1 class="text-2xl font-semibold text-slate-900">Trip Hotels</h1>
        <pre class="mt-4 whitespace-pre-wrap rounded bg-slate-100 p-4 text-sm text-slate-700">{{ format(props) }}</pre>
    </main>
</template>
