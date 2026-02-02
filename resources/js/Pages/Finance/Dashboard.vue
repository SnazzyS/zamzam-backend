<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import LineChart from '../../Components/Charts/LineChart.vue';
import BarChart from '../../Components/Charts/BarChart.vue';
import DoughnutChart from '../../Components/Charts/DoughnutChart.vue';

const props = defineProps({
    selectedYear: Number,
    years: Array,
    tripFinancials: Array,
    yearlySummary: Object,
    monthlyData: Array,
    expensesByCategory: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};

const formatCurrencyFull = (value) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const changeYear = (year) => {
    router.get(route('finance.dashboard'), { year }, { preserveState: true });
};

// Calculate profit margin
const profitMargin = computed(() => {
    if (!props.yearlySummary.totalRevenue || props.yearlySummary.totalRevenue === 0) return 0;
    return ((props.yearlySummary.netProfit / props.yearlySummary.totalRevenue) * 100).toFixed(1);
});

// Monthly Revenue vs Expenses Chart Data
const monthlyChartData = computed(() => ({
    labels: props.monthlyData.map(m => m.monthName.substring(0, 3)),
    datasets: [
        {
            label: 'Revenue',
            data: props.monthlyData.map(m => m.revenue),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#10b981',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 2,
            borderWidth: 2,
        },
        {
            label: 'Expenses',
            data: props.monthlyData.map(m => m.expenses),
            borderColor: '#f43f5e',
            backgroundColor: 'rgba(244, 63, 94, 0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#f43f5e',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 2,
            borderWidth: 2,
        },
    ],
}));

// Trip Profit/Loss Bar Chart Data
const tripProfitChartData = computed(() => ({
    labels: props.tripFinancials.slice(0, 6).map(t => t.name.length > 15 ? t.name.substring(0, 15) + '...' : t.name),
    datasets: [
        {
            label: 'Profit/Loss',
            data: props.tripFinancials.slice(0, 6).map(t => t.profit),
            backgroundColor: props.tripFinancials.slice(0, 6).map(t => t.profit >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(244, 63, 94, 0.8)'),
            borderRadius: 6,
            borderSkipped: false,
        },
    ],
}));

// Expenses by Category
const categoryLabels = {
    hotel: 'Hotel',
    transport: 'Transport',
    food: 'Food & Meals',
    visa: 'Visa',
    flights: 'Flights',
    other: 'Other',
};

const categoryColors = {
    hotel: '#3b82f6',
    transport: '#f59e0b',
    food: '#10b981',
    visa: '#8b5cf6',
    flights: '#ec4899',
    other: '#64748b',
};

const expensesCategoryChartData = computed(() => ({
    labels: props.expensesByCategory.map(c => categoryLabels[c.category] || c.category),
    datasets: [
        {
            data: props.expensesByCategory.map(c => c.total),
            backgroundColor: props.expensesByCategory.map(c => categoryColors[c.category] || '#64748b'),
            borderWidth: 0,
            spacing: 2,
        },
    ],
}));

// Total expenses for category percentages
const totalCategoryExpenses = computed(() => {
    return props.expensesByCategory.reduce((sum, c) => sum + Number(c.total), 0);
});
</script>

<template>
    <Head title="Finance" />

    <main class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Finance</h1>
                <p class="text-slate-500 mt-0.5">Financial overview for {{ selectedYear }}</p>
            </div>
            <div class="flex items-center gap-2">
                <select
                    :value="selectedYear"
                    @change="changeYear($event.target.value)"
                    class="h-9 rounded-lg border-0 bg-slate-100 pl-3 pr-8 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500"
                >
                    <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                </select>
                <Link
                    :href="route('finance.expenses.index')"
                    class="h-9 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 text-sm font-medium text-slate-600 transition hover:bg-slate-200"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Expenses
                </Link>
                <Link
                    :href="route('finance.expenses.create')"
                    class="h-9 inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 text-sm font-medium text-white transition hover:bg-blue-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Expense
                </Link>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Revenue Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white">
                <div class="absolute right-0 top-0 -mr-4 -mt-4 h-24 w-24 rounded-full bg-white/10"></div>
                <div class="relative">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white/80">Total Revenue</span>
                    </div>
                    <p class="mt-3 text-3xl font-bold" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ formatCurrency(yearlySummary.totalRevenue) }}</p>
                    <p class="mt-1 text-sm text-white/70" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ yearlySummary.tripCount }} trips this year</p>
                </div>
            </div>

            <!-- Expenses Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 p-5 text-white">
                <div class="absolute right-0 top-0 -mr-4 -mt-4 h-24 w-24 rounded-full bg-white/10"></div>
                <div class="relative">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white/80">Total Expenses</span>
                    </div>
                    <p class="mt-3 text-3xl font-bold" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ formatCurrency(yearlySummary.totalExpenses) }}</p>
                    <p class="mt-1 text-sm text-white/70" style="font-family: ui-sans-serif, system-ui, sans-serif;">
                        Trip: {{ formatCurrency(yearlySummary.tripExpenses) }} | General: {{ formatCurrency(yearlySummary.generalExpenses) }}
                    </p>
                </div>
            </div>

            <!-- Net Profit Card -->
            <div class="relative overflow-hidden rounded-xl bg-white p-5 ring-1 ring-slate-200">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg" :class="yearlySummary.netProfit >= 0 ? 'bg-emerald-100' : 'bg-rose-100'">
                        <svg v-if="yearlySummary.netProfit >= 0" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <svg v-else class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500">Net {{ yearlySummary.netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
                </div>
                <p class="mt-3 text-3xl font-bold" :class="yearlySummary.netProfit >= 0 ? 'text-emerald-600' : 'text-rose-600'" style="font-family: ui-sans-serif, system-ui, sans-serif;">
                    {{ yearlySummary.netProfit >= 0 ? '+' : '-' }}{{ formatCurrency(Math.abs(yearlySummary.netProfit)) }}
                </p>
                <div class="mt-2 flex items-center gap-1.5">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                        :class="yearlySummary.netProfit >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                        style="font-family: ui-sans-serif, system-ui, sans-serif;">
                        {{ profitMargin }}% margin
                    </span>
                </div>
            </div>

            <!-- Activity Revenue Card -->
            <div class="relative overflow-hidden rounded-xl bg-white p-5 ring-1 ring-slate-200">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500">Activities</span>
                </div>
                <p class="mt-3 text-3xl font-bold text-slate-800" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ formatCurrency(yearlySummary.activityRevenue) }}</p>
                <p class="mt-1 text-sm text-slate-400">Optional trip activities</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Monthly Trends - Large -->
            <div class="lg:col-span-2 rounded-xl bg-white p-6 ring-1 ring-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-semibold text-slate-800">Monthly Overview</h2>
                        <p class="text-sm text-slate-400">Revenue vs expenses trend</p>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-500">Revenue</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-slate-500">Expenses</span>
                        </div>
                    </div>
                </div>
                <div class="h-72">
                    <LineChart :chartData="monthlyChartData" />
                </div>
            </div>

            <!-- Expenses by Category - Small -->
            <div class="rounded-xl bg-white p-6 ring-1 ring-slate-200">
                <div class="mb-6">
                    <h2 class="font-semibold text-slate-800">Expenses by Category</h2>
                    <p class="text-sm text-slate-400">Breakdown of spending</p>
                </div>
                <div v-if="expensesByCategory.length > 0">
                    <div class="h-48 mb-4">
                        <DoughnutChart :chartData="expensesCategoryChartData" />
                    </div>
                    <div class="space-y-2">
                        <div v-for="cat in expensesByCategory.slice(0, 4)" :key="cat.category" class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: categoryColors[cat.category] || '#64748b' }"></span>
                                <span class="text-sm text-slate-600">{{ categoryLabels[cat.category] || cat.category }}</span>
                            </div>
                            <span class="text-sm font-medium text-slate-800" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ ((cat.total / totalCategoryExpenses) * 100).toFixed(0) }}%</span>
                        </div>
                    </div>
                </div>
                <div v-else class="h-48 flex items-center justify-center text-slate-400">
                    <div class="text-center">
                        <svg class="h-12 w-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="mt-2 text-sm">No expenses yet</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trip Performance -->
        <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800">Trip Performance</h2>
                    <p class="text-sm text-slate-400">Financial summary by trip</p>
                </div>
            </div>

            <div v-if="tripFinancials.length > 0">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Trip</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Customers</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Revenue</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Expenses</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Profit/Loss</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="trip in tripFinancials" :key="trip.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ trip.name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ trip.departure_date }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center h-7 min-w-[28px] rounded-full bg-slate-100 px-2 text-sm font-medium text-slate-600" style="font-family: ui-sans-serif, system-ui, sans-serif;">
                                    {{ trip.customers_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-800" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ formatCurrencyFull(trip.revenue) }}</td>
                            <td class="px-6 py-4 text-right font-medium text-slate-800" style="font-family: ui-sans-serif, system-ui, sans-serif;">{{ formatCurrencyFull(trip.expenses) }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-sm font-semibold"
                                    :class="trip.profit >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                                    style="font-family: ui-sans-serif, system-ui, sans-serif;">
                                    {{ trip.profit >= 0 ? '+' : '' }}{{ formatCurrencyFull(trip.profit) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="route('finance.trip-report', trip.id)"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700"
                                >
                                    View
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="py-16 text-center">
                <svg class="h-12 w-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="mt-3 text-slate-500">No trips recorded for {{ selectedYear }}</p>
                <Link :href="route('trips.index')" class="mt-3 inline-flex text-sm font-medium text-blue-600 hover:text-blue-700">
                    View all trips
                </Link>
            </div>
        </div>
    </main>
</template>
