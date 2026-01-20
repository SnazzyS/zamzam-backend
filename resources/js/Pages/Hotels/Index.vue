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
    if (!confirm('Are you sure you want to delete this hotel?')) {
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
    if (!confirm('Are you sure you want to delete this room?')) {
        return;
    }

    router.delete(route('hotels.rooms.destroy', [room.hotel_id, room.id]), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Hotel Management" />

    <main class="space-y-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Hotel Management</h1>
            <p class="mt-1 text-slate-500">Create hotels and manage rooms</p>
        </div>

        <!-- Create Hotel Form -->
        <section class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-800 mb-5">Create Hotel</h2>
            <form @submit.prevent="submitHotel" class="grid gap-4 md:grid-cols-3">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700">Name</label>
                    <input
                        v-model="hotelForm.name"
                        type="text"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                        required
                    >
                    <p v-if="hotelForm.errors.name" class="text-xs text-red-500 mt-1">{{ hotelForm.errors.name }}</p>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700">Address</label>
                    <input
                        v-model="hotelForm.address"
                        type="text"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                        required
                    >
                    <p v-if="hotelForm.errors.address" class="text-xs text-red-500 mt-1">{{ hotelForm.errors.address }}</p>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700">Phone Number</label>
                    <input
                        v-model="hotelForm.phone_number"
                        type="number"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                        required
                    >
                    <p v-if="hotelForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ hotelForm.errors.phone_number }}</p>
                </div>
                <div class="md:col-span-3 flex justify-end pt-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="hotelForm.processing"
                    >
                        {{ hotelForm.processing ? 'Saving...' : 'Create Hotel' }}
                    </button>
                </div>
            </form>
        </section>

        <!-- Hotels List -->
        <section v-if="hotels.length" class="space-y-5">
            <div
                v-for="hotel in hotels"
                :key="hotel.id"
                class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">{{ hotel.name }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ hotel.address }}</p>
                        <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ hotel.phone_number }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50"
                            @click="openEditHotel(hotel)"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-500 transition-all duration-200 hover:bg-red-50"
                            @click="deleteHotel(hotel.id)"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Rooms Section -->
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-base font-semibold text-slate-800">Rooms</h4>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-slate-900/20 transition-all duration-200 hover:bg-slate-800"
                            @click="openCreateRoom(hotel)"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Room
                        </button>
                    </div>

                    <div v-if="hotel.rooms.length" class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50/50 text-xs font-medium uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">Room</th>
                                    <th class="px-4 py-3 text-left">Bed Count</th>
                                    <th class="px-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr v-for="room in hotel.rooms" :key="room.id" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 font-medium">{{ room.room_number }}</td>
                                    <td class="px-4 py-3">{{ room.bed_count }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3">
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-slate-600 transition hover:text-slate-900"
                                                @click="openEditRoom(room)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-red-500 transition hover:text-red-600"
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
                    <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-8 text-center">
                        <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-sm text-slate-400">No rooms yet</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 py-16 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-base font-medium text-slate-800 mb-1">No Hotels</h3>
            <p class="text-sm text-slate-500">Create a new hotel to get started</p>
        </div>
    </main>

    <!-- Edit Hotel Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showEditHotelModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeEditHotel"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showEditHotelModal" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800">Edit Hotel</h3>
                                    <p class="text-sm text-slate-500 mt-0.5">Update hotel information</p>
                                </div>
                                <button type="button" @click="closeEditHotel" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitEditHotel" class="space-y-5">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Name</label>
                                    <input
                                        v-model="editHotelForm.name"
                                        type="text"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="editHotelForm.errors.name" class="text-xs text-red-500 mt-1">{{ editHotelForm.errors.name }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Address</label>
                                    <input
                                        v-model="editHotelForm.address"
                                        type="text"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="editHotelForm.errors.address" class="text-xs text-red-500 mt-1">{{ editHotelForm.errors.address }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Phone Number</label>
                                    <input
                                        v-model="editHotelForm.phone_number"
                                        type="number"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="editHotelForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ editHotelForm.errors.phone_number }}</p>
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" @click="closeEditHotel" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50">
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="editHotelForm.processing"
                                    >
                                        {{ editHotelForm.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Create Room Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showCreateRoomModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeCreateRoom"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showCreateRoomModal" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800">Create Room</h3>
                                    <p class="text-sm text-slate-500 mt-0.5">Add a new room</p>
                                </div>
                                <button type="button" @click="closeCreateRoom" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitCreateRoom" class="space-y-5">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Room Number</label>
                                    <input
                                        v-model="roomForm.room_number"
                                        type="number"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="roomForm.errors.room_number" class="text-xs text-red-500 mt-1">{{ roomForm.errors.room_number }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Bed Count</label>
                                    <input
                                        v-model="roomForm.bed_count"
                                        type="number"
                                        min="1"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="roomForm.errors.bed_count" class="text-xs text-red-500 mt-1">{{ roomForm.errors.bed_count }}</p>
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" @click="closeCreateRoom" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50">
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="roomForm.processing"
                                    >
                                        {{ roomForm.processing ? 'Saving...' : 'Create Room' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Edit Room Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showEditRoomModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeEditRoom"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showEditRoomModal" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800">Edit Room</h3>
                                    <p class="text-sm text-slate-500 mt-0.5">Update room information</p>
                                </div>
                                <button type="button" @click="closeEditRoom" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitEditRoom" class="space-y-5">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Room Number</label>
                                    <input
                                        v-model="editRoomForm.room_number"
                                        type="number"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="editRoomForm.errors.room_number" class="text-xs text-red-500 mt-1">{{ editRoomForm.errors.room_number }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">Bed Count</label>
                                    <input
                                        v-model="editRoomForm.bed_count"
                                        type="number"
                                        min="1"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="editRoomForm.errors.bed_count" class="text-xs text-red-500 mt-1">{{ editRoomForm.errors.bed_count }}</p>
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" @click="closeEditRoom" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50">
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="editRoomForm.processing"
                                    >
                                        {{ editRoomForm.processing ? 'Saving...' : 'Save Changes' }}
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
