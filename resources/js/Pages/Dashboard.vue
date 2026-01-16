<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    trips: Array,
    stats: Object,
});

// Filter state: 'active', 'inactive', 'all'
const filter = ref('active');

// Modal State
const showEditModal = ref(false);
const editingTrip = ref(null);

// Form for editing
const form = useForm({
    id: null,
    name: '',
    price: '',
    departure_date: '',
    phone_number: '',
    hotel_address: '',
});

// Check if a trip is active based on status field
const isActive = (trip) => {
    return trip.status === 'active';
};

// Filtered trips based on current filter
const filteredTrips = computed(() => {
    if (!props.trips) return [];

    switch (filter.value) {
        case 'active':
            return props.trips.filter(trip => isActive(trip));
        case 'inactive':
            return props.trips.filter(trip => !isActive(trip));
        default:
            return props.trips;
    }
});

// Format price with Rufiyaa
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-MV', {
        style: 'currency',
        currency: 'MVR',
        minimumFractionDigits: 0,
    }).format(price);
};

// Format date in Dhivehi-friendly format
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

// Open Edit Modal
const openEditModal = (trip) => {
    editingTrip.value = trip;
    form.id = trip.id;
    form.name = trip.name;
    form.price = trip.price;
    form.departure_date = trip.departure_date;
    form.phone_number = trip.phone_number;
    form.hotel_address = trip.hotel_address;
    showEditModal.value = true;
};

// Close Edit Modal
const closeEditModal = () => {
    showEditModal.value = false;
    editingTrip.value = null;
    form.reset();
    form.clearErrors();
};

// Submit Edit
const submitEdit = () => {
    form.put(route('trips.update', form.id), {
        onSuccess: () => {
            closeEditModal();
        },
    });
};
</script>

<template>
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="space-y-1">
            <h1 class="text-[1.75rem] font-bold text-slate-900">ޑޭޝްބޯޑު</h1>
            <p class="text-sm text-slate-500">ހުރިހާ ދަތުރުތައް ބައްލަވާ</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-4" v-if="stats">
            <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-bold text-slate-900">{{ stats.totalTrips }}</span>
                    <span class="text-xs text-slate-500">ޖުމްލަ ދަތުރު</span>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-bold text-slate-900">{{ stats.activeTrips }}</span>
                    <span class="text-xs text-slate-500">އެކްޓިވް ދަތުރު</span>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-400 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-bold text-slate-900">{{ stats.totalCustomers }}</span>
                    <span class="text-xs text-slate-500">ޖުމްލަ ކަސްޓަމަރުން</span>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-400 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-bold text-slate-900">{{ formatPrice(stats.totalRevenue) }}</span>
                    <span class="text-xs text-slate-500">ޖުމްލަ އާމްދަނީ</span>
                </div>
            </div>
        </div>

        <!-- Trips Section -->
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-slate-900">ދަތުރުތައް</h2>

                <!-- Filter Buttons -->
                <div class="flex gap-2" dir="ltr">
                    <button
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition',
                            filter === 'active'
                                ? 'bg-slate-900 text-white'
                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-600',
                        ]"
                        @click="filter = 'active'"
                    >
                        <span v-if="filter === 'active'" class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        އެކްޓިވް
                    </button>
                    <button
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition',
                            filter === 'inactive'
                                ? 'bg-slate-900 text-white'
                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-600',
                        ]"
                        @click="filter = 'inactive'"
                    >
                        ނިމިފައި
                    </button>
                    <button
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition',
                            filter === 'all'
                                ? 'bg-slate-900 text-white'
                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-600',
                        ]"
                        @click="filter = 'all'"
                    >
                        ހުރިހާ
                    </button>
                </div>
            </div>

            <!-- Trips Grid -->
            <div class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(300px,1fr))] gap-4" v-if="filteredTrips.length > 0">
                <div
                    v-for="trip in filteredTrips"
                    :key="trip.id"
                    class="group relative"
                >
                    <Link
                        :href="`/trips/${trip.id}`"
                        class="relative flex h-full flex-col gap-4 rounded-xl border border-slate-200 bg-slate-50 p-5 pr-12 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-100 hover:shadow-sm"
                    >
                        <!-- Active Indicator -->
                        <div class="absolute left-4 top-4 h-3 w-3" v-if="isActive(trip)">
                            <span class="absolute inline-flex h-3 w-3 animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                            <span class="absolute left-0.5 top-0.5 h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>

                        <!-- Trip Info -->
                        <div class="flex-1">
                            <h3 class="text-[1.1rem] font-semibold text-slate-900">{{ trip.name }}</h3>
                            <p class="text-xs text-slate-500">{{ formatDate(trip.departure_date) }}</p>
                        </div>

                        <!-- Trip Stats -->
                        <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ trip.customers_count }} ކަސްޓަމަރުން</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500">ދަތުރުގެ އަގު</span>
                                <span class="text-base font-bold text-emerald-500">
                                {{ formatPrice(trip.price) }}
                                </span>
                            </div>
                        </div>
                    </Link>

                    <!-- Edit Button -->
                    <button
                        @click.stop.prevent="openEditModal(trip)"
                        class="absolute right-3 top-3 z-10 inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 shadow-sm transition hover:border-slate-300 hover:text-slate-600"
                        title="ދަތުރު އެޑިޓް"
                    >
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-12 text-center text-slate-400" v-else>
                <svg class="mb-4 h-16 w-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-sm">މި ފިލްޓަރުގައި ދަތުރެއް ނެތް</p>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @click.self="closeEditModal">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-semibold text-slate-900">ދަތުރު އެޑިޓް</h3>
                    <button type="button" @click="closeEditModal" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitEdit" class="space-y-5">
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium text-slate-700 text-right">ދަތުރުގެ ނަން</label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            dir="rtl"
                            lang="dv"
                            class="w-full rounded-lg border px-3 py-2 text-sm text-slate-900 transition focus:outline-none focus:ring-2"
                            :class="form.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200'"
                            required
                        >
                        <p v-if="form.errors.name" class="text-xs text-red-500 text-left" dir="ltr">{{ form.errors.name }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="price" class="text-sm font-medium text-slate-700 text-right">އަގު (MVR)</label>
                        <input
                            id="price"
                            type="number"
                            v-model="form.price"
                            dir="ltr"
                            class="w-full rounded-lg border px-3 py-2 text-sm text-slate-900 transition focus:outline-none focus:ring-2"
                            :class="form.errors.price ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200'"
                            required
                        >
                        <p v-if="form.errors.price" class="text-xs text-red-500 text-left" dir="ltr">{{ form.errors.price }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="closeEditModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-600">ކެންސަލް</button>
                        <button type="submit" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-70" :disabled="form.processing">
                            {{ form.processing ? 'ރައްކާލަމުން...' : 'ބަދަލު ރައްކާލާ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
