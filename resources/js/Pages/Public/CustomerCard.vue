<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    customer: Object,
    flight: Object,
    bus: Object,
    room: Object,
    primaryHotel: Object,
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
    return timeString.slice(0, 5);
};
</script>

<template>
    <Head :title="`${customer.name_in_english || customer.name} - Trip Details`" />

    <div class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 py-8 px-4">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="text-center mb-6">
                <img
                    src="/images/logo.png"
                    alt="Zam Zam H&U Travel"
                    class="w-20 h-20 mx-auto mb-4 object-contain"
                >
                <h1 class="text-2xl font-bold text-slate-800">Zam Zam H&U Travel</h1>
                <p class="text-slate-500 mt-1">{{ trip.name }}</p>
            </div>

            <!-- Customer Info Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
                <div class="bg-slate-600 px-6 py-4">
                    <div class="text-slate-300 text-sm font-medium">Umrah ID</div>
                    <div class="text-white text-3xl font-bold">{{ customer.umrah_id || '-' }}</div>
                </div>
                <div class="px-6 py-5">
                    <div class="text-xl font-semibold text-slate-800">{{ customer.name_in_english || customer.name }}</div>
                    <div class="text-slate-500 mt-1">{{ customer.name }}</div>
                    <div class="mt-4">
                        <div class="text-xs text-slate-400 uppercase tracking-wide">Passport</div>
                        <div class="text-slate-700 font-medium">{{ customer.passport_number || '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Flight Info -->
            <div v-if="flight" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Flight</div>
                            <div class="text-slate-800 font-semibold">{{ flight.name }}</div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Departure</div>
                            <div class="text-slate-700 font-medium">{{ formatDate(flight.departure_date) }}</div>
                            <div v-if="flight.departure_time" class="text-slate-500 text-sm">{{ formatTime(flight.departure_time) }}</div>
                        </div>
                        <div v-if="flight.arrival_date">
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Arrival</div>
                            <div class="text-slate-700 font-medium">{{ formatDate(flight.arrival_date) }}</div>
                            <div v-if="flight.arrival_time" class="text-slate-500 text-sm">{{ formatTime(flight.arrival_time) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hotel & Room Info -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Hotel</div>
                            <div class="text-slate-800 font-semibold">{{ room?.hotel_name || primaryHotel?.name || '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div v-if="room" class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-3">
                        <div class="text-xs text-slate-400 uppercase tracking-wide">Room Number</div>
                        <div class="text-2xl font-bold text-slate-800">{{ room.room_number }}</div>
                    </div>
                    <div v-else class="text-slate-400 text-sm text-center py-2">Room not assigned yet</div>
                </div>
            </div>

            <!-- Bus Info -->
            <div v-if="bus" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
                <div class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 4h8m-6 4h4M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Bus</div>
                            <div class="text-slate-800 font-semibold">{{ bus.name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Number</div>
                            <div class="text-slate-800 font-semibold">#{{ bus.bus_number }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visa -->
            <div v-if="customer.visa_path" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
                <div class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs text-slate-400 uppercase tracking-wide">Visa Document</div>
                            <div class="text-slate-800 font-semibold">PDF Available</div>
                        </div>
                        <a
                            :href="customer.visa_path"
                            target="_blank"
                            class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-red-700 transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            View
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-slate-400 text-sm mt-6">
                <p>Zam Zam Hajj & Umrah Travel</p>
            </div>
        </div>
    </div>
</template>
