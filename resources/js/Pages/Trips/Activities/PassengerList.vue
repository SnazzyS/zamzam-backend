<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    trip: Object,
    activity: Object,
    customers: Array,
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const printPage = () => {
    window.print();
};

onMounted(() => {
    setTimeout(() => {
        printPage();
    }, 500);
});
</script>

<template>
    <Head :title="`${activity.name} - Passenger List`" />

    <div class="min-h-screen bg-white p-8 print:p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <img
                        src="/images/logo.png"
                        alt="Zam Zam H&U Travel"
                        class="w-14 h-14 object-contain"
                    >
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Zam Zam H&U Travel</h1>
                        <p class="text-sm text-slate-600">{{ trip.name }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-lg font-bold text-slate-800">{{ activity.name }}</h2>
                    <p v-if="activity.date" class="text-sm text-slate-600">{{ formatDate(activity.date) }}</p>
                    <p class="text-sm font-medium text-slate-700">{{ customers.length }} Passengers</p>
                </div>
            </div>

            <!-- Passenger Table -->
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="border border-slate-300 px-3 py-2 text-left w-12">#</th>
                        <th class="border border-slate-300 px-3 py-2 text-center w-24">Hajee ID</th>
                        <th class="border border-slate-300 px-3 py-2 text-right" dir="rtl">Name</th>
                        <th class="border border-slate-300 px-3 py-2 text-center w-20">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(customer, index) in customers" :key="customer.id" class="even:bg-slate-50">
                        <td class="border border-slate-300 px-3 py-2 text-center">{{ index + 1 }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-center font-mono font-semibold">
                            {{ customer.umrah_id || '-' }}
                        </td>
                        <td class="border border-slate-300 px-3 py-2 text-right" dir="rtl" lang="dv">
                            <span class="font-medium">{{ customer.name }}</span>
                        </td>
                        <td class="border border-slate-300 px-3 py-2 text-center">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="customer.is_paid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                            >
                                {{ customer.is_paid ? 'Paid' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Summary -->
            <div class="mt-6 flex justify-between text-sm">
                <div class="text-slate-600">
                    Total: <span class="font-semibold">{{ customers.length }}</span> passengers
                </div>
                <div class="text-slate-600">
                    Paid: <span class="font-semibold text-emerald-600">{{ customers.filter(c => c.is_paid).length }}</span> |
                    Pending: <span class="font-semibold text-amber-600">{{ customers.filter(c => !c.is_paid).length }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-4 border-t border-slate-200 text-center text-xs text-slate-400">
                <p>Zam Zam Hajj & Umrah Travel - {{ activity.name }} Passenger List</p>
                <p class="mt-1">Printed on {{ new Date().toLocaleDateString() }}</p>
            </div>

            <!-- Print Button -->
            <div class="mt-6 text-center print:hidden">
                <button
                    type="button"
                    class="rounded-md bg-blue-500 px-6 py-2 text-sm font-medium text-white transition hover:bg-blue-600"
                    @click="printPage"
                >
                    Print List
                </button>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm;
    }
}
</style>
