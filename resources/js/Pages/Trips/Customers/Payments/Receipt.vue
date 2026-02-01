<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    customer: Object,
    invoice: Object,
    payment: Object,
    tripCustomer: Object,
    balanceAfterPayment: Number,
});

const formatDate = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};
</script>

<template>
    <Head :title="`Receipt - ${payment?.batch_id || payment?.id}`" />

    <div class="min-h-screen bg-white flex items-start justify-center p-6 print:p-0 print:items-start">
        <!-- A5 Receipt Container: 148mm x 210mm -->
        <div class="w-[148mm] print:w-[148mm]">
            <div class="border border-slate-400 bg-white" dir="rtl" lang="dv">
                <!-- Header Row -->
                <div class="flex items-start justify-between p-5 border-b border-slate-300">
                    <!-- Receipt Info (Right side in RTL) -->
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="text-slate-500">ރަސީދު ނަންބަރު:</span>
                            <span class="font-semibold mr-1" dir="ltr">{{ payment?.batch_id || payment?.id }}</span>
                        </p>
                        <p>
                            <span class="text-slate-500">ތާރީޚް:</span>
                            <span class="mr-1" dir="ltr">{{ formatDate(payment?.created_at) }}</span>
                        </p>
                        <p>
                            <span class="text-slate-500">ހައްޖު/އުމްރާ ނަންބަރު:</span>
                            <span class="font-semibold mr-1">{{ tripCustomer?.umrah_id || '-' }}</span>
                        </p>
                    </div>

                    <!-- Center - Title -->
                    <div class="text-center flex-1 px-4">
                        <h1 class="text-lg font-bold">ފައިސާ ބަލައިގަތް ރަސީދު</h1>
                    </div>

                    <!-- Logo (Left side in RTL) -->
                    <div class="flex items-center gap-2" dir="ltr">
                        <img src="/images/logo.png" alt="Zam Zam" class="h-12 w-auto" onerror="this.style.display='none'">
                        <div class="text-left">
                            <p class="text-sm font-bold text-slate-800 leading-tight">Zam Zam</p>
                            <p class="text-xs text-slate-600 leading-tight">Hajj & Umra Travel</p>
                        </div>
                    </div>
                </div>

                <!-- Details Section - Two Columns -->
                <div class="flex p-5 gap-8">
                    <!-- Right Column - Customer Info -->
                    <div class="flex-1 space-y-3">
                        <div>
                            <span class="text-sm text-slate-500">ނަން:</span>
                            <p class="text-base font-medium">{{ customer?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-slate-500">އެޑްރެސް:</span>
                            <p class="text-sm">{{ customer?.address }}, {{ customer?.island }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-slate-500">އައިޑީ ކާޑު ނަންބަރު:</span>
                            <p class="text-sm" dir="ltr">{{ customer?.national_id }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-slate-500">ފޯން ނަންބަރު:</span>
                            <p class="text-sm" dir="ltr">{{ customer?.phone_number }}</p>
                        </div>
                    </div>

                    <!-- Left Column - Payment Info -->
                    <div class="flex-1 space-y-3">
                        <div>
                            <span class="text-sm text-slate-500">އަދަދު:</span>
                            <p class="text-2xl font-bold" dir="ltr">{{ formatCurrency(payment?.amount) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-slate-500">ބާކީ:</span>
                            <p class="text-base" dir="ltr">{{ formatCurrency(balanceAfterPayment) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-slate-500">ތަފްސީލް:</span>
                            <p class="text-sm">{{ payment?.details || trip?.name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="p-5 pt-8">
                    <p class="text-sm text-slate-500 mb-8">ސޮއި:</p>
                    <div class="border-t border-slate-400 pt-2 w-48">
                        <p class="text-sm">ނަން: މުޙައްމަދު ސުޙައިލް</p>
                        <p class="text-sm text-slate-500 mt-1">
                            <span>ތާރީޚް:</span>
                            <span dir="ltr" class="mr-1">{{ formatDate(payment?.created_at) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="text-right mt-4 print:hidden">
                <Link
                    :href="route('trips.customers.show', [trip.id, customer.id])"
                    class="text-blue-600 hover:text-blue-800 text-sm"
                >
                    Back
                </Link>
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

    html, body {
        width: 148mm;
        height: 210mm;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
