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
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const changeYear = (year) => {
    router.get(route('finance.dashboard'), { year }, { preserveState: true });
};

// Monthly Revenue vs Expenses Chart Data
const monthlyChartData = computed(() => ({
    labels: props.monthlyData.map(m => m.monthName),
    datasets: [
        {
            label: 'Revenue',
            data: props.monthlyData.map(m => m.revenue),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4,
        },
        {
            label: 'Expenses',
            data: props.monthlyData.map(m => m.expenses),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            fill: true,
            tension: 0.4,
        },
    ],
}));

// Trip Profit/Loss Bar Chart Data
const tripProfitChartData = computed(() => ({
    labels: props.tripFinancials.map(t => t.name),
    datasets: [
        {
            label: 'Profit/Loss',
            data: props.tripFinancials.map(t => t.profit),
            backgroundColor: props.tripFinancials.map(t => t.profit >= 0 ? '#10b981' : '#ef4444'),
            borderRadius: 4,
        },
    ],
}));

// Expenses by Category Doughnut Chart Data
const categoryColors = {
    hotel: '#3b82f6',
    transport: '#f59e0b',
    food: '#10b981',
    visa: '#8b5cf6',
    flights: '#ec4899',
    other: '#6b7280',
};

const expensesCategoryChartData = computed(() => ({
    labels: props.expensesByCategory.map(c => c.category.charAt(0).toUpperCase() + c.category.slice(1)),
    datasets: [
        {
            data: props.expensesByCategory.map(c => c.total),
            backgroundColor: props.expensesByCategory.map(c => categoryColors[c.category] || '#6b7280'),
        },
    ],
}));
</script>

<template>
    <Head title="Finance Dashboard" />

    <main class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Finance Dashboard</h1>
                <p class="text-sm text-slate-500">Overview of revenue, expenses, and profits</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Year Selector -->
                <select
                    :value="selectedYear"
                    @change="changeYear($event.target.value)"
                    class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                    <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                </select>
                <Link
                    :href="route('finance.expenses.create')"
                    class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600"
                >
                    Add Expense
                </Link>
                <Link
                    :href="route('finance.expenses.index')"
                    class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    All Expenses
                </Link>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Total Revenue</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ formatCurrency(yearlySummary.totalRevenue) }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ yearlySummary.tripCount }} trips</p>
            </div>
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Total Expenses</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ formatCurrency(yearlySummary.totalExpenses) }}</p>
                <p class="text-xs text-slate-400 mt-1">Trip: {{ formatCurrency(yearlySummary.tripExpenses) }} | General: {{ formatCurrency(yearlySummary.generalExpenses) }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Net Profit/Loss</p>
                <p class="text-2xl font-bold mt-1" :class="yearlySummary.netProfit >= 0 ? 'text-emerald-600' : 'text-red-600'">
                    {{ yearlySummary.netProfit >= 0 ? '' : '-' }}{{ formatCurrency(Math.abs(yearlySummary.netProfit)) }}
                </p>
                <p class="text-xs mt-1" :class="yearlySummary.netProfit >= 0 ? 'text-emerald-500' : 'text-red-500'">
                    {{ yearlySummary.netProfit >= 0 ? 'Profit' : 'Loss' }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-lg">
                <p class="text-sm text-slate-500">Activity Revenue</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ formatCurrency(yearlySummary.activityRevenue) }}</p>
                <p class="text-xs text-slate-400 mt-1">Optional trips</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Monthly Revenue vs Expenses -->
            <div class="bg-white p-5 rounded-lg">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Monthly Revenue vs Expenses</h2>
                <div class="h-64">
                    <LineChart :chartData="monthlyChartData" />
                </div>
            </div>

            <!-- Expenses by Category -->
            <div class="bg-white p-5 rounded-lg">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Expenses by Category</h2>
                <div class="h-64">
                    <DoughnutChart
                        v-if="expensesByCategory.length > 0"
                        :chartData="expensesCategoryChartData"
                    />
                    <div v-else class="h-full flex items-center justify-center text-slate-400">
                        No expense data
                    </div>
                </div>
            </div>
        </div>

        <!-- Trip Profit/Loss Chart -->
        <div class="bg-white p-5 rounded-lg" v-if="tripFinancials.length > 0">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Trip Profit/Loss</h2>
            <div class="h-64">
                <BarChart :chartData="tripProfitChartData" />
            </div>
        </div>

        <!-- Trip Financial Details Table -->
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Trip Financial Summary</h2>
            </div>
            <div v-if="tripFinancials.length > 0" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-slate-600">Trip</th>
                            <th class="px-5 py-3 text-center font-medium text-slate-600">Customers</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Revenue</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Expenses</th>
                            <th class="px-5 py-3 text-right font-medium text-slate-600">Profit/Loss</th>
                            <th class="px-5 py-3 text-center font-medium text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="trip in tripFinancials" :key="trip.id" class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ trip.name }}</div>
                                <div class="text-xs text-slate-400">{{ trip.departure_date }}</div>
                            </td>
                            <td class="px-5 py-3 text-center text-slate-600">{{ trip.customers_count }}</td>
                            <td class="px-5 py-3 text-right text-emerald-600 font-medium">{{ formatCurrency(trip.revenue) }}</td>
                            <td class="px-5 py-3 text-right text-red-600 font-medium">{{ formatCurrency(trip.expenses) }}</td>
                            <td class="px-5 py-3 text-right font-semibold" :class="trip.profit >= 0 ? 'text-emerald-600' : 'text-red-600'">
                                {{ trip.profit >= 0 ? '' : '-' }}{{ formatCurrency(Math.abs(trip.profit)) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <Link
                                    :href="route('finance.trip-report', trip.id)"
                                    class="text-blue-600 hover:text-blue-700 text-xs font-medium"
                                >
                                    View Report
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-8 text-center text-slate-400">
                No trips for this year
            </div>
        </div>
    </main>
</template>
