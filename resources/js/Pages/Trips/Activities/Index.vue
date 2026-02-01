<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    trip: Object,
    activityTrips: Array,
    activities: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingActivity = ref(null);

const activityForm = useForm({
    name: '',
    price_usd: '',
    price_mvr: '',
    price_sar: '',
    date: '',
});

const openCreateModal = () => {
    activityForm.reset();
    activityForm.clearErrors();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    activityForm.reset();
    activityForm.clearErrors();
};

const submitCreate = () => {
    activityForm.post(route('trips.activities.store', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

const openEditModal = (activity) => {
    editingActivity.value = activity;
    activityForm.name = activity.name || '';
    activityForm.price_usd = activity.price_usd || '';
    activityForm.price_mvr = activity.price_mvr || '';
    activityForm.price_sar = activity.price_sar || '';
    activityForm.date = activity.date || '';
    activityForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingActivity.value = null;
    activityForm.reset();
    activityForm.clearErrors();
};

const submitEdit = () => {
    if (!editingActivity.value) return;
    activityForm.put(route('trips.activities.update', [props.trip.id, editingActivity.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const deleteActivity = (activity) => {
    if (!confirm('Are you sure you want to delete this activity? All customer assignments will be removed.')) return;
    router.delete(route('trips.activities.destroy', [props.trip.id, activity.id]), {
        preserveScroll: true,
    });
};

const formatCurrency = (value, currency) => {
    if (!value) return '-';
    return `${currency} ${parseFloat(value).toFixed(2)}`;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Optional Trips" />

    <main class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Optional Trips</h1>
                <p class="mt-1 text-slate-500">Manage optional activities for this trip</p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-600"
                @click="openCreateModal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Activity
            </button>
        </div>

        <!-- Activities Grid -->
        <div v-if="activityTrips.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="activity in activityTrips"
                :key="activity.id"
                class="group relative rounded-lg border border-slate-200/60 bg-white shadow-sm transition-all duration-200 hover:shadow-md hover:border-slate-300 overflow-hidden"
            >
                <!-- Color Bar -->
                <div class="h-2 w-full bg-amber-500"></div>

                <div class="p-5">
                    <!-- Stats -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium bg-amber-50 text-amber-700">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ activity.customer_count }} customers
                        </span>
                        <span
                            class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-medium"
                            :class="{
                                'bg-emerald-50 text-emerald-700': activity.paid_count === activity.customer_count && activity.customer_count > 0,
                                'bg-amber-50 text-amber-700': activity.paid_count < activity.customer_count,
                                'bg-slate-50 text-slate-500': activity.customer_count === 0,
                            }"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ activity.paid_count }} paid
                        </span>
                    </div>

                    <h2 class="text-lg font-semibold text-slate-800">{{ activity.name }}</h2>

                    <!-- Date -->
                    <div v-if="activity.date" class="mt-2 flex items-center gap-1.5 text-sm text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatDate(activity.date) }}
                    </div>

                    <!-- Prices -->
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <div class="rounded-lg bg-slate-50 p-2 text-center">
                            <div class="text-xs text-slate-400 uppercase">USD</div>
                            <div class="text-sm font-semibold text-slate-700">{{ activity.price_usd ? `$${activity.price_usd}` : '-' }}</div>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-2 text-center">
                            <div class="text-xs text-slate-400 uppercase">MVR</div>
                            <div class="text-sm font-semibold text-slate-700">{{ activity.price_mvr ? `${activity.price_mvr}` : '-' }}</div>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-2 text-center">
                            <div class="text-xs text-slate-400 uppercase">SAR</div>
                            <div class="text-sm font-semibold text-slate-700">{{ activity.price_sar ? `${activity.price_sar}` : '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-5 pb-5">
                    <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                        <Link
                            :href="route('trips.activities.show', [trip.id, activity.id])"
                            class="flex-1 rounded-md bg-blue-500 px-4 py-2.5 text-center text-sm font-medium text-white transition-all duration-200 hover:bg-blue-600"
                        >
                            Manage
                        </Link>
                        <Link
                            :href="route('trips.activities.passenger-list', [trip.id, activity.id])"
                            class="rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50"
                            title="Print List"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </Link>
                        <button
                            type="button"
                            class="rounded-md border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50"
                            @click="openEditModal(activity)"
                            title="Edit"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.121 2.121 0 00-3-3L5 17v3z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="rounded-md border border-red-200 bg-white px-3 py-2.5 text-sm font-medium text-red-600 transition-all duration-200 hover:bg-red-50"
                            @click="deleteActivity(activity)"
                            title="Delete"
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-base font-medium text-slate-800 mb-1">No Optional Trips</h3>
            <p class="text-sm text-slate-500">Create an activity like "Jeddah Trip" to get started</p>
            <button
                type="button"
                class="mt-4 inline-flex items-center gap-2 rounded-md bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-600"
                @click="openCreateModal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Activity
            </button>
        </div>
    </main>

    <!-- Create Activity Modal -->
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
                        <div v-if="showCreateModal" class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Add Optional Trip</h3>
                                    <p class="text-sm text-slate-500">Create a new activity for customers</p>
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
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Activity Name *</label>
                                    <input
                                        type="text"
                                        v-model="activityForm.name"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="e.g., Jeddah Trip"
                                        required
                                    >
                                    <p v-if="activityForm.errors.name" class="text-xs text-red-500 mt-1">{{ activityForm.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
                                    <input
                                        type="date"
                                        v-model="activityForm.date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    >
                                </div>

                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-slate-700">Prices</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">USD</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                v-model="activityForm.price_usd"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">MVR</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                v-model="activityForm.price_mvr"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">SAR</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                v-model="activityForm.price_sar"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="0.00"
                                            >
                                        </div>
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
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="activityForm.processing"
                                    >
                                        {{ activityForm.processing ? 'Creating...' : 'Create Activity' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Edit Activity Modal -->
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
                        <div v-if="showEditModal" class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Edit Activity</h3>
                                    <p class="text-sm text-slate-500">Update activity details</p>
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
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Activity Name *</label>
                                    <input
                                        type="text"
                                        v-model="activityForm.name"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        required
                                    >
                                    <p v-if="activityForm.errors.name" class="text-xs text-red-500 mt-1">{{ activityForm.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
                                    <input
                                        type="date"
                                        v-model="activityForm.date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    >
                                </div>

                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-slate-700">Prices</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">USD</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                v-model="activityForm.price_usd"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">MVR</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                v-model="activityForm.price_mvr"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">SAR</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                v-model="activityForm.price_sar"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            >
                                        </div>
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
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="activityForm.processing"
                                    >
                                        {{ activityForm.processing ? 'Saving...' : 'Save Changes' }}
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
