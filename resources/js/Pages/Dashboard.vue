<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';

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

// Get counts for filter badges
const activeTripCount = computed(() => props.trips?.filter(trip => isActive(trip)).length || 0);
const inactiveTripCount = computed(() => props.trips?.filter(trip => !isActive(trip)).length || 0);

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

const handleDhivehiInput = (event, targetForm, field) => {
    const mapped = toDhivehi(event.target.value);
    if (mapped !== event.target.value) {
        event.target.value = mapped;
    }
    targetForm[field] = mapped;
};

const handleDhivehiKeydown = (event, targetForm, field) => {
    if (event.isComposing || event.ctrlKey || event.metaKey || event.altKey) {
        return;
    }

    const key = event.key;
    if (key.length !== 1) {
        return;
    }

    const mapped = tkGetChar(key);
    if (mapped === key) {
        return;
    }

    event.preventDefault();
    const input = event.target;
    const start = input.selectionStart ?? 0;
    const end = input.selectionEnd ?? 0;
    const nextValue = `${input.value.slice(0, start)}${mapped}${input.value.slice(end)}`;
    input.value = nextValue;
    const nextPos = start + mapped.length;
    input.setSelectionRange(nextPos, nextPos);
    targetForm[field] = nextValue;
};
</script>

<template>
    <div class="min-h-screen">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">ޑޭޝްބޯޑު</h1>
            <p class="mt-1 text-slate-500">ހުރިހާ ދަތުރުތަކުގެ މައުލޫމާތު</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10" v-if="stats">
            <!-- Total Trips -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 p-5 text-white shadow-lg shadow-violet-500/20">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.totalTrips }}</p>
                    <p class="text-sm text-white/80 mt-1">ޖުމްލަ ދަތުރު</p>
                </div>
                <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/10"></div>
            </div>

            <!-- Active Trips -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-lg shadow-emerald-500/20">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-2.5 w-2.5 animate-ping rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-white"></span>
                        </span>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.activeTrips }}</p>
                    <p class="text-sm text-white/80 mt-1">އެކްޓިވް ދަތުރު</p>
                </div>
                <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/10"></div>
            </div>

            <!-- Total Customers -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 p-5 text-white shadow-lg shadow-sky-500/20">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.totalCustomers }}</p>
                    <p class="text-sm text-white/80 mt-1">ޖުމްލަ ކަސްޓަމަރުން</p>
                </div>
                <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/10"></div>
            </div>

            <!-- Total Revenue -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 text-white shadow-lg shadow-amber-500/20">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold">{{ formatPrice(stats.totalRevenue) }}</p>
                    <p class="text-sm text-white/80 mt-1">ޖުމްލަ އާމްދަނީ</p>
                </div>
                <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/10"></div>
            </div>
        </div>

        <!-- Trips Section -->
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-200/50">
            <!-- Section Header -->
            <div class="p-6 border-b border-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">ދަތުރުތައް</h2>
                        <p class="text-sm text-slate-500 mt-0.5">{{ filteredTrips.length }} ދަތުރު</p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="inline-flex items-center rounded-xl bg-slate-100 p-1" dir="ltr">
                        <button
                            @click="filter = 'active'"
                            :class="[
                                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200',
                                filter === 'active'
                                    ? 'bg-white text-slate-900 shadow-sm'
                                    : 'text-slate-600 hover:text-slate-900',
                            ]"
                        >
                            <span v-if="filter === 'active'" class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            <span v-else class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            އެކްޓިވް
                            <span class="text-xs text-slate-400">{{ activeTripCount }}</span>
                        </button>
                        <button
                            @click="filter = 'inactive'"
                            :class="[
                                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200',
                                filter === 'inactive'
                                    ? 'bg-white text-slate-900 shadow-sm'
                                    : 'text-slate-600 hover:text-slate-900',
                            ]"
                        >
                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                            ނިމިފައި
                            <span class="text-xs text-slate-400">{{ inactiveTripCount }}</span>
                        </button>
                        <button
                            @click="filter = 'all'"
                            :class="[
                                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200',
                                filter === 'all'
                                    ? 'bg-white text-slate-900 shadow-sm'
                                    : 'text-slate-600 hover:text-slate-900',
                            ]"
                        >
                            ހުރިހާ
                            <span class="text-xs text-slate-400">{{ trips?.length || 0 }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Trips Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" v-if="filteredTrips.length > 0">
                    <div
                        v-for="trip in filteredTrips"
                        :key="trip.id"
                        class="group relative"
                    >
                        <Link
                            :href="`/trips/${trip.id}`"
                            class="block rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:border-slate-300 hover:shadow-md hover:shadow-slate-200/50"
                        >
                            <!-- Price Badge -->
                            <div class="absolute left-4 top-4 rounded-lg bg-violet-600 px-3 py-1.5 text-white shadow-md shadow-violet-600/20">
                                <span class="text-sm font-bold">{{ formatPrice(trip.price) }}</span>
                            </div>

                            <!-- Card Header -->
                            <div class="pt-10 mb-4">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span v-if="isActive(trip)" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        އެކްޓިވް
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                        ނިމިފައި
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800 leading-snug">{{ trip.name }}</h3>
                            </div>

                            <!-- Trip Details -->
                            <div class="flex items-center gap-4 text-sm text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ formatDate(trip.departure_date) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ trip.customers_count }}</span>
                                </div>
                            </div>
                        </Link>

                        <!-- Edit Button -->
                        <button
                            @click.stop.prevent="openEditModal(trip)"
                            class="absolute right-4 top-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-slate-500 shadow-sm ring-1 ring-slate-200 backdrop-blur-sm transition-all duration-200 hover:bg-white hover:text-slate-700 hover:shadow"
                            title="ދަތުރު އެޑިޓް"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-16 text-center" v-else>
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                        <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-slate-800 mb-1">ދަތުރެއް ނެތް</h3>
                    <p class="text-sm text-slate-500">މި ފިލްޓަރުގައި ދަތުރެއް ނެތް</p>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showEditModal" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeEditModal"></div>

                        <!-- Modal -->
                        <Transition
                            enter-active-class="duration-200 ease-out"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="duration-150 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div v-if="showEditModal" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                                <!-- Modal Header -->
                                <div class="mb-6 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-800">ދަތުރު އެޑިޓް</h3>
                                        <p class="text-sm text-slate-500 mt-0.5">ދަތުރުގެ މައުލޫމާތު ބަދަލުކުރޭ</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="closeEditModal"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <form @submit.prevent="submitEdit" class="space-y-5">
                                    <div class="space-y-1.5">
                                        <label for="name" class="block text-sm font-medium text-slate-700">ދަތުރުގެ ނަން</label>
                                        <input
                                            id="name"
                                            type="text"
                                            :value="form.name"
                                            dir="rtl"
                                            lang="dv"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                            @keydown="handleDhivehiKeydown($event, form, 'name')"
                                            @input="handleDhivehiInput($event, form, 'name')"
                                            :class="form.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/10'"
                                            required
                                        >
                                        <p v-if="form.errors.name" class="text-xs text-red-500 mt-1" dir="ltr">{{ form.errors.name }}</p>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="departure_date" class="block text-sm font-medium text-slate-700">ފުރާ ތާރީޚު</label>
                                        <input
                                            id="departure_date"
                                            type="date"
                                            v-model="form.departure_date"
                                            dir="ltr"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                            :class="form.errors.departure_date && 'border-red-300 focus:border-red-500 focus:ring-red-500/10'"
                                            required
                                        >
                                        <p v-if="form.errors.departure_date" class="text-xs text-red-500 mt-1" dir="ltr">{{ form.errors.departure_date }}</p>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="price" class="block text-sm font-medium text-slate-700">އަގު (MVR)</label>
                                        <input
                                            id="price"
                                            type="number"
                                            v-model="form.price"
                                            dir="ltr"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                            :class="form.errors.price && 'border-red-300 focus:border-red-500 focus:ring-red-500/10'"
                                            required
                                        >
                                        <p v-if="form.errors.price" class="text-xs text-red-500 mt-1" dir="ltr">{{ form.errors.price }}</p>
                                    </div>

                                    <div class="flex items-center justify-end gap-3 pt-4">
                                        <button
                                            type="button"
                                            @click="closeEditModal"
                                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50"
                                        >
                                            ކެންސަލް
                                        </button>
                                        <button
                                            type="submit"
                                            class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                                            :disabled="form.processing"
                                        >
                                            {{ form.processing ? 'ރައްކާލަމުން...' : 'ބަދަލު ރައްކާލާ' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
