<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();

const props = defineProps({
    trip: Object,
    customers: Array,
    groups: Array,
});

// Watch for successful bulk payment to open receipt
watch(() => page.props.flash?.batch_id, (batchId) => {
    if (batchId) {
        window.open(
            route('trips.bulk-payments.show', { trip: props.trip.id, batchId: batchId }),
            '_blank'
        );
    }
});

const navigateToCustomer = (customerId) => {
    router.visit(route('trips.customers.show', [props.trip.id, customerId]));
};

const groupMap = computed(() => {
    const map = new Map();
    (props.groups || []).forEach((group, index) => {
        map.set(group.id, `G${index + 1}`);
    });
    return map;
});

const getGroupTag = (groupId) => {
    if (!groupId) return '-';
    return groupMap.value.get(groupId) || '-';
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    if (typeof value === 'string' && value.includes('-')) {
        const [year, month, day] = value.split('-');
        if (day) {
            return `${day}/${month}/${year}`;
        }
    }

    return value;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

// Bulk Payment
const showBulkPaymentModal = ref(false);
const selectedCustomers = ref(new Set());
const paymentAmounts = ref({});
const discountAmounts = ref({});

const bulkPaymentForm = useForm({
    payments: [],
    payment_method: 'cash',
    details: props.trip?.name ?? '',
    transfer_reference_number: '',
});

const customersWithBalance = computed(() => {
    // Only include non-staff customers with outstanding balance
    return props.customers?.filter(c => !c.is_staff && c.balance > 0) || [];
});

const toggleCustomerSelection = (customerId) => {
    if (selectedCustomers.value.has(customerId)) {
        selectedCustomers.value.delete(customerId);
        delete paymentAmounts.value[customerId];
        delete discountAmounts.value[customerId];
    } else {
        selectedCustomers.value.add(customerId);
        // Default to full balance
        const customer = props.customers.find(c => c.id === customerId);
        if (customer) {
            paymentAmounts.value[customerId] = customer.balance;
            discountAmounts.value[customerId] = 0;
        }
    }
    // Force reactivity
    selectedCustomers.value = new Set(selectedCustomers.value);
};

const selectAllCustomers = () => {
    customersWithBalance.value.forEach(customer => {
        selectedCustomers.value.add(customer.id);
        paymentAmounts.value[customer.id] = customer.balance;
        discountAmounts.value[customer.id] = 0;
    });
    selectedCustomers.value = new Set(selectedCustomers.value);
};

const clearSelection = () => {
    selectedCustomers.value.clear();
    paymentAmounts.value = {};
    discountAmounts.value = {};
    selectedCustomers.value = new Set(selectedCustomers.value);
};

const selectedDiscountTotal = computed(() => {
    let total = 0;
    selectedCustomers.value.forEach(customerId => {
        total += parseFloat(discountAmounts.value[customerId] || 0);
    });
    return total;
});

const selectedTotal = computed(() => {
    let total = 0;
    selectedCustomers.value.forEach(customerId => {
        total += parseFloat(paymentAmounts.value[customerId] || 0);
    });
    return total;
});

const openBulkPaymentModal = () => {
    bulkPaymentForm.reset();
    bulkPaymentForm.details = props.trip?.name ?? '';
    bulkPaymentForm.payment_method = 'cash';
    selectedCustomers.value.clear();
    paymentAmounts.value = {};
    discountAmounts.value = {};
    showBulkPaymentModal.value = true;
};

const closeBulkPaymentModal = () => {
    showBulkPaymentModal.value = false;
    bulkPaymentForm.clearErrors();
};

const submitBulkPayment = () => {
    // Build payments array
    const payments = [];
    selectedCustomers.value.forEach(customerId => {
        const amount = parseFloat(paymentAmounts.value[customerId] || 0);
        const discount = parseFloat(discountAmounts.value[customerId] || 0);
        if (amount > 0 || discount > 0) {
            payments.push({
                customer_id: customerId,
                amount: amount,
                discount: discount,
            });
        }
    });

    if (payments.length === 0) {
        return;
    }

    bulkPaymentForm.payments = payments;

    bulkPaymentForm.post(route('trips.bulk-payments.store', { trip: props.trip.id }), {
        onSuccess: () => {
            closeBulkPaymentModal();
        },
    });
};
</script>

<template>
    <Head title="Trip" />

    <main class="space-y-6">
        <div class="flex items-center justify-between">
            <button
                type="button"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                @click="openBulkPaymentModal"
            >
                Accept Bulk Payments
            </button>
            <div class="text-right">
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ trip?.name || 'Trip' }}</h1>
                <p class="mt-1 text-slate-500" dir="rtl" lang="dv">ކަސްޓަމަރު ލިސްޓް</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-slate-100 text-xs font-semibold text-slate-600" dir="rtl" lang="dv">
                        <tr>
                            <th class="border border-slate-200 px-3 py-2 text-center">ބެލެންސް</th>
                            <th class="border border-slate-200 px-3 py-2 text-center" dir="rtl" lang="dv">ގްރޫޕް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">އުފަން ތާރީޚް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">ފޯނު ނަންބަރު</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">އައިޑީ ކާޑު</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">އެޑްރެސް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">ރަށް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">ނަން</th>
                            <th class="border border-slate-200 px-3 py-2 text-center" dir="rtl" lang="dv">ހާޖީ ނަންބަރު</th>
                            <th class="border border-slate-200 px-3 py-2 text-center" dir="ltr">#</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr
                            v-for="(customer, index) in customers"
                            :key="customer.id"
                            class="even:bg-slate-50/70 cursor-pointer hover:bg-violet-50 transition-colors"
                            @click="navigateToCustomer(customer.id)"
                        >
                            <td class="border border-slate-200 px-3 py-2 text-center" dir="ltr">
                                <span
                                    v-if="customer.is_staff"
                                    class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-700"
                                    dir="rtl"
                                    lang="dv"
                                >
                                    ސްޓާފް
                                </span>
                                <span
                                    v-else-if="customer.balance > 0"
                                    class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700"
                                >
                                    {{ formatCurrency(customer.balance) }}
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700"
                                >
                                    Paid
                                </span>
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-center" dir="ltr">
                                <span
                                    v-if="customer.pivot?.group_id"
                                    class="inline-flex items-center rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700"
                                >
                                    {{ getGroupTag(customer.pivot.group_id) }}
                                </span>
                                <span v-else class="text-slate-400">-</span>
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl">
                                {{ formatDate(customer.date_of_birth) }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl">
                                {{ customer.phone_number || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right font-mono text-slate-600" dir="rtl">
                                {{ customer.national_id }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl" lang="dv">
                                {{ customer.address || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl" lang="dv">
                                {{ customer.island || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl" lang="dv">
                                <span class="font-medium text-slate-800">{{ customer.name }}</span>
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right font-mono text-slate-600" dir="rtl">
                                {{ customer.pivot?.umrah_id || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-center" dir="ltr">
                                {{ index + 1 }}
                            </td>
                        </tr>
                        <tr v-if="!customers || customers.length === 0">
                            <td class="border border-slate-200 px-5 py-8 text-center text-slate-400" colspan="10">
                                No customers
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Bulk Payment Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showBulkPaymentModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeBulkPaymentModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showBulkPaymentModal" class="relative w-full max-w-3xl rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900" dir="rtl" lang="dv">ފައިސާ ބަލައިގަތުން</h3>
                                    <p class="text-sm text-slate-500">Select customers and enter payment amounts</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeBulkPaymentModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitBulkPayment" class="space-y-4">
                                <!-- Customer Selection -->
                                <div class="border border-slate-200 rounded-lg overflow-hidden">
                                    <div class="bg-slate-50 px-4 py-2 flex items-center justify-between">
                                        <span class="text-sm font-medium text-slate-700">Select Customers</span>
                                        <div class="flex gap-2">
                                            <button
                                                type="button"
                                                class="text-xs text-violet-600 hover:text-violet-800"
                                                @click="selectAllCustomers"
                                            >
                                                Select All
                                            </button>
                                            <span class="text-slate-300">|</span>
                                            <button
                                                type="button"
                                                class="text-xs text-slate-500 hover:text-slate-700"
                                                @click="clearSelection"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-slate-50 sticky top-0">
                                                <tr>
                                                    <th class="px-3 py-2 text-left w-10"></th>
                                                    <th class="px-3 py-2 text-right" dir="rtl" lang="dv">ނަން</th>
                                                    <th class="px-3 py-2 text-center">ID</th>
                                                    <th class="px-3 py-2 text-right">Balance</th>
                                                    <th class="px-3 py-2 text-right" dir="rtl" lang="dv">ޑިސްކައުންޓް</th>
                                                    <th class="px-3 py-2 text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="customer in customersWithBalance"
                                                    :key="customer.id"
                                                    class="border-t border-slate-100 hover:bg-slate-50"
                                                    :class="{ 'bg-violet-50': selectedCustomers.has(customer.id) }"
                                                >
                                                    <td class="px-3 py-2">
                                                        <input
                                                            type="checkbox"
                                                            :checked="selectedCustomers.has(customer.id)"
                                                            @change="toggleCustomerSelection(customer.id)"
                                                            class="rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                                                        >
                                                    </td>
                                                    <td class="px-3 py-2 text-right" dir="rtl" lang="dv">
                                                        <span class="font-medium">{{ customer.name }}</span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center font-mono text-xs text-slate-500">
                                                        {{ customer.national_id }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right">
                                                        <span class="text-red-600 font-medium">{{ formatCurrency(customer.balance) }}</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input
                                                            v-if="selectedCustomers.has(customer.id)"
                                                            type="number"
                                                            v-model="discountAmounts[customer.id]"
                                                            step="0.01"
                                                            min="0"
                                                            :max="customer.balance"
                                                            class="w-20 rounded border border-slate-200 px-2 py-1 text-sm text-right"
                                                            placeholder="0"
                                                        >
                                                        <span v-else class="text-slate-400">-</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input
                                                            v-if="selectedCustomers.has(customer.id)"
                                                            type="number"
                                                            v-model="paymentAmounts[customer.id]"
                                                            step="0.01"
                                                            min="0"
                                                            :max="customer.balance - (discountAmounts[customer.id] || 0)"
                                                            class="w-24 rounded border border-slate-200 px-2 py-1 text-sm text-right"
                                                        >
                                                        <span v-else class="text-slate-400">-</span>
                                                    </td>
                                                </tr>
                                                <tr v-if="customersWithBalance.length === 0">
                                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                                        No customers with outstanding balance
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Totals -->
                                <div class="bg-slate-50 rounded-lg p-4 flex items-center justify-between gap-6">
                                    <div class="flex items-center gap-6">
                                        <div v-if="selectedDiscountTotal > 0">
                                            <span class="text-sm text-slate-500" dir="rtl" lang="dv">ޖުމްލަ ޑިސްކައުންޓް:</span>
                                            <span class="text-lg font-semibold text-orange-600 ml-2" dir="ltr">{{ formatCurrency(selectedDiscountTotal) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-slate-600">Total Payment:</span>
                                        <span class="text-xl font-bold text-emerald-600 ml-2">{{ formatCurrency(selectedTotal) }}</span>
                                    </div>
                                </div>

                                <!-- Payment Details -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Payment Method</label>
                                        <select
                                            v-model="bulkPaymentForm.payment_method"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                            required
                                        >
                                            <option value="cash">Cash</option>
                                            <option value="transfer">Bank Transfer</option>
                                        </select>
                                    </div>

                                    <div v-if="bulkPaymentForm.payment_method === 'transfer'">
                                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Transfer Reference</label>
                                        <input
                                            v-model="bulkPaymentForm.transfer_reference_number"
                                            type="text"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                            placeholder="Reference number"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Reason for Payment</label>
                                    <input
                                        v-model="bulkPaymentForm.details"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :placeholder="trip?.name"
                                    >
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeBulkPaymentModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:opacity-50"
                                        :disabled="bulkPaymentForm.processing || selectedCustomers.size === 0"
                                    >
                                        {{ bulkPaymentForm.processing ? 'Processing...' : `Accept Payment (${formatCurrency(selectedTotal)})` }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
