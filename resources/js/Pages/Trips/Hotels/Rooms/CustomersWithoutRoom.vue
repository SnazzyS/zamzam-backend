<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    hotel: Object,
    customers: Array,
});

const format = (value) => JSON.stringify(value, null, 2);
</script>

<template>
    <Head title="ރޫމަށް ނުއެއްކޮށް ކަސްޓަމަރުން" />

    <main class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">ރޫމަށް ނުއެއްކޮށް ކަސްޓަމަރުން</h1>
            <p class="text-sm text-slate-500">މި ހޮޓާގެ ރޫމަކާ ނުއެއްކޮށް ކަސްޓަމަރުން.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700">ތަފްޞީލު</h2>
            <pre class="mt-3 whitespace-pre-wrap rounded-lg bg-slate-50 p-4 text-xs text-slate-600">{{ format(props) }}</pre>
        </div>
    </main>
</template>
