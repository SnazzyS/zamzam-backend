<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    batchId: String,
    paymentDetails: Array,
    totalAmount: Number,
    paymentMethod: String,
    paymentDate: String,
    details: String,
    transferReference: String,
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

const paymentMethodLabel = (method) => {
    return method === 'transfer' ? 'ބޭންކް ޓްރާންސްފާ' : 'ކޭޝް';
};
</script>

<template>
    <Head :title="`Bulk Receipt - ${batchId?.substring(0, 8)}`" />

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
                            <span class="font-semibold mr-1" dir="ltr">{{ batchId?.substring(0, 8) }}</span>
                        </p>
                        <p>
                            <span class="text-slate-500">ތާރީޚް:</span>
                            <span class="mr-1" dir="ltr">{{ formatDate(paymentDate) }}</span>
                        </p>
                        <p>
                            <span class="text-slate-500">ޖުމްލަ ފައިސާ:</span>
                            <span class="font-bold text-emerald-700 mr-1" dir="ltr">{{ formatCurrency(totalAmount) }}</span>
                        </p>
                    </div>

                    <!-- Center - Title -->
                    <div class="text-center flex-1 px-4">
                        <h1 class="text-lg font-bold text-emerald-700">ފައިސާ ބަލައިގަތް ރަސީދު</h1>
                        <p class="text-sm text-slate-500 mt-1">{{ details }}</p>
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

                <!-- Payment Method Info -->
                <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center gap-6 text-sm">
                    <div>
                        <span class="text-slate-500">ޕޭމަންޓް މެތަޑް:</span>
                        <span class="font-medium mr-1">{{ paymentMethodLabel(paymentMethod) }}</span>
                    </div>
                    <div v-if="transferReference">
                        <span class="text-slate-500">ޓްރާންސްފާ ރެފަރެންސް:</span>
                        <span class="font-medium mr-1" dir="ltr">{{ transferReference }}</span>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="p-5">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-300">
                                <th class="py-2 text-right font-semibold text-slate-600">#</th>
                                <th class="py-2 text-right font-semibold text-slate-600">ނަން</th>
                                <th class="py-2 text-right font-semibold text-slate-600">ހާޖީ ނަންބަރު</th>
                                <th class="py-2 text-center font-semibold text-slate-600" dir="ltr">އަދަދު</th>
                                <th class="py-2 text-center font-semibold text-slate-600" dir="ltr">ބާކީ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in paymentDetails"
                                :key="item.payment.id"
                                class="border-b border-slate-200"
                            >
                                <td class="py-2 text-right text-slate-500" dir="ltr">{{ index + 1 }}</td>
                                <td class="py-2 text-right font-medium">{{ item.customer.name }}</td>
                                <td class="py-2 text-right text-slate-600">{{ item.umrah_id || '-' }}</td>
                                <td class="py-2 text-center font-semibold text-red-600" dir="ltr">{{ formatCurrency(item.payment.amount) }}</td>
                                <td class="py-2 text-center" dir="ltr">{{ formatCurrency(item.balance_after) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-400">
                                <td colspan="3" class="py-3 text-right font-bold text-slate-700">ޖުމްލަ:</td>
                                <td class="py-3 text-center font-bold text-lg text-emerald-700" dir="ltr">{{ formatCurrency(totalAmount) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Signature Section -->
                <div class="p-5 pt-4">
                    <p class="text-sm text-slate-500 mb-8">ސޮއި:</p>
                    <div class="border-t border-slate-400 pt-2 w-48">
                        <p class="text-sm">ނަން: މުޙައްމަދު ސުޙައިލް</p>
                        <p class="text-sm text-slate-500 mt-1">
                            <span>ތާރީޚް:</span>
                            <span dir="ltr" class="mr-1">{{ formatDate(paymentDate) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="text-right mt-4 print:hidden">
                <Link
                    :href="route('trips.show', trip.id)"
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
