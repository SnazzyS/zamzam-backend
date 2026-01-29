<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';

const props = defineProps({
    trips: Array,
    stats: Object,
});

const activeFilter = ref('active');
const showEditModal = ref(false);
const editTrip = ref(null);

const editForm = useForm({
    name: '',
    departure_date: '',
    price: '',
});

const filteredTrips = computed(() => {
    if (activeFilter.value === 'active') {
        return props.trips.filter(trip => trip.status === 'active');
    }
    return props.trips;
});


const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US').format(amount) + ' MVR';
};

const getStatusBadge = (status) => {
    const badges = {
        active: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        completed: 'bg-slate-50 text-slate-700 ring-slate-600/20',
        cancelled: 'bg-red-50 text-red-700 ring-red-600/20',
    };
    return badges[status] || badges.active;
};

const openEditModal = (trip) => {
    editTrip.value = trip;
    editForm.name = trip?.name ?? '';
    editForm.departure_date = trip?.departure_date ?? '';
    editForm.price = trip?.price ?? '';
    editForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editTrip.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const submitEdit = () => {
    if (!editTrip.value) {
        return;
    }

    editForm.put(route('trips.update', editTrip.value.id), {
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const handleDhivehiInput = (event, form, field) => {
    const mapped = toDhivehi(event.target.value);
    if (mapped !== event.target.value) {
        event.target.value = mapped;
    }
    form[field] = mapped;
};

const handleDhivehiKeydown = (event, form, field) => {
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
    form[field] = nextValue;
};
</script>

<template>
    <Head title="Dashboard" />
    <div class="space-y-6 font-sans antialiased">
        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100">
                        <svg class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">Total Trips</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ stats.totalTrips }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">Active Trips</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ stats.activeTrips }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">Total Customers</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ stats.totalCustomers }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Trips Section -->
        <div class="rounded-xl border border-slate-200 bg-white">
            <!-- Header with Filter -->
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Trips</h2>
                <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-1">
                    <button
                        type="button"
                        @click="activeFilter = 'active'"
                        :class="[
                            'rounded-md px-3 py-1.5 text-sm font-medium transition',
                            activeFilter === 'active'
                                ? 'bg-white text-slate-900 shadow-sm'
                                : 'text-slate-600 hover:text-slate-900'
                        ]"
                    >
                        Active
                    </button>
                    <button
                        type="button"
                        @click="activeFilter = 'all'"
                        :class="[
                            'rounded-md px-3 py-1.5 text-sm font-medium transition',
                            activeFilter === 'all'
                                ? 'bg-white text-slate-900 shadow-sm'
                                : 'text-slate-600 hover:text-slate-900'
                        ]"
                    >
                        All
                    </button>
                </div>
            </div>

            <!-- Trips Grid -->
            <div class="p-5">
                <div v-if="filteredTrips.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="trip in filteredTrips"
                        :key="trip.id"
                        class="group relative rounded-xl border border-slate-200 transition hover:border-violet-200 hover:shadow-md"
                    >
                        <Link
                            :href="route('trips.show', trip.id)"
                            class="block p-5"
                        >
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <h3 class="font-semibold text-slate-900 group-hover:text-violet-600">{{ trip.name }}</h3>
                                <div class="flex items-center gap-2">
                                    <span :class="['inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset whitespace-nowrap', getStatusBadge(trip.status)]">
                                        {{ trip.status === 'active' ? 'Active' : trip.status }}
                                    </span>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-violet-200 hover:text-violet-600"
                                        @click.prevent="openEditModal(trip)"
                                        aria-label="Edit trip"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.121 2.121 0 00-3-3L5 17v3z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm" dir="ltr">
                                <div class="flex items-center gap-2 text-slate-500">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ formatDate(trip.departure_date) }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-slate-500">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>{{ trip.customers_count }} customers</span>
                                </div>

                                <div class="flex items-center gap-2 text-slate-500">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ formatCurrency(trip.price) }}</span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="font-medium text-slate-900">No Trips</p>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ activeFilter === 'active' ? 'No active trips at the moment' : 'No trips created yet' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Trip Modal -->
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
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeEditModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showEditModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Edit Trip</h3>
                                    <p class="text-sm text-slate-500">Update trip details</p>
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

                            <form @submit.prevent="submitEdit" class="space-y-4">
                                <div>
                                    <label for="edit-name" class="block text-sm font-medium text-slate-700 mb-1.5">Trip Name</label>
                                    <input
                                        id="edit-name"
                                        type="text"
                                        v-model="editForm.name"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="editForm.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="editForm.errors.name" class="text-xs text-red-500 mt-1">{{ editForm.errors.name }}</p>
                                </div>

                                <div>
                                    <label for="edit-departure-date" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Date</label>
                                    <input
                                        id="edit-departure-date"
                                        type="date"
                                        v-model="editForm.departure_date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="editForm.errors.departure_date && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="editForm.errors.departure_date" class="text-xs text-red-500 mt-1">{{ editForm.errors.departure_date }}</p>
                                </div>

                                <div>
                                    <label for="edit-price" class="block text-sm font-medium text-slate-700 mb-1.5">Price (MVR)</label>
                                    <input
                                        id="edit-price"
                                        type="number"
                                        v-model="editForm.price"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="editForm.errors.price && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="editForm.errors.price" class="text-xs text-red-500 mt-1">{{ editForm.errors.price }}</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeEditModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                                        :disabled="editForm.processing"
                                    >
                                        {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
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
