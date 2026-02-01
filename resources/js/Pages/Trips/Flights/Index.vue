<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    trip: Object,
    flights: Array,
    customersWithoutFlight: Array,
    allCustomers: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showAssignModal = ref(false);
const editingFlight = ref(null);
const assigningFlight = ref(null);

const flightForm = useForm({
    name: '',
    departure_date: '',
    departure_time: '',
    return_date: '',
    return_time: '',
    departure_flight_number: '',
    departure_transit_flight_number: '',
    return_flight_number: '',
    return_transit_flight_number: '',
});

const assignForm = useForm({
    customer_id: '',
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatTime = (timeString) => {
    if (!timeString) return '';
    // Handle HH:mm:ss or HH:mm format
    const parts = timeString.split(':');
    if (parts.length >= 2) {
        const hours = parseInt(parts[0]);
        const minutes = parts[1];
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const hour12 = hours % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
    return timeString;
};

const openCreateModal = () => {
    flightForm.reset();
    flightForm.clearErrors();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    flightForm.reset();
    flightForm.clearErrors();
};

const submitCreate = () => {
    if (returnDateError.value) return;
    flightForm.post(route('trips.flights.store', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

const openEditModal = (flight) => {
    editingFlight.value = flight;
    flightForm.name = flight.name || '';
    flightForm.departure_date = flight.departure_date ? flight.departure_date.split('T')[0] : '';
    flightForm.departure_time = flight.departure_time ? flight.departure_time.substring(0, 5) : '';
    flightForm.return_date = flight.return_date ? flight.return_date.split('T')[0] : '';
    flightForm.return_time = flight.return_time ? flight.return_time.substring(0, 5) : '';
    flightForm.departure_flight_number = flight.departure_flight_number || '';
    flightForm.departure_transit_flight_number = flight.departure_transit_flight_number || '';
    flightForm.return_flight_number = flight.return_flight_number || '';
    flightForm.return_transit_flight_number = flight.return_transit_flight_number || '';
    flightForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingFlight.value = null;
    flightForm.reset();
    flightForm.clearErrors();
};

const submitEdit = () => {
    if (!editingFlight.value || returnDateError.value) return;
    flightForm.put(route('trips.flights.update', [props.trip.id, editingFlight.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const deleteFlight = (flight) => {
    if (!confirm('Are you sure you want to delete this flight? All customer assignments will be removed.')) return;
    router.delete(route('trips.flights.destroy', [props.trip.id, flight.id]), {
        preserveScroll: true,
    });
};

const openAssignModal = (flight) => {
    assigningFlight.value = flight;
    assignForm.customer_id = '';
    assignForm.clearErrors();
    showAssignModal.value = true;
};

const closeAssignModal = () => {
    showAssignModal.value = false;
    assigningFlight.value = null;
    assignForm.reset();
    assignForm.clearErrors();
};

const submitAssign = () => {
    if (!assigningFlight.value) return;
    assignForm.post(route('trips.flights.assign-customer', [props.trip.id, assigningFlight.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeAssignModal();
        },
    });
};

const removeCustomer = (flight, customerId) => {
    router.delete(route('trips.flights.remove-customer', [props.trip.id, flight.id]), {
        data: { customer_id: customerId },
        preserveScroll: true,
    });
};

const getPassengersForFlight = (flightId) => {
    return props.allCustomers.filter(c => c.flight_id === flightId);
};

const availableCustomersForAssignment = computed(() => {
    return props.customersWithoutFlight || [];
});

const returnDateError = computed(() => {
    if (flightForm.departure_date && flightForm.return_date) {
        if (new Date(flightForm.return_date) < new Date(flightForm.departure_date)) {
            return 'Return date cannot be before departure date';
        }
    }
    return null;
});
</script>

<template>
    <Head title="Trip Flights" />

    <main class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Flights</h1>
                <p class="mt-1 text-slate-500">Manage flights and passenger assignments</p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md bg-blue-500 px-4 py-2.5 text-sm font-medium text-white  transition-all duration-200 hover:bg-blue-600"
                @click="openCreateModal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Flight
            </button>
        </div>

        <!-- Flights Grid -->
        <div v-if="flights.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="flight in flights"
                :key="flight.id"
                class="group relative rounded-md bg-white p-4 transition hover:bg-slate-50"
            >
                <!-- Customer Count Badge -->
                <div class="absolute left-4 top-4">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ flight.customers_count || 0 }} passengers
                    </span>
                </div>

                <div class="pt-8">
                    <h2 class="text-lg font-semibold text-slate-800">{{ flight.name }}</h2>

                    <!-- Flight Dates -->
                    <div class="mt-3 space-y-1.5 text-sm">
                        <div class="flex items-center gap-2 text-slate-600">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span class="text-slate-500">Departure:</span>
                            <span>{{ formatDate(flight.departure_date) }}</span>
                            <span v-if="flight.departure_time" class="text-slate-500">at</span>
                            <span v-if="flight.departure_time" class="font-medium">{{ formatTime(flight.departure_time) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-600">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span class="text-slate-500">Return:</span>
                            <span>{{ formatDate(flight.return_date) }}</span>
                            <span v-if="flight.return_time" class="text-slate-500">at</span>
                            <span v-if="flight.return_time" class="font-medium">{{ formatTime(flight.return_time) }}</span>
                        </div>
                    </div>

                    <!-- Flight Numbers -->
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div v-if="flight.departure_flight_number" class="rounded-md bg-slate-50 px-2.5 py-2">
                            <span class="text-slate-500">Dep:</span>
                            <span class="ml-1 font-mono font-medium text-slate-700">{{ flight.departure_flight_number }}</span>
                        </div>
                        <div v-if="flight.departure_transit_flight_number" class="rounded-md bg-slate-50 px-2.5 py-2">
                            <span class="text-slate-500">Dep Transit:</span>
                            <span class="ml-1 font-mono font-medium text-slate-700">{{ flight.departure_transit_flight_number }}</span>
                        </div>
                        <div v-if="flight.return_flight_number" class="rounded-md bg-slate-50 px-2.5 py-2">
                            <span class="text-slate-500">Ret:</span>
                            <span class="ml-1 font-mono font-medium text-slate-700">{{ flight.return_flight_number }}</span>
                        </div>
                        <div v-if="flight.return_transit_flight_number" class="rounded-md bg-slate-50 px-2.5 py-2">
                            <span class="text-slate-500">Ret Transit:</span>
                            <span class="ml-1 font-mono font-medium text-slate-700">{{ flight.return_transit_flight_number }}</span>
                        </div>
                    </div>

                    <!-- Passengers List -->
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-slate-700">Passengers</h3>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-100"
                                @click="openAssignModal(flight)"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Add
                            </button>
                        </div>
                        <div v-if="getPassengersForFlight(flight.id).length" class="space-y-1.5 max-h-48 overflow-y-auto">
                            <div
                                v-for="passenger in getPassengersForFlight(flight.id)"
                                :key="passenger.id"
                                class="flex items-center justify-between rounded-md bg-slate-50 px-2.5 py-2 text-xs"
                            >
                                <div class="flex items-center gap-2 min-w-0" dir="rtl" lang="dv">
                                    <span v-if="passenger.umrah_id" class="shrink-0 rounded bg-blue-100 px-1.5 py-0.5 font-mono text-blue-700">{{ passenger.umrah_id }}</span>
                                    <span class="truncate font-medium text-slate-700">{{ passenger.name }}</span>
                                </div>
                                <button
                                    type="button"
                                    class="shrink-0 ml-2 rounded p-1 text-slate-400 transition hover:bg-red-100 hover:text-red-600"
                                    @click="removeCustomer(flight, passenger.id)"
                                    title="Remove passenger"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-xs text-slate-400 text-center py-2">No passengers</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <Link
                        :href="route('trips.flights.show', [trip.id, flight.id])"
                        class="flex-1 rounded-md bg-blue-500 px-4 py-2.5 text-center text-sm font-medium text-white  transition-all duration-200 hover:bg-blue-600"
                    >
                        Flight Details
                    </Link>
                    <button
                        type="button"
                        class="rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50"
                        @click="openEditModal(flight)"
                        title="Edit Flight"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.121 2.121 0 00-3-3L5 17v3z" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-red-200 bg-white px-3 py-2.5 text-sm font-medium text-red-600 transition-all duration-200 hover:bg-red-50"
                        @click="deleteFlight(flight)"
                        title="Delete Flight"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center rounded-md border border-dashed border-slate-200 bg-slate-50/50 py-16 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-md bg-slate-100">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
            </div>
            <h3 class="text-base font-medium text-slate-800 mb-1">No Flights</h3>
            <p class="text-sm text-slate-500">Create a flight to start assigning passengers</p>
            <button
                type="button"
                class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-500 px-4 py-2.5 text-sm font-medium text-white  transition-all duration-200 hover:bg-blue-600"
                @click="openCreateModal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Flight
            </button>
        </div>
    </main>

    <!-- Create Flight Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showCreateModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/50" @click="closeCreateModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="duration-100 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showCreateModal" class="relative w-full max-w-lg rounded-md bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Add New Flight</h3>
                                    <p class="text-sm text-slate-500">Create a new flight for this trip</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeCreateModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitCreate" class="space-y-4">
                                <div>
                                    <label for="create-name" class="block text-sm font-medium text-slate-700 mb-1.5">Flight Name *</label>
                                    <input
                                        id="create-name"
                                        type="text"
                                        v-model="flightForm.name"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="flightForm.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        placeholder="e.g., Flight Group 1"
                                        required
                                    >
                                    <p v-if="flightForm.errors.name" class="text-xs text-red-500 mt-1">{{ flightForm.errors.name }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="create-departure-date" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Date</label>
                                        <input
                                            id="create-departure-date"
                                            type="date"
                                            v-model="flightForm.departure_date"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="flightForm.errors.departure_date && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        >
                                        <p v-if="flightForm.errors.departure_date" class="text-xs text-red-500 mt-1">{{ flightForm.errors.departure_date }}</p>
                                    </div>
                                    <div>
                                        <label for="create-departure-time" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Time</label>
                                        <input
                                            id="create-departure-time"
                                            type="time"
                                            v-model="flightForm.departure_time"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="flightForm.errors.departure_time && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        >
                                        <p v-if="flightForm.errors.departure_time" class="text-xs text-red-500 mt-1">{{ flightForm.errors.departure_time }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="create-return-date" class="block text-sm font-medium text-slate-700 mb-1.5">Return Date</label>
                                        <input
                                            id="create-return-date"
                                            type="date"
                                            v-model="flightForm.return_date"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="(flightForm.errors.return_date || returnDateError) && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        >
                                        <p v-if="returnDateError" class="text-xs text-red-500 mt-1">{{ returnDateError }}</p>
                                        <p v-else-if="flightForm.errors.return_date" class="text-xs text-red-500 mt-1">{{ flightForm.errors.return_date }}</p>
                                    </div>
                                    <div>
                                        <label for="create-return-time" class="block text-sm font-medium text-slate-700 mb-1.5">Return Time</label>
                                        <input
                                            id="create-return-time"
                                            type="time"
                                            v-model="flightForm.return_time"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="flightForm.errors.return_time && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        >
                                        <p v-if="flightForm.errors.return_time" class="text-xs text-red-500 mt-1">{{ flightForm.errors.return_time }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="create-departure-flight" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Flight #</label>
                                        <input
                                            id="create-departure-flight"
                                            type="text"
                                            v-model="flightForm.departure_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="e.g., WY123"
                                        >
                                    </div>
                                    <div>
                                        <label for="create-departure-transit" class="block text-sm font-medium text-slate-700 mb-1.5">Dep. Transit Flight #</label>
                                        <input
                                            id="create-departure-transit"
                                            type="text"
                                            v-model="flightForm.departure_transit_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="e.g., SV456"
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="create-return-flight" class="block text-sm font-medium text-slate-700 mb-1.5">Return Flight #</label>
                                        <input
                                            id="create-return-flight"
                                            type="text"
                                            v-model="flightForm.return_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="e.g., SV789"
                                        >
                                    </div>
                                    <div>
                                        <label for="create-return-transit" class="block text-sm font-medium text-slate-700 mb-1.5">Ret. Transit Flight #</label>
                                        <input
                                            id="create-return-transit"
                                            type="text"
                                            v-model="flightForm.return_transit_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="e.g., WY321"
                                        >
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeCreateModal"
                                        class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="flightForm.processing || returnDateError"
                                    >
                                        {{ flightForm.processing ? 'Creating...' : 'Create Flight' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Edit Flight Modal -->
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
                    <div class="fixed inset-0 bg-black/50" @click="closeEditModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="duration-100 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showEditModal" class="relative w-full max-w-lg rounded-md bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Edit Flight</h3>
                                    <p class="text-sm text-slate-500">Update flight details</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeEditModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitEdit" class="space-y-4">
                                <div>
                                    <label for="edit-name" class="block text-sm font-medium text-slate-700 mb-1.5">Flight Name *</label>
                                    <input
                                        id="edit-name"
                                        type="text"
                                        v-model="flightForm.name"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="flightForm.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="flightForm.errors.name" class="text-xs text-red-500 mt-1">{{ flightForm.errors.name }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit-departure-date" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Date</label>
                                        <input
                                            id="edit-departure-date"
                                            type="date"
                                            v-model="flightForm.departure_date"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                    <div>
                                        <label for="edit-departure-time" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Time</label>
                                        <input
                                            id="edit-departure-time"
                                            type="time"
                                            v-model="flightForm.departure_time"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit-return-date" class="block text-sm font-medium text-slate-700 mb-1.5">Return Date</label>
                                        <input
                                            id="edit-return-date"
                                            type="date"
                                            v-model="flightForm.return_date"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="returnDateError && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        >
                                        <p v-if="returnDateError" class="text-xs text-red-500 mt-1">{{ returnDateError }}</p>
                                    </div>
                                    <div>
                                        <label for="edit-return-time" class="block text-sm font-medium text-slate-700 mb-1.5">Return Time</label>
                                        <input
                                            id="edit-return-time"
                                            type="time"
                                            v-model="flightForm.return_time"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit-departure-flight" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Flight #</label>
                                        <input
                                            id="edit-departure-flight"
                                            type="text"
                                            v-model="flightForm.departure_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                    <div>
                                        <label for="edit-departure-transit" class="block text-sm font-medium text-slate-700 mb-1.5">Dep. Transit Flight #</label>
                                        <input
                                            id="edit-departure-transit"
                                            type="text"
                                            v-model="flightForm.departure_transit_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit-return-flight" class="block text-sm font-medium text-slate-700 mb-1.5">Return Flight #</label>
                                        <input
                                            id="edit-return-flight"
                                            type="text"
                                            v-model="flightForm.return_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                    <div>
                                        <label for="edit-return-transit" class="block text-sm font-medium text-slate-700 mb-1.5">Ret. Transit Flight #</label>
                                        <input
                                            id="edit-return-transit"
                                            type="text"
                                            v-model="flightForm.return_transit_flight_number"
                                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeEditModal"
                                        class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="flightForm.processing || returnDateError"
                                    >
                                        {{ flightForm.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

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
                    <div class="fixed inset-0 bg-black/50" @click="closeAssignModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="duration-100 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showAssignModal" class="relative w-full max-w-md rounded-md bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Assign Customer</h3>
                                    <p class="text-sm text-slate-500">Add a passenger to {{ assigningFlight?.name }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeAssignModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitAssign" class="space-y-4">
                                <div>
                                    <label for="assign-customer" class="block text-sm font-medium text-slate-700 mb-1.5">Select Customer</label>
                                    <select
                                        id="assign-customer"
                                        v-model="assignForm.customer_id"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="assignForm.errors.customer_id && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                        <option value="">Select a customer...</option>
                                        <option v-for="customer in availableCustomersForAssignment" :key="customer.id" :value="customer.id">
                                            {{ customer.name }} ({{ customer.national_id }})
                                        </option>
                                    </select>
                                    <p v-if="assignForm.errors.customer_id" class="text-xs text-red-500 mt-1">{{ assignForm.errors.customer_id }}</p>
                                    <p v-if="availableCustomersForAssignment.length === 0" class="text-xs text-amber-600 mt-1">
                                        All customers in this trip are already assigned to flights.
                                    </p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeAssignModal"
                                        class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="flightForm.processing || availableCustomersForAssignment.length === 0"
                                    >
                                        {{ assignForm.processing ? 'Assigning...' : 'Assign Customer' }}
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
