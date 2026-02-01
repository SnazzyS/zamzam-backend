<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    trip: Object,
    activityTrip: Object,
    customers: Array,
    availableCustomers: Array,
});

const page = usePage();

// Watch for receipt_id to open receipt
watch(() => page.props.flash?.receipt_id, (receiptId) => {
    if (receiptId) {
        window.open(
            route('trips.activities.receipt', [props.trip.id, props.activityTrip.id, receiptId]),
            '_blank'
        );
    }
});

// Watch for bulk_receipt_ids to open bulk receipt
watch(() => page.props.flash?.bulk_receipt_ids, (ids) => {
    if (ids && ids.length > 0) {
        window.open(
            route('trips.activities.bulk-receipt', [props.trip.id, props.activityTrip.id]) + '?ids=' + ids.join(','),
            '_blank'
        );
        selectedCustomerIds.value = [];
    }
});

const showAssignModal = ref(false);
const showPaymentModal = ref(false);
const showBulkPaymentModal = ref(false);
const payingCustomer = ref(null);
const selectedCustomerIds = ref([]);

const assignForm = useForm({
    customer_id: '',
});

const paymentForm = useForm({
    customer_id: '',
    currency: 'MVR',
    payment_method: 'cash',
});

const bulkPaymentForm = useForm({
    customer_ids: [],
    currency: 'MVR',
    payment_method: 'cash',
});

// Get unpaid customers
const unpaidCustomers = computed(() => {
    return props.customers.filter(c => !c.is_paid);
});

// Check if all unpaid customers are selected
const allUnpaidSelected = computed(() => {
    return unpaidCustomers.value.length > 0 &&
           unpaidCustomers.value.every(c => selectedCustomerIds.value.includes(c.customer_id));
});

// Toggle select all unpaid
const toggleSelectAll = () => {
    if (allUnpaidSelected.value) {
        selectedCustomerIds.value = [];
    } else {
        selectedCustomerIds.value = unpaidCustomers.value.map(c => c.customer_id);
    }
};

// Toggle single customer selection
const toggleCustomerSelection = (customerId) => {
    const index = selectedCustomerIds.value.indexOf(customerId);
    if (index === -1) {
        selectedCustomerIds.value.push(customerId);
    } else {
        selectedCustomerIds.value.splice(index, 1);
    }
};

const openAssignModal = () => {
    assignForm.reset();
    assignForm.clearErrors();
    showAssignModal.value = true;
};

const closeAssignModal = () => {
    showAssignModal.value = false;
    assignForm.reset();
    assignForm.clearErrors();
};

const submitAssign = () => {
    assignForm.post(route('trips.activities.assign-customer', [props.trip.id, props.activityTrip.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeAssignModal();
        },
    });
};

const removeCustomer = (customerId) => {
    if (!confirm('Remove this customer from the activity?')) return;
    router.delete(route('trips.activities.remove-customer', [props.trip.id, props.activityTrip.id]), {
        data: { customer_id: customerId },
        preserveScroll: true,
    });
};

const openPaymentModal = (customer) => {
    payingCustomer.value = customer;
    paymentForm.customer_id = customer.customer_id;
    paymentForm.currency = 'MVR';
    paymentForm.payment_method = 'cash';
    paymentForm.clearErrors();
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    payingCustomer.value = null;
    paymentForm.reset();
    paymentForm.clearErrors();
};

const submitPayment = () => {
    paymentForm.post(route('trips.activities.accept-payment', [props.trip.id, props.activityTrip.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closePaymentModal();
        },
    });
};

const openBulkPaymentModal = () => {
    bulkPaymentForm.customer_ids = [...selectedCustomerIds.value];
    bulkPaymentForm.currency = 'MVR';
    bulkPaymentForm.payment_method = 'cash';
    bulkPaymentForm.clearErrors();
    showBulkPaymentModal.value = true;
};

const closeBulkPaymentModal = () => {
    showBulkPaymentModal.value = false;
    bulkPaymentForm.reset();
    bulkPaymentForm.clearErrors();
};

const submitBulkPayment = () => {
    bulkPaymentForm.post(route('trips.activities.accept-bulk-payment', [props.trip.id, props.activityTrip.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeBulkPaymentModal();
        },
    });
};

const openReceipt = (customerActivityId) => {
    window.open(
        route('trips.activities.receipt', [props.trip.id, props.activityTrip.id, customerActivityId]),
        '_blank'
    );
};

const getPriceForCurrency = (currency) => {
    switch (currency) {
        case 'USD': return props.activityTrip.price_usd;
        case 'MVR': return props.activityTrip.price_mvr;
        case 'SAR': return props.activityTrip.price_sar;
        default: return null;
    }
};

const formatCurrency = (amount, currency) => {
    if (!amount) return '-';
    const symbols = { USD: 'USD ', MVR: 'MVR ', SAR: 'SAR ' };
    return `${symbols[currency] || ''}${parseFloat(amount).toFixed(2)}`;
};

const formatDateTime = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Calculate total for bulk payment
const bulkPaymentTotal = computed(() => {
    const price = getPriceForCurrency(bulkPaymentForm.currency);
    if (!price) return 0;
    return price * bulkPaymentForm.customer_ids.length;
});
</script>

<template>
    <Head :title="activityTrip.name" />

    <main class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <Link
                        :href="route('trips.activities.index', trip.id)"
                        class="text-slate-400 hover:text-slate-600 transition"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ activityTrip.name }}</h1>
                </div>
                <p class="mt-1 text-slate-500">Manage customers and payments</p>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    :href="route('trips.activities.passenger-list', [trip.id, activityTrip.id])"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print List
                </Link>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700"
                    @click="openAssignModal"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Customer
                </button>
            </div>
        </div>

        <!-- Prices Info -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-medium text-slate-700 mb-3">Activity Prices</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg bg-slate-50 p-3 text-center">
                    <div class="text-xs text-slate-400 uppercase mb-1">USD</div>
                    <div class="text-lg font-bold text-slate-800">{{ activityTrip.price_usd ? parseFloat(activityTrip.price_usd).toFixed(2) : '-' }}</div>
                </div>
                <div class="rounded-lg bg-slate-50 p-3 text-center">
                    <div class="text-xs text-slate-400 uppercase mb-1">MVR</div>
                    <div class="text-lg font-bold text-slate-800">{{ activityTrip.price_mvr || '-' }}</div>
                </div>
                <div class="rounded-lg bg-slate-50 p-3 text-center">
                    <div class="text-xs text-slate-400 uppercase mb-1">SAR</div>
                    <div class="text-lg font-bold text-slate-800">{{ activityTrip.price_sar || '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Bulk Payment Action Bar -->
        <div v-if="selectedCustomerIds.length > 0" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white text-sm font-bold">
                    {{ selectedCustomerIds.length }}
                </span>
                <span class="text-sm font-medium text-emerald-800">customers selected</span>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                    @click="selectedCustomerIds = []"
                >
                    Clear Selection
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                    @click="openBulkPaymentModal"
                >
                    Accept Bulk Payment
                </button>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Customers ({{ customers.length }})</h2>
                <div v-if="unpaidCustomers.length > 0" class="flex items-center gap-2">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input
                            type="checkbox"
                            :checked="allUnpaidSelected"
                            @change="toggleSelectAll"
                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                        >
                        Select all unpaid ({{ unpaidCustomers.length }})
                    </label>
                </div>
            </div>

            <div v-if="customers.length" class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                        <tr>
                            <th class="border-b border-slate-200 px-4 py-3 text-center w-12"></th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" dir="rtl">Hajee #</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" dir="rtl">Name</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Status</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Amount</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Paid At</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr
                            v-for="customer in customers"
                            :key="customer.id"
                            class="transition-colors"
                            :class="[
                                selectedCustomerIds.includes(customer.customer_id) ? 'bg-emerald-50' : 'even:bg-slate-50/50 hover:bg-violet-50/50'
                            ]"
                        >
                            <td class="border-b border-slate-100 px-4 py-3 text-center">
                                <input
                                    v-if="!customer.is_paid"
                                    type="checkbox"
                                    :checked="selectedCustomerIds.includes(customer.customer_id)"
                                    @change="toggleCustomerSelection(customer.customer_id)"
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                >
                                <span v-else class="text-emerald-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3 text-right font-mono" dir="rtl">
                                {{ customer.umrah_id || '-' }}
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3 text-right" dir="rtl" lang="dv">
                                <div class="font-medium">{{ customer.name }}</div>
                                <div v-if="customer.name_in_english" class="text-xs text-slate-500" dir="ltr">{{ customer.name_in_english }}</div>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="customer.is_paid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                                >
                                    <svg v-if="customer.is_paid" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ customer.is_paid ? 'Paid' : 'Pending' }}
                                </span>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3">
                                <span v-if="customer.is_paid" class="font-medium text-emerald-600">
                                    {{ formatCurrency(customer.amount_paid, customer.currency) }}
                                </span>
                                <span v-else class="text-slate-400">-</span>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3 text-slate-500 text-xs">
                                {{ customer.paid_at ? formatDateTime(customer.paid_at) : '-' }}
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        v-if="!customer.is_paid"
                                        type="button"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-emerald-700"
                                        @click="openPaymentModal(customer)"
                                    >
                                        Pay
                                    </button>
                                    <button
                                        v-if="customer.is_paid"
                                        type="button"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                                        @click="openReceipt(customer.id)"
                                    >
                                        Receipt
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-red-200 bg-white p-1.5 text-red-500 transition hover:bg-red-50"
                                        @click="removeCustomer(customer.customer_id)"
                                        title="Remove"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="p-8 text-center text-slate-500">
                <p class="text-sm">No customers assigned yet</p>
            </div>
        </div>
    </main>

    <!-- Assign Customer Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showAssignModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeAssignModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showAssignModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Add Customer</h3>
                                    <p class="text-sm text-slate-500">Add customer to {{ activityTrip.name }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeAssignModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitAssign" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select Customer</label>
                                    <select
                                        v-model="assignForm.customer_id"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                        <option value="">Select a customer...</option>
                                        <option v-for="customer in availableCustomers" :key="customer.id" :value="customer.id">
                                            {{ customer.umrah_id }} - {{ customer.name }}
                                        </option>
                                    </select>
                                    <p v-if="assignForm.errors.customer_id" class="text-xs text-red-500 mt-1">{{ assignForm.errors.customer_id }}</p>
                                    <p v-if="availableCustomers.length === 0" class="text-xs text-amber-600 mt-1">
                                        All trip customers are already in this activity.
                                    </p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeAssignModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                                        :disabled="assignForm.processing || availableCustomers.length === 0"
                                    >
                                        {{ assignForm.processing ? 'Adding...' : 'Add Customer' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Accept Payment Modal (Single) -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showPaymentModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closePaymentModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showPaymentModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Accept Payment</h3>
                                    <p class="text-sm text-slate-500">{{ payingCustomer?.name }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closePaymentModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitPayment" class="space-y-4">
                                <!-- Currency Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select Currency</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <button
                                            type="button"
                                            @click="paymentForm.currency = 'USD'"
                                            class="rounded-lg border-2 p-3 text-center transition"
                                            :class="paymentForm.currency === 'USD' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                            :disabled="!activityTrip.price_usd"
                                        >
                                            <div class="text-xs text-slate-500">USD</div>
                                            <div class="font-bold" :class="paymentForm.currency === 'USD' ? 'text-violet-700' : 'text-slate-700'">
                                                {{ activityTrip.price_usd ? parseFloat(activityTrip.price_usd).toFixed(2) : 'N/A' }}
                                            </div>
                                        </button>
                                        <button
                                            type="button"
                                            @click="paymentForm.currency = 'MVR'"
                                            class="rounded-lg border-2 p-3 text-center transition"
                                            :class="paymentForm.currency === 'MVR' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                            :disabled="!activityTrip.price_mvr"
                                        >
                                            <div class="text-xs text-slate-500">MVR</div>
                                            <div class="font-bold" :class="paymentForm.currency === 'MVR' ? 'text-violet-700' : 'text-slate-700'">
                                                {{ activityTrip.price_mvr || 'N/A' }}
                                            </div>
                                        </button>
                                        <button
                                            type="button"
                                            @click="paymentForm.currency = 'SAR'"
                                            class="rounded-lg border-2 p-3 text-center transition"
                                            :class="paymentForm.currency === 'SAR' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                            :disabled="!activityTrip.price_sar"
                                        >
                                            <div class="text-xs text-slate-500">SAR</div>
                                            <div class="font-bold" :class="paymentForm.currency === 'SAR' ? 'text-violet-700' : 'text-slate-700'">
                                                {{ activityTrip.price_sar || 'N/A' }}
                                            </div>
                                        </button>
                                    </div>
                                    <p v-if="paymentForm.errors.currency" class="text-xs text-red-500 mt-1">{{ paymentForm.errors.currency }}</p>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Payment Method</label>
                                    <select
                                        v-model="paymentForm.payment_method"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                <!-- Amount Display -->
                                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-center">
                                    <div class="text-sm text-emerald-600">Amount to Pay</div>
                                    <div class="text-2xl font-bold text-emerald-700">
                                        {{ formatCurrency(getPriceForCurrency(paymentForm.currency), paymentForm.currency) }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closePaymentModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:opacity-50"
                                        :disabled="paymentForm.processing || !getPriceForCurrency(paymentForm.currency)"
                                    >
                                        {{ paymentForm.processing ? 'Processing...' : 'Confirm Payment' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

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
                        <div v-if="showBulkPaymentModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Bulk Payment</h3>
                                    <p class="text-sm text-slate-500">{{ bulkPaymentForm.customer_ids.length }} customers selected</p>
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
                                <!-- Currency Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select Currency</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <button
                                            type="button"
                                            @click="bulkPaymentForm.currency = 'USD'"
                                            class="rounded-lg border-2 p-3 text-center transition"
                                            :class="bulkPaymentForm.currency === 'USD' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                            :disabled="!activityTrip.price_usd"
                                        >
                                            <div class="text-xs text-slate-500">USD</div>
                                            <div class="font-bold" :class="bulkPaymentForm.currency === 'USD' ? 'text-violet-700' : 'text-slate-700'">
                                                {{ activityTrip.price_usd ? parseFloat(activityTrip.price_usd).toFixed(2) : 'N/A' }}
                                            </div>
                                        </button>
                                        <button
                                            type="button"
                                            @click="bulkPaymentForm.currency = 'MVR'"
                                            class="rounded-lg border-2 p-3 text-center transition"
                                            :class="bulkPaymentForm.currency === 'MVR' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                            :disabled="!activityTrip.price_mvr"
                                        >
                                            <div class="text-xs text-slate-500">MVR</div>
                                            <div class="font-bold" :class="bulkPaymentForm.currency === 'MVR' ? 'text-violet-700' : 'text-slate-700'">
                                                {{ activityTrip.price_mvr || 'N/A' }}
                                            </div>
                                        </button>
                                        <button
                                            type="button"
                                            @click="bulkPaymentForm.currency = 'SAR'"
                                            class="rounded-lg border-2 p-3 text-center transition"
                                            :class="bulkPaymentForm.currency === 'SAR' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                            :disabled="!activityTrip.price_sar"
                                        >
                                            <div class="text-xs text-slate-500">SAR</div>
                                            <div class="font-bold" :class="bulkPaymentForm.currency === 'SAR' ? 'text-violet-700' : 'text-slate-700'">
                                                {{ activityTrip.price_sar || 'N/A' }}
                                            </div>
                                        </button>
                                    </div>
                                    <p v-if="bulkPaymentForm.errors.currency" class="text-xs text-red-500 mt-1">{{ bulkPaymentForm.errors.currency }}</p>
                                </div>

                                <!-- Payment Method -->
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

                                <!-- Amount Display -->
                                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-emerald-600">Price per person</span>
                                        <span class="font-medium text-emerald-700">{{ formatCurrency(getPriceForCurrency(bulkPaymentForm.currency), bulkPaymentForm.currency) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-emerald-600">Number of customers</span>
                                        <span class="font-medium text-emerald-700">{{ bulkPaymentForm.customer_ids.length }}</span>
                                    </div>
                                    <div class="border-t border-emerald-200 pt-2 mt-2 flex justify-between items-center">
                                        <span class="text-sm font-semibold text-emerald-700">Total Amount</span>
                                        <span class="text-xl font-bold text-emerald-700">{{ formatCurrency(bulkPaymentTotal, bulkPaymentForm.currency) }}</span>
                                    </div>
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
                                        :disabled="bulkPaymentForm.processing || !getPriceForCurrency(bulkPaymentForm.currency)"
                                    >
                                        {{ bulkPaymentForm.processing ? 'Processing...' : 'Confirm Bulk Payment' }}
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
