<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    bus: Object,
    passengers: Array,
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

const capacityStatus = () => {
    const count = props.passengers.length;
    const capacity = props.bus.capacity;
    if (count >= capacity) return { label: 'Full', class: 'bg-red-100 text-red-700' };
    if (count >= capacity * 0.8) return { label: 'Almost Full', class: 'bg-amber-100 text-amber-700' };
    return { label: 'Available', class: 'bg-emerald-100 text-emerald-700' };
};

const removePassenger = (customerId) => {
    if (!confirm('Are you sure you want to remove this passenger from the bus?')) return;
    router.delete(route('trips.buses.remove-customer', [props.trip.id, props.bus.id]), {
        data: { customer_id: customerId },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Bus - ${bus.name}`" />

    <main class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <Link
                        :href="route('trips.buses.index', trip.id)"
                        class="text-slate-400 hover:text-violet-600 transition"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div
                        v-if="bus.color_code"
                        class="w-5 h-5 rounded-full border-2 border-white shadow"
                        :style="{ backgroundColor: bus.color_code }"
                    ></div>
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ bus.name }}</h1>
                </div>
                <p class="mt-1 text-slate-500">{{ passengers.length }} passengers assigned to this bus</p>
            </div>
        </div>

        <!-- Bus Details Card -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                v-if="bus.color_code"
                class="h-2"
                :style="{ backgroundColor: bus.color_code }"
            ></div>
            <div class="p-5">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Bus Details</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs text-slate-500">Bus Name</span>
                        <p class="text-sm font-medium text-slate-800">{{ bus.name }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">Bus Number</span>
                        <p class="text-sm font-medium font-mono text-slate-800">#{{ bus.bus_number }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">Capacity</span>
                        <p class="text-sm font-medium text-slate-800">{{ passengers.length }} / {{ bus.capacity }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">Status</span>
                        <p>
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="capacityStatus().class"
                            >
                                {{ capacityStatus().label }}
                            </span>
                        </p>
                    </div>
                    <div v-if="bus.color_code">
                        <span class="text-xs text-slate-500">Color</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div
                                class="w-4 h-4 rounded border border-slate-200"
                                :style="{ backgroundColor: bus.color_code }"
                            ></div>
                            <span class="text-xs font-mono text-slate-600">{{ bus.color_code }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Passengers Table -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Passengers</h2>
            </div>

            <div v-if="passengers.length" class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                        <tr>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">#</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Umrah ID</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Name</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Name (English)</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">National ID</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Passport</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Phone</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">DOB</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr
                            v-for="(passenger, index) in passengers"
                            :key="passenger.id"
                            class="even:bg-slate-50/50 hover:bg-violet-50/50 transition-colors"
                        >
                            <td class="border-b border-slate-100 px-4 py-3 text-slate-500">{{ index + 1 }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-mono text-slate-600">{{ passenger.umrah_id || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3">
                                <Link
                                    :href="route('trips.customers.show', [trip.id, passenger.id])"
                                    class="font-medium text-slate-800 hover:text-violet-600 transition"
                                >
                                    {{ passenger.name }}
                                </Link>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3">{{ passenger.name_in_english || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-mono text-slate-600">{{ passenger.national_id }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-mono text-slate-600">{{ passenger.passport_number || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3">{{ passenger.phone_number || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3">{{ formatDate(passenger.date_of_birth) }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 text-center">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
                                    @click="removePassenger(passenger.id)"
                                    title="Remove from bus"
                                >
                                    <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                    <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="font-medium text-slate-800">No Passengers</p>
                <p class="mt-1 text-sm text-slate-500">Assign customers to this bus from the buses list</p>
                <Link
                    :href="route('trips.buses.index', trip.id)"
                    class="mt-4 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Buses
                </Link>
            </div>
        </div>
    </main>
</template>
