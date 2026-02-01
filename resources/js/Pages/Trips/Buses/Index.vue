<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    trip: Object,
    buses: Array,
    customersWithoutBus: Array,
    allCustomers: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showAssignModal = ref(false);
const editingBus = ref(null);
const assigningBus = ref(null);

const busForm = useForm({
    name: '',
    bus_number: '',
    capacity: '',
    color_code: '#8B5CF6',
});

const assignForm = useForm({
    customer_id: '',
});

const openCreateModal = () => {
    busForm.reset();
    busForm.color_code = '#8B5CF6';
    busForm.clearErrors();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    busForm.reset();
    busForm.clearErrors();
};

const submitCreate = () => {
    busForm.post(route('trips.buses.store', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

const openEditModal = (bus) => {
    editingBus.value = bus;
    busForm.name = bus.name || '';
    busForm.bus_number = bus.bus_number || '';
    busForm.capacity = bus.capacity || '';
    busForm.color_code = bus.color_code || '#8B5CF6';
    busForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingBus.value = null;
    busForm.reset();
    busForm.clearErrors();
};

const submitEdit = () => {
    if (!editingBus.value) return;
    busForm.put(route('trips.buses.update', [props.trip.id, editingBus.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const deleteBus = (bus) => {
    if (!confirm('Are you sure you want to delete this bus? All customer assignments will be removed.')) return;
    router.delete(route('trips.buses.destroy', [props.trip.id, bus.id]), {
        preserveScroll: true,
    });
};

const openAssignModal = (bus) => {
    assigningBus.value = bus;
    assignForm.customer_id = '';
    assignForm.clearErrors();
    showAssignModal.value = true;
};

const closeAssignModal = () => {
    showAssignModal.value = false;
    assigningBus.value = null;
    assignForm.reset();
    assignForm.clearErrors();
};

const submitAssign = () => {
    if (!assigningBus.value) return;
    assignForm.post(route('trips.buses.assign-customer', [props.trip.id, assigningBus.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeAssignModal();
        },
    });
};

const removeCustomer = (bus, customerId) => {
    router.delete(route('trips.buses.remove-customer', [props.trip.id, bus.id]), {
        data: { customer_id: customerId },
        preserveScroll: true,
    });
};

const getPassengersForBus = (busId) => {
    return props.allCustomers.filter(c => c.bus_id === busId);
};

const availableCustomersForAssignment = computed(() => {
    return props.customersWithoutBus || [];
});

const getCapacityStatus = (bus) => {
    const count = bus.customers_count || 0;
    const capacity = bus.capacity || 0;
    if (capacity === 0) return 'neutral';
    const ratio = count / capacity;
    if (ratio >= 1) return 'full';
    if (ratio >= 0.8) return 'almost';
    return 'available';
};
</script>

<template>
    <Head title="Trip Buses" />

    <main class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Buses</h1>
                <p class="mt-1 text-slate-500">Manage buses and passenger assignments</p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-600"
                @click="openCreateModal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Bus
            </button>
        </div>

        <!-- Buses Grid -->
        <div v-if="buses.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="bus in buses"
                :key="bus.id"
                class="group relative rounded-lg border border-slate-200/60 bg-white shadow-sm transition-all duration-200 hover:shadow-md hover:border-slate-300 overflow-hidden"
            >
                <!-- Color Bar -->
                <div
                    class="h-2 w-full"
                    :style="{ backgroundColor: bus.color_code || '#8B5CF6' }"
                ></div>

                <div class="p-5">
                    <!-- Customer Count Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium"
                            :class="{
                                'bg-red-50 text-red-700': getCapacityStatus(bus) === 'full',
                                'bg-amber-50 text-amber-700': getCapacityStatus(bus) === 'almost',
                                'bg-emerald-50 text-emerald-700': getCapacityStatus(bus) === 'available',
                                'bg-slate-50 text-slate-700': getCapacityStatus(bus) === 'neutral',
                            }"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ bus.customers_count || 0 }} / {{ bus.capacity || '∞' }}
                        </span>
                        <span class="text-xs font-mono text-slate-500">Bus #{{ bus.bus_number }}</span>
                    </div>

                    <h2 class="text-lg font-semibold text-slate-800">{{ bus.name }}</h2>

                    <!-- Bus Info -->
                    <div class="mt-3 flex items-center gap-4 text-sm text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Capacity: {{ bus.capacity }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="h-4 w-4 rounded-full border border-slate-200"
                                :style="{ backgroundColor: bus.color_code || '#8B5CF6' }"
                            ></div>
                            <span class="font-mono text-xs">{{ bus.color_code || '#8B5CF6' }}</span>
                        </div>
                    </div>

                    <!-- Passengers List -->
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-slate-700">Passengers</h3>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-100"
                                @click="openAssignModal(bus)"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Add
                            </button>
                        </div>
                        <div v-if="getPassengersForBus(bus.id).length" class="space-y-1.5 max-h-48 overflow-y-auto">
                            <div
                                v-for="passenger in getPassengersForBus(bus.id)"
                                :key="passenger.id"
                                class="flex items-center justify-between rounded-lg bg-slate-50 px-2.5 py-2 text-xs"
                            >
                                <div class="flex items-center gap-2 min-w-0" dir="rtl" lang="dv">
                                    <span v-if="passenger.umrah_id" class="shrink-0 rounded bg-blue-100 px-1.5 py-0.5 font-mono text-blue-700">{{ passenger.umrah_id }}</span>
                                    <span class="truncate font-medium text-slate-700">{{ passenger.name }}</span>
                                </div>
                                <button
                                    type="button"
                                    class="shrink-0 ml-2 rounded p-1 text-slate-400 transition hover:bg-red-100 hover:text-red-600"
                                    @click="removeCustomer(bus, passenger.id)"
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
                <div class="px-5 pb-5">
                    <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                        <Link
                            :href="route('trips.buses.show', [trip.id, bus.id])"
                            class="flex-1 rounded-md bg-blue-500 px-4 py-2.5 text-center text-sm font-medium text-white transition-all duration-200 hover:bg-blue-600"
                        >
                            Bus Details
                        </Link>
                        <button
                            type="button"
                            class="rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50"
                            @click="openEditModal(bus)"
                            title="Edit Bus"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.121 2.121 0 00-3-3L5 17v3z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="rounded-md border border-red-200 bg-white px-3 py-2.5 text-sm font-medium text-red-600 transition-all duration-200 hover:bg-red-50"
                            @click="deleteBus(bus)"
                            title="Delete Bus"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50/50 py-16 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-lg bg-slate-100">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <h3 class="text-base font-medium text-slate-800 mb-1">No Buses</h3>
            <p class="text-sm text-slate-500">Create a bus to start assigning passengers</p>
            <button
                type="button"
                class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-600"
                @click="openCreateModal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Bus
            </button>
        </div>
    </main>

    <!-- Create Bus Modal -->
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
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showCreateModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Add New Bus</h3>
                                    <p class="text-sm text-slate-500">Create a new bus for this trip</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeCreateModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitCreate" class="space-y-4">
                                <div>
                                    <label for="create-name" class="block text-sm font-medium text-slate-700 mb-1.5">Bus Name *</label>
                                    <input
                                        id="create-name"
                                        type="text"
                                        v-model="busForm.name"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="busForm.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        placeholder="e.g., Bus A"
                                        required
                                    >
                                    <p v-if="busForm.errors.name" class="text-xs text-red-500 mt-1">{{ busForm.errors.name }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="create-bus-number" class="block text-sm font-medium text-slate-700 mb-1.5">Bus Number *</label>
                                        <input
                                            id="create-bus-number"
                                            type="number"
                                            v-model="busForm.bus_number"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="busForm.errors.bus_number && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                            placeholder="1"
                                            required
                                        >
                                        <p v-if="busForm.errors.bus_number" class="text-xs text-red-500 mt-1">{{ busForm.errors.bus_number }}</p>
                                    </div>
                                    <div>
                                        <label for="create-capacity" class="block text-sm font-medium text-slate-700 mb-1.5">Capacity *</label>
                                        <input
                                            id="create-capacity"
                                            type="number"
                                            v-model="busForm.capacity"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="busForm.errors.capacity && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                            placeholder="45"
                                            required
                                        >
                                        <p v-if="busForm.errors.capacity" class="text-xs text-red-500 mt-1">{{ busForm.errors.capacity }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="create-color" class="block text-sm font-medium text-slate-700 mb-1.5">Color Code</label>
                                    <div class="flex items-center gap-3">
                                        <input
                                            id="create-color"
                                            type="color"
                                            v-model="busForm.color_code"
                                            class="h-10 w-20 cursor-pointer rounded-lg border border-slate-200 p-1"
                                        >
                                        <input
                                            type="text"
                                            v-model="busForm.color_code"
                                            class="flex-1 rounded-lg border border-slate-200 px-3 py-2 font-mono text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="#8B5CF6"
                                        >
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeCreateModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="busForm.processing"
                                    >
                                        {{ busForm.processing ? 'Creating...' : 'Create Bus' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Edit Bus Modal -->
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
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showEditModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Edit Bus</h3>
                                    <p class="text-sm text-slate-500">Update bus details</p>
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
                                    <label for="edit-name" class="block text-sm font-medium text-slate-700 mb-1.5">Bus Name *</label>
                                    <input
                                        id="edit-name"
                                        type="text"
                                        v-model="busForm.name"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="busForm.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="busForm.errors.name" class="text-xs text-red-500 mt-1">{{ busForm.errors.name }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit-bus-number" class="block text-sm font-medium text-slate-700 mb-1.5">Bus Number *</label>
                                        <input
                                            id="edit-bus-number"
                                            type="number"
                                            v-model="busForm.bus_number"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="busForm.errors.bus_number && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                            required
                                        >
                                        <p v-if="busForm.errors.bus_number" class="text-xs text-red-500 mt-1">{{ busForm.errors.bus_number }}</p>
                                    </div>
                                    <div>
                                        <label for="edit-capacity" class="block text-sm font-medium text-slate-700 mb-1.5">Capacity *</label>
                                        <input
                                            id="edit-capacity"
                                            type="number"
                                            v-model="busForm.capacity"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            :class="busForm.errors.capacity && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                            required
                                        >
                                        <p v-if="busForm.errors.capacity" class="text-xs text-red-500 mt-1">{{ busForm.errors.capacity }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="edit-color" class="block text-sm font-medium text-slate-700 mb-1.5">Color Code</label>
                                    <div class="flex items-center gap-3">
                                        <input
                                            id="edit-color"
                                            type="color"
                                            v-model="busForm.color_code"
                                            class="h-10 w-20 cursor-pointer rounded-lg border border-slate-200 p-1"
                                        >
                                        <input
                                            type="text"
                                            v-model="busForm.color_code"
                                            class="flex-1 rounded-lg border border-slate-200 px-3 py-2 font-mono text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="#8B5CF6"
                                        >
                                    </div>
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
                                        class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="busForm.processing"
                                    >
                                        {{ busForm.processing ? 'Saving...' : 'Save Changes' }}
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
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showAssignModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Assign Customer</h3>
                                    <p class="text-sm text-slate-500">Add a passenger to {{ assigningBus?.name }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeAssignModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
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
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                                        All customers in this trip are already assigned to buses.
                                    </p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeAssignModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="assignForm.processing || availableCustomersForAssignment.length === 0"
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
