<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    trip: Object,
    hotel: Object,
    rooms: Array,
    availableCustomers: Array,
});

const page = usePage();
const errors = computed(() => page.props?.errors || {});
const availableCustomersList = computed(() => props.availableCustomers || []);

const assigningRoomId = ref(null);
const removingRoomId = ref(null);

const assignCustomer = (roomId, customerId) => {
    if (!customerId) {
        return;
    }

    assigningRoomId.value = roomId;
    router.post(
        route('trips.hotels.rooms.assign-customer', [props.trip.id, props.hotel.id, roomId]),
        { customer_id: customerId },
        {
            preserveScroll: true,
            onFinish: () => {
                assigningRoomId.value = null;
            },
        }
    );
};

const removeCustomer = (roomId, customerId) => {
    removingRoomId.value = roomId;
    router.delete(route('trips.hotels.rooms.remove-customer', [props.trip.id, props.hotel.id, roomId]), {
        data: { customer_id: customerId },
        preserveScroll: true,
        onFinish: () => {
            removingRoomId.value = null;
        },
    });
};
</script>

<template>
    <Head title="Hotel Rooms" />

    <main class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">{{ hotel?.name || 'Hotel Rooms' }}</h1>
            <p class="text-sm text-slate-500">Assign trip customers to rooms in this hotel</p>
        </div>

        <div
            v-if="errors.assignCustomer"
            class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"
        >
            {{ errors.assignCustomer }}
        </div>

        <div v-if="rooms.length" class="space-y-4">
            <div
                v-for="room in rooms"
                :key="room.id"
                class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Room {{ room.room_number }}</h2>
                        <p class="text-xs text-slate-500">Bed count: {{ room.bed_count }}</p>
                    </div>
                    <div class="text-xs text-slate-500">
                        {{ room.customers.length }} / {{ room.bed_count }} occupied
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <select
                            class="min-w-[220px] rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                            :disabled="room.customers.length >= room.bed_count || availableCustomersList.length === 0 || assigningRoomId === room.id"
                            @change="assignCustomer(room.id, $event.target.value)"
                        >
                            <option value="" selected disabled>
                                {{ room.customers.length >= room.bed_count ? 'Room full' : 'Assign customer' }}
                            </option>
                            <option v-for="customer in availableCustomersList" :key="customer.id" :value="customer.id">
                                {{ customer.name }} ({{ customer.national_id }})
                            </option>
                        </select>
                    </div>

                    <div class="mt-4 overflow-hidden rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-2 text-left">Name</th>
                                    <th class="px-3 py-2 text-left">National ID</th>
                                    <th class="px-3 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr v-for="customer in room.customers" :key="customer.id">
                                    <td class="px-3 py-2" dir="rtl">{{ customer.name }}</td>
                                    <td class="px-3 py-2">{{ customer.national_id }}</td>
                                    <td class="px-3 py-2">
                                        <button
                                            type="button"
                                            class="text-xs font-semibold text-slate-600 transition hover:text-slate-900 disabled:opacity-50"
                                            :disabled="removingRoomId === room.id"
                                            @click="removeCustomer(room.id, customer.id)"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="room.customers.length === 0">
                                    <td class="px-3 py-4 text-center text-slate-400" colspan="3">No customers assigned</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-10 text-center text-sm text-slate-400">
            No rooms for this trip
        </div>
    </main>
</template>
