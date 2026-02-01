<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    expenses: Object,
    trips: Array,
    years: Array,
    categories: Array,
    filters: Object,
});

const filters = ref({
    trip_id: props.filters?.trip_id || '',
    year: props.filters?.year || '',
    category: props.filters?.category || '',
});

const applyFilters = () => {
    router.get(route('finance.expenses.index'), {
        trip_id: filters.value.trip_id || undefined,
        year: filters.value.year || undefined,
        category: filters.value.category || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = { trip_id: '', year: '', category: '' };
    router.get(route('finance.expenses.index'), {}, { preserveState: true, replace: true });
};

const deleteExpense = (expense) => {
    if (confirm('Are you sure you want to delete this expense?')) {
        router.delete(route('finance.expenses.destroy', expense.id));
    }
};

const formatCurrency = (value, currency = 'MVR') => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0) + ' ' + currency;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
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
    <Head title="Expenses" />

    <main class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Expenses</h1>
                <p class="text-sm text-slate-500">Manage all expenses</p>
            </div>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('finance.dashboard')"
                    class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    Back to Dashboard
                </Link>
                <Link
                    :href="route('finance.expenses.create')"
                    class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600"
                >
                    Add Expense
                </Link>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Trip</label>
                    <select
                        v-model="filters.trip_id"
                        @change="applyFilters"
                        class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">All Trips</option>
                        <option value="general">General Expenses</option>
                        <option v-for="trip in trips" :key="trip.id" :value="trip.id">{{ trip.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Year</label>
                    <select
                        v-model="filters.year"
                        @change="applyFilters"
                        class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">All Years</option>
                        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                    <select
                        v-model="filters.category"
                        @change="applyFilters"
                        class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ categoryLabels[cat] || cat }}</option>
                    </select>
                </div>
                <button
                    type="button"
                    @click="clearFilters"
                    class="text-sm text-slate-500 hover:text-slate-700"
                >
                    Clear filters
                </button>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="bg-white rounded-lg overflow-hidden">
            <div v-if="expenses.data.length > 0" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Date</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Title</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Trip</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Category</th>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Payment</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Amount</th>
                            <th class="px-5 py-3 text-center font-medium text-slate-600">Document</th>
                            <th class="px-5 py-3 text-center font-medium text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">{{ formatDate(expense.expense_date) }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ expense.title }}</div>
                                <div v-if="expense.vendor_name" class="text-xs text-slate-400">{{ expense.vendor_name }}</div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">
                                <span v-if="expense.trip" class="text-slate-700">{{ expense.trip.name }}</span>
                                <span v-else class="text-slate-400">General ({{ expense.fiscal_year }})</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                    {{ categoryLabels[expense.category] || expense.category || 'Other' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs text-slate-600">{{ paymentMethodLabels[expense.payment_method] }}</span>
                                <div v-if="expense.transfer_reference_number" class="text-xs text-slate-400">Ref: {{ expense.transfer_reference_number }}</div>
                                <div v-if="expense.cheque_number" class="text-xs text-slate-400">Cheque: {{ expense.cheque_number }}</div>
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-slate-800">
                                {{ formatCurrency(expense.amount, expense.currency) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a
                                    v-if="expense.document_path"
                                    :href="'/storage/' + expense.document_path"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-700 text-xs"
                                >
                                    View
                                </a>
                                <span v-else class="text-slate-300">-</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('finance.expenses.edit', expense.id)"
                                        class="text-blue-600 hover:text-blue-700 text-xs font-medium"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        @click="deleteExpense(expense)"
                                        class="text-red-600 hover:text-red-700 text-xs font-medium"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-8 text-center text-slate-400">
                No expenses found
            </div>

            <!-- Pagination -->
            <div v-if="expenses.last_page > 1" class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Showing {{ expenses.from }} to {{ expenses.to }} of {{ expenses.total }} expenses
                </div>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="link in expenses.links"
                        :key="link.label"
                        :href="link.url"
                        v-html="link.label"
                        class="px-3 py-1 rounded text-sm"
                        :class="link.active ? 'bg-blue-500 text-white' : 'text-slate-600 hover:bg-slate-100'"
                        :disabled="!link.url"
                    />
                </div>
            </div>
        </div>
    </main>
</template>
