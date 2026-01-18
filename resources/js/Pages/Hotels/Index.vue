<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    hotels: Array,
});

const hotelForm = useForm({
    name: '',
    address: '',
    phone_number: '',
});

const submitHotel = () => {
    hotelForm.post(route('hotels.store'), {
        preserveScroll: true,
        onSuccess: () => {
            hotelForm.reset();
        },
    });
};

const showEditHotelModal = ref(false);
const editingHotel = ref(null);
const editHotelForm = useForm({
    name: '',
    address: '',
    phone_number: '',
});

const openEditHotel = (hotel) => {
    editingHotel.value = hotel;
    editHotelForm.name = hotel.name;
    editHotelForm.address = hotel.address;
    editHotelForm.phone_number = hotel.phone_number;
    showEditHotelModal.value = true;
};

const closeEditHotel = () => {
    showEditHotelModal.value = false;
    editingHotel.value = null;
    editHotelForm.reset();
    editHotelForm.clearErrors();
};

const submitEditHotel = () => {
    if (!editingHotel.value) {
        return;
    }

    editHotelForm.put(route('hotels.update', editingHotel.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditHotel();
        },
    });
};

const deleteHotel = (hotelId) => {
    if (!confirm('Delete this hotel?')) {
        return;
    }

    router.delete(route('hotels.destroy', hotelId), {
        preserveScroll: true,
    });
};

const showCreateRoomModal = ref(false);
const roomHotel = ref(null);
const roomForm = useForm({
    room_number: '',
    bed_count: '',
});

const openCreateRoom = (hotel) => {
    roomHotel.value = hotel;
    roomForm.reset();
    roomForm.clearErrors();
    showCreateRoomModal.value = true;
};

const closeCreateRoom = () => {
    showCreateRoomModal.value = false;
    roomHotel.value = null;
    roomForm.reset();
    roomForm.clearErrors();
};

const submitCreateRoom = () => {
    if (!roomHotel.value) {
        return;
    }

    roomForm.post(route('hotels.rooms.store', roomHotel.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateRoom();
        },
    });
};

const showEditRoomModal = ref(false);
const editingRoom = ref(null);
const editRoomForm = useForm({
    room_number: '',
    bed_count: '',
});

const openEditRoom = (room) => {
    editingRoom.value = room;
    editRoomForm.room_number = room.room_number;
    editRoomForm.bed_count = room.bed_count;
    showEditRoomModal.value = true;
};

const closeEditRoom = () => {
    showEditRoomModal.value = false;
    editingRoom.value = null;
    editRoomForm.reset();
    editRoomForm.clearErrors();
};

const submitEditRoom = () => {
    if (!editingRoom.value) {
        return;
    }

    editRoomForm.put(route('hotels.rooms.update', [editingRoom.value.hotel_id, editingRoom.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditRoom();
        },
    });
};

const deleteRoom = (room) => {
    if (!confirm('Delete this room?')) {
        return;
    }

    router.delete(route('hotels.rooms.destroy', [room.hotel_id, room.id]), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="ހޮޓާ ހެދުން" />

    <main class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">ހޮޓާ ހެދުން</h1>
            <p class="text-sm text-slate-500">Create hotels and manage rooms.</p>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Create Hotel</h2>
            <form @submit.prevent="submitHotel" class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="space-y-1 md:col-span-1">
                    <label class="text-xs font-medium text-slate-500">Name</label>
                    <input
                        v-model="hotelForm.name"
                        type="text"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="hotelForm.errors.name" class="text-xs text-red-500">{{ hotelForm.errors.name }}</p>
                </div>
                <div class="space-y-1 md:col-span-1">
                    <label class="text-xs font-medium text-slate-500">Address</label>
                    <input
                        v-model="hotelForm.address"
                        type="text"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="hotelForm.errors.address" class="text-xs text-red-500">{{ hotelForm.errors.address }}</p>
                </div>
                <div class="space-y-1 md:col-span-1">
                    <label class="text-xs font-medium text-slate-500">Phone</label>
                    <input
                        v-model="hotelForm.phone_number"
                        type="number"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="hotelForm.errors.phone_number" class="text-xs text-red-500">{{ hotelForm.errors.phone_number }}</p>
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="hotelForm.processing"
                    >
                        {{ hotelForm.processing ? 'Saving...' : 'Create Hotel' }}
                    </button>
                </div>
            </form>
        </section>

        <section v-if="hotels.length" class="space-y-4">
            <div
                v-for="hotel in hotels"
                :key="hotel.id"
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ hotel.name }}</h3>
                        <p class="text-sm text-slate-500">{{ hotel.address }}</p>
                        <p class="text-xs text-slate-400">{{ hotel.phone_number }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                            @click="openEditHotel(hotel)"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-500 transition hover:bg-red-50"
                            @click="deleteHotel(hotel.id)"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700">Rooms</h4>
                        <button
                            type="button"
                            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800"
                            @click="openCreateRoom(hotel)"
                        >
                            Add Room
                        </button>
                    </div>

                    <div v-if="hotel.rooms.length" class="mt-3 overflow-hidden rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-3 py-2 text-right font-medium">Room</th>
                                    <th class="px-3 py-2 text-right font-medium">Beds</th>
                                    <th class="px-3 py-2 text-right font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr v-for="room in hotel.rooms" :key="room.id">
                                    <td class="px-3 py-2 text-right">{{ room.room_number }}</td>
                                    <td class="px-3 py-2 text-right">{{ room.bed_count }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="text-xs font-semibold text-slate-500 transition hover:text-slate-700"
                                                @click="openEditRoom(room)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="text-xs font-semibold text-red-500 transition hover:text-red-600"
                                                @click="deleteRoom(room)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="mt-3 text-xs text-slate-400">No rooms yet.</p>
                </div>
            </div>
        </section>

        <div v-else class="rounded-xl border border-dashed border-slate-200 bg-white p-10 text-center text-sm text-slate-400">
            No hotels created yet.
        </div>
    </main>

    <div
        v-if="showEditHotelModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeEditHotel"
    >
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xl font-semibold text-slate-900">Edit Hotel</h3>
                <button type="button" @click="closeEditHotel" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitEditHotel" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Name</label>
                    <input
                        v-model="editHotelForm.name"
                        type="text"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="editHotelForm.errors.name" class="text-xs text-red-500">{{ editHotelForm.errors.name }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Address</label>
                    <input
                        v-model="editHotelForm.address"
                        type="text"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="editHotelForm.errors.address" class="text-xs text-red-500">{{ editHotelForm.errors.address }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Phone</label>
                    <input
                        v-model="editHotelForm.phone_number"
                        type="number"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="editHotelForm.errors.phone_number" class="text-xs text-red-500">{{ editHotelForm.errors.phone_number }}</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="closeEditHotel" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-600">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="editHotelForm.processing"
                    >
                        {{ editHotelForm.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div
        v-if="showCreateRoomModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeCreateRoom"
    >
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xl font-semibold text-slate-900">Add Room</h3>
                <button type="button" @click="closeCreateRoom" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitCreateRoom" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Room Number</label>
                    <input
                        v-model="roomForm.room_number"
                        type="number"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="roomForm.errors.room_number" class="text-xs text-red-500">{{ roomForm.errors.room_number }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Bed Count</label>
                    <input
                        v-model="roomForm.bed_count"
                        type="number"
                        min="1"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="roomForm.errors.bed_count" class="text-xs text-red-500">{{ roomForm.errors.bed_count }}</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="closeCreateRoom" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-600">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="roomForm.processing"
                    >
                        {{ roomForm.processing ? 'Saving...' : 'Add Room' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div
        v-if="showEditRoomModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeEditRoom"
    >
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xl font-semibold text-slate-900">Edit Room</h3>
                <button type="button" @click="closeEditRoom" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitEditRoom" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Room Number</label>
                    <input
                        v-model="editRoomForm.room_number"
                        type="number"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="editRoomForm.errors.room_number" class="text-xs text-red-500">{{ editRoomForm.errors.room_number }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Bed Count</label>
                    <input
                        v-model="editRoomForm.bed_count"
                        type="number"
                        min="1"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="editRoomForm.errors.bed_count" class="text-xs text-red-500">{{ editRoomForm.errors.bed_count }}</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="closeEditRoom" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-600">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="editRoomForm.processing"
                    >
                        {{ editRoomForm.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
