<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    trip: Object,
    hotels: Array,
    attachedHotelIds: Array,
    primaryHotelId: Number,
});

const attachedSet = computed(() => new Set((props.attachedHotelIds || []).map((id) => Number(id))));

const isAttached = (hotelId) => attachedSet.value.has(Number(hotelId));

const isPrimary = (hotelId) => Number(props.primaryHotelId) === Number(hotelId);

const attachHotel = (hotelId) => {
    router.post(route('trips.hotels.attach', [props.trip.id, hotelId]), {}, { preserveScroll: true });
};

const detachHotel = (hotelId) => {
    router.delete(route('trips.hotels.detach', [props.trip.id, hotelId]), {
        preserveScroll: true,
    });
};

const setPrimaryHotel = (hotelId) => {
    router.post(route('trips.hotels.set-primary', [props.trip.id, hotelId]), {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Trip Hotels" />

    <main class="space-y-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Trip Hotels</h1>
            <p class="mt-1 text-slate-500">Select hotels for this trip</p>
        </div>

        <!-- Hotels Grid -->
        <div v-if="hotels.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="hotel in hotels"
                :key="hotel.id"
                class="group relative bg-white p-4 rounded-lg transition hover:bg-slate-50"
            >
                <!-- Attached Badge -->
                <div class="absolute left-4 top-4 flex items-center gap-2">
                    <span
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-medium',
                            isAttached(hotel.id)
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-slate-100 text-slate-500',
                        ]"
                    >
                        <span v-if="isAttached(hotel.id)" class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        {{ isAttached(hotel.id) ? 'Attached' : 'Not attached' }}
                    </span>
                    <span
                        v-if="isPrimary(hotel.id)"
                        class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700"
                    >
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Primary
                    </span>
                </div>

                <div class="pt-8">
                    <h2 class="text-lg font-semibold text-slate-800">{{ hotel.name }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ hotel.address }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ hotel.phone_number }}</p>
                </div>

                <div class="mt-3 flex items-center gap-2 text-sm text-slate-500">
                    <div class="flex h-7 w-7 items-center justify-center rounded-md bg-slate-100">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span>{{ hotel.rooms_count }} rooms</span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <button
                        v-if="!isAttached(hotel.id)"
                        type="button"
                        class="flex-1 rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600"
                        @click="attachHotel(hotel.id)"
                    >
                        Attach
                    </button>
                    <template v-else>
                        <button
                            type="button"
                            class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                            @click="detachHotel(hotel.id)"
                        >
                            Detach
                        </button>
                        <button
                            v-if="!isPrimary(hotel.id)"
                            type="button"
                            class="rounded-md bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100"
                            @click="setPrimaryHotel(hotel.id)"
                        >
                            Set Primary
                        </button>
                        <Link
                            :href="route('trips.hotels.rooms.index', [trip.id, hotel.id])"
                            class="flex-1 rounded-md border border-slate-200 bg-white px-4 py-2 text-center text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                        >
                            Rooms
                        </Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center rounded-lg bg-slate-50 py-12 text-center">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-base font-medium text-slate-800 mb-1">No Hotels</h3>
            <p class="text-sm text-slate-500">Create a new hotel from the menu</p>
        </div>
    </main>
</template>
