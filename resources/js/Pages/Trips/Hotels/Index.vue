<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    trip: Object,
    hotels: Array,
    attachedHotelIds: Array,
});

const attachedSet = computed(() => new Set((props.attachedHotelIds || []).map((id) => Number(id))));

const isAttached = (hotelId) => attachedSet.value.has(Number(hotelId));

const attachHotel = (hotelId) => {
    router.post(route('trips.hotels.attach', [props.trip.id, hotelId]), {}, { preserveScroll: true });
};

const detachHotel = (hotelId) => {
    router.delete(route('trips.hotels.detach', [props.trip.id, hotelId]), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Trip Hotels" />

    <main class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">Trip Hotels</h1>
            <p class="text-sm text-slate-500">Select which hotels belong to this trip.</p>
        </div>

        <div v-if="hotels.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="hotel in hotels"
                :key="hotel.id"
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ hotel.name }}</h2>
                        <p class="text-sm text-slate-500">{{ hotel.address }}</p>
                        <p class="text-xs text-slate-400">{{ hotel.phone_number }}</p>
                    </div>
                    <span
                        class="rounded-full px-2 py-1 text-xs font-semibold"
                        :class="isAttached(hotel.id) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                    >
                        {{ isAttached(hotel.id) ? 'Attached' : 'Not attached' }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                    <span>{{ hotel.rooms_count }} rooms</span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button
                        v-if="!isAttached(hotel.id)"
                        type="button"
                        class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800"
                        @click="attachHotel(hotel.id)"
                    >
                        އެޅުއްވާލާ
                    </button>
                    <button
                        v-else
                        type="button"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                        @click="detachHotel(hotel.id)"
                    >
                        ނެގުން
                    </button>
                    <Link
                        v-if="isAttached(hotel.id)"
                        :href="route('trips.hotels.rooms.index', [trip.id, hotel.id])"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        ރޫމްތައް
                    </Link>
                </div>
            </div>
        </div>

        <div v-else class="rounded-xl border border-dashed border-slate-200 bg-white p-10 text-center text-sm text-slate-400">
            No hotels created yet. Create hotels from the navbar.
        </div>
    </main>
</template>
