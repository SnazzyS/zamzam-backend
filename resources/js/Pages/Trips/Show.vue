<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    trip: Object,
});

const customers = computed(() => props.trip?.customers || []);
</script>

<template>
    <Head title="Trip" />

    <main class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">{{ trip?.name || 'Trip' }}</h1>
            <p class="text-sm text-slate-500">ދަތުރުގެ ކަސްޓަމަރުން</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-medium">name</th>
                        <th class="px-4 py-3 text-left font-medium" dir="ltr">national_id</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr v-for="customer in customers" :key="customer.id">
                        <td class="px-4 py-3 text-right">{{ customer.name }}</td>
                        <td class="px-4 py-3 text-left" dir="ltr">{{ customer.national_id }}</td>
                    </tr>
                    <tr v-if="customers.length === 0">
                        <td class="px-4 py-6 text-center text-slate-400" colspan="2">ކަސްޓަމަރު ނެތް</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</template>
