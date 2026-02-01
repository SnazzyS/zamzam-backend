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
    receipt: Object,
});

const formatDateTime = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (amount, currency) => {
    if (!amount) return '-';
    const symbols = { USD: 'USD ', MVR: 'MVR ', SAR: 'SAR ' };
    return `${symbols[currency] || ''}${parseFloat(amount).toFixed(2)}`;
};

onMounted(() => {
    // Auto print after a short delay
    setTimeout(() => {
        window.print();
    }, 500);
});
</script>

<template>
    <Head :title="`Receipt - ${receipt.receipt_number}`" />

    <div class="min-h-screen bg-white p-8 print:p-0">
        <div class="max-w-[148mm] mx-auto print:max-w-none">
            <!-- Header -->
            <div class="text-center border-b-2 border-slate-800 pb-4 mb-6">
                <img
                    src="/images/logo.png"
                    alt="Zam Zam H&U Travel"
                    class="w-16 h-16 mx-auto mb-2 object-contain"
                >
                <h1 class="text-xl font-bold text-slate-800">Zam Zam H&U Travel</h1>
                <p class="text-sm text-slate-600">Activity Payment Receipt</p>
            </div>

            <!-- Receipt Number -->
            <div class="text-center mb-6">
                <div class="text-sm text-slate-500">Receipt Number</div>
                <div class="text-2xl font-mono font-bold text-slate-800">{{ receipt.receipt_number }}</div>
            </div>

            <!-- Customer Info -->
            <div class="border border-slate-200 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-slate-500 uppercase">Hajee Number</div>
                        <div class="font-mono font-semibold text-slate-800">{{ receipt.umrah_id || '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase">Trip</div>
                        <div class="font-semibold text-slate-800">{{ trip.name }}</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-xs text-slate-500 uppercase">Customer Name</div>
                    <div class="font-semibold text-slate-800" dir="rtl" lang="dv">{{ receipt.customer_name }}</div>
                    <div v-if="receipt.customer_name_english" class="text-sm text-slate-600">{{ receipt.customer_name_english }}</div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="border border-slate-200 rounded-lg p-4 mb-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3 uppercase">Payment Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Activity</span>
                        <span class="font-medium text-slate-800">{{ receipt.activity_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Payment Method</span>
                        <span class="font-medium text-slate-800 capitalize">{{ receipt.payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Currency</span>
                        <span class="font-medium text-slate-800">{{ receipt.currency }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-3">
                        <span class="text-slate-600 font-semibold">Amount Paid</span>
                        <span class="text-xl font-bold text-emerald-600">{{ formatCurrency(receipt.amount, receipt.currency) }}</span>
                    </div>
                </div>
            </div>

            <!-- Timestamp -->
            <div class="text-center text-sm text-slate-500 mb-6">
                <div>Payment received on</div>
                <div class="font-medium">{{ formatDateTime(receipt.paid_at) }}</div>
            </div>

            <!-- Footer -->
            <div class="text-center border-t border-slate-200 pt-4">
                <p class="text-xs text-slate-400">Thank you for your payment</p>
                <p class="text-xs text-slate-400 mt-1">Zam Zam Hajj & Umrah Travel</p>
            </div>

            <!-- Print Button (hidden when printing) -->
            <div class="mt-6 text-center print:hidden">
                <button
                    type="button"
                    class="rounded-md bg-blue-500 px-6 py-2 text-sm font-medium text-white transition hover:bg-blue-600"
                    @click="window.print()"
                >
                    Print Receipt
                </button>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    @page {
        size: A5 portrait;
        margin: 10mm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
