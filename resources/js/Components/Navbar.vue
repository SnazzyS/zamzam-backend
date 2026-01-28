<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';

const page = usePage();
const currentUrl = computed(() => page.url);

const navItems = [
    { name: 'Dashboard', href: '/dashboard', icon: 'home' },
    { name: 'Create Trip', action: 'createTrip', icon: 'plus' },
    { name: 'Hotels', href: '/hotels', icon: 'building' },
    { name: 'Finance', href: '/finance', icon: 'chart' },
];

const showCreateModal = ref(false);

const createForm = useForm({
    name: '',
    price: '',
    departure_date: '',
});

const isActive = (href) => {
    if (href === '/dashboard') {
        return currentUrl.value === '/dashboard' || currentUrl.value === '/';
    }
    return currentUrl.value.startsWith(href);
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.reset();
    createForm.clearErrors();
};

const submitCreate = () => {
    createForm.post(route('trips.store'), {
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

const handleDhivehiInput = (event, form, field) => {
    const mapped = toDhivehi(event.target.value);
    if (mapped !== event.target.value) {
        event.target.value = mapped;
    }
    form[field] = mapped;
};

const handleDhivehiKeydown = (event, form, field) => {
    if (event.isComposing || event.ctrlKey || event.metaKey || event.altKey) {
        return;
    }

    const key = event.key;
    if (key.length !== 1) {
        return;
    }

    const mapped = tkGetChar(key);
    if (mapped === key) {
        return;
    }

    event.preventDefault();
    const input = event.target;
    const start = input.selectionStart ?? 0;
    const end = input.selectionEnd ?? 0;
    const nextValue = `${input.value.slice(0, start)}${mapped}${input.value.slice(end)}`;
    input.value = nextValue;
    const nextPos = start + mapped.length;
    input.setSelectionRange(nextPos, nextPos);
    form[field] = nextValue;
};
</script>

<template>
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-14 max-w-[1400px] items-center justify-between px-6">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-600">
                    <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="font-semibold text-slate-900">Zam Zam</span>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center gap-1">
                <template v-for="item in navItems" :key="item.name">
                    <button
                        v-if="item.action === 'createTrip'"
                        type="button"
                        @click="openCreateModal"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ item.name }}
                    </button>
                    <Link
                        v-else
                        :href="item.href"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                            isActive(item.href)
                                ? 'bg-slate-900 text-white'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                        ]"
                    >
                        <!-- Home Icon -->
                        <svg v-if="item.icon === 'home'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <!-- Building Icon -->
                        <svg v-else-if="item.icon === 'building'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <!-- Chart Icon -->
                        <svg v-else-if="item.icon === 'chart'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </template>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-2">
                <button type="button" class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-violet-500"></span>
                </button>
                <button class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Create Trip Modal -->
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
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeCreateModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showCreateModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Create Trip</h3>
                                    <p class="text-sm text-slate-500">Add a new trip</p>
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
                                    <label for="create-name" class="block text-sm font-medium text-slate-700 mb-1.5">Trip Name</label>
                                    <input
                                        id="create-name"
                                        type="text"
                                        v-model="createForm.name"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="createForm.errors.name && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="createForm.errors.name" class="text-xs text-red-500 mt-1">{{ createForm.errors.name }}</p>
                                </div>

                                <div>
                                    <label for="create-departure-date" class="block text-sm font-medium text-slate-700 mb-1.5">Departure Date</label>
                                    <input
                                        id="create-departure-date"
                                        type="date"
                                        v-model="createForm.departure_date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="createForm.errors.departure_date && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="createForm.errors.departure_date" class="text-xs text-red-500 mt-1">{{ createForm.errors.departure_date }}</p>
                                </div>

                                <div>
                                    <label for="create-price" class="block text-sm font-medium text-slate-700 mb-1.5">Price (MVR)</label>
                                    <input
                                        id="create-price"
                                        type="number"
                                        v-model="createForm.price"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="createForm.errors.price && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="createForm.errors.price" class="text-xs text-red-500 mt-1">{{ createForm.errors.price }}</p>
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
                                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                                        :disabled="createForm.processing"
                                    >
                                        {{ createForm.processing ? 'Creating...' : 'Create Trip' }}
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
