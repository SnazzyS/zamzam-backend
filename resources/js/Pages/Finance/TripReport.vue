<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    payments: Array,
    activityPayments: Array,
    expenses: Array,
    summary: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatDateTime = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const paymentMethodLabels = {
    transfer: 'Transfer',
    cash: 'Cash',
    cheque: 'Cheque',
};

const categoryLabels = {
    hotel: 'Hotel',
    transport: 'Transport',
    food: 'Food',
    visa: 'Visa',
    flights: 'Flights',
    other: 'Other',
};
</script>

<template>
    <Head :title="`Financial Report - ${trip.name}`" />

    <main class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ trip.name }}</h1>
                <p class="text-sm text-slate-500">Financial Report</p>
            </div>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('finance.dashboard')"
                    class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    Back to Finance
                </Link>
                <Link
                    :href="route('finance.expenses.create') + '?trip_id=' + trip.id"
                    class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600"
                >
                    Add Expense
                </Link>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Customers</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ summary.customerCount }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Total Revenue</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ formatCurrency(summary.totalRevenue) }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Total Expenses</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ formatCurrency(summary.totalExpenses) }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Net Profit/Loss</p>
                <p class="text-2xl font-bold mt-1" :class="summary.netProfit >= 0 ? 'text-emerald-600' : 'text-red-600'">
                    {{ summary.netProfit >= 0 ? '' : '-' }}{{ formatCurrency(Math.abs(summary.netProfit)) }}
                </p>
            </div>
        </div>

        <!-- Revenue Section -->
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Revenue - Customer Payments</h2>
            </div>
            <div v-if="payments.length > 0" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Date</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Customer</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Method</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Details</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="payment in payments" :key="payment.id" class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">{{ formatDateTime(payment.created_at) }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ payment.invoice?.customer?.name || '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="payment.payment_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'">
                                    {{ payment.payment_method === 'cash' ? 'Cash' : 'Transfer' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ payment.details || '-' }}</td>
                            <td class="px-5 py-3 text-right font-medium text-emerald-600">{{ formatCurrency(payment.amount) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-right font-medium text-slate-700">Total Customer Payments</td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-600">{{ formatCurrency(payments.reduce((sum, p) => sum + Number(p.amount), 0)) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div v-else class="p-6 text-center text-slate-400">No customer payments recorded</div>
        </div>

        <!-- Activity Revenue Section -->
        <div v-if="activityPayments.length > 0" class="bg-white rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Revenue - Optional Activities</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Date</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Activity</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Customer</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Currency</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="ap in activityPayments" :key="ap.id" class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">{{ formatDateTime(ap.paid_at) }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ ap.activity_trip?.activity?.name || '-' }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ ap.customer?.name || '-' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ ap.currency || 'MVR' }}</td>
                            <td class="px-5 py-3 text-right font-medium text-blue-600">{{ formatCurrency(ap.amount_paid) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-right font-medium text-slate-700">Total Activity Revenue</td>
                            <td class="px-5 py-3 text-right font-bold text-blue-600">{{ formatCurrency(activityPayments.reduce((sum, p) => sum + Number(p.amount_paid), 0)) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Expenses Section -->
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Expenses</h2>
                <Link
                    :href="route('finance.expenses.create') + '?trip_id=' + trip.id"
                    class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                >
                    + Add Expense
                </Link>
            </div>
            <div v-if="expenses.length > 0" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Date</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Title</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Category</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Payment</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Amount</th>
                            <th class="px-5 py-3 text-center font-medium text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="expense in expenses" :key="expense.id" class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">{{ formatDate(expense.expense_date) }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ expense.title }}</div>
                                <div v-if="expense.vendor_name" class="text-xs text-slate-400">{{ expense.vendor_name }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                    {{ categoryLabels[expense.category] || expense.category || 'Other' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ paymentMethodLabels[expense.payment_method] }}
                                <span v-if="expense.transfer_reference_number" class="text-xs text-slate-400 block">Ref: {{ expense.transfer_reference_number }}</span>
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-red-600">{{ formatCurrency(expense.amount) }} {{ expense.currency }}</td>
                            <td class="px-5 py-3 text-center">
                                <Link
                                    :href="route('finance.expenses.edit', expense.id)"
                                    class="text-blue-600 hover:text-blue-700 text-xs font-medium"
                                >
                                    Edit
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-right font-medium text-slate-700">Total Expenses</td>
                            <td class="px-5 py-3 text-right font-bold text-red-600">{{ formatCurrency(expenses.reduce((sum, e) => sum + Number(e.amount), 0)) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div v-else class="p-6 text-center text-slate-400">No expenses recorded for this trip</div>
        </div>

        <!-- Summary Footer -->
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <span class="text-slate-600">Total Revenue</span>
                <span class="text-lg font-semibold text-emerald-600">{{ formatCurrency(summary.totalRevenue) }}</span>
            </div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <span class="text-slate-600">Total Expenses</span>
                <span class="text-lg font-semibold text-red-600">-{{ formatCurrency(summary.totalExpenses) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-lg font-semibold text-slate-800">Net Profit/Loss</span>
                <span class="text-2xl font-bold" :class="summary.netProfit >= 0 ? 'text-emerald-600' : 'text-red-600'">
                    {{ summary.netProfit >= 0 ? '' : '-' }}{{ formatCurrency(Math.abs(summary.netProfit)) }}
                </span>
            </div>
        </div>
    </main>
</template>
