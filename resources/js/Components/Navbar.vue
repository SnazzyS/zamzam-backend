<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';

const page = usePage();
const currentUrl = computed(() => page.url);

const navItems = [
    { name: 'Dashboard', href: '/office/dashboard', icon: 'home' },
    { name: 'Create Trip', action: 'createTrip', icon: 'plus' },
    { name: 'Customers', href: '/office/customers', icon: 'users' },
    { name: 'Hotels', href: '/office/hotels', icon: 'building' },
];

const showCreateModal = ref(false);
const showSettingsModal = ref(false);

const createForm = useForm({
    name: '',
    price: '',
    departure_date: '',
    phone_number: '',
});

const settingsForm = useForm({
    company_address: '',
    company_phone: '',
});

const openSettingsModal = async () => {
    try {
        const response = await fetch(route('settings.index'));
        const data = await response.json();
        settingsForm.company_address = data.company_address || '';
        settingsForm.company_phone = data.company_phone || '';
    } catch (error) {
        console.error('Failed to load settings:', error);
    }
    showSettingsModal.value = true;
};

const closeSettingsModal = () => {
    showSettingsModal.value = false;
    settingsForm.clearErrors();
};

const submitSettings = () => {
    settingsForm.put(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            closeSettingsModal();
        },
    });
};

const isActive = (href) => {
    if (href === '/office/dashboard') {
        return currentUrl.value === '/office/dashboard' || currentUrl.value === '/office';
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
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500">
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
                        class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
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
                            'flex items-center gap-2 rounded-md px-3 py-1.5 text-sm transition',
                            isActive(item.href)
                                ? 'text-blue-600 bg-blue-50'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <!-- Home Icon -->
                        <svg v-if="item.icon === 'home'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <!-- Users Icon -->
                        <svg v-else-if="item.icon === 'users'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <!-- Building Icon -->
                        <svg v-else-if="item.icon === 'building'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </template>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-1">
                <!-- Settings Button -->
                <button
                    type="button"
                    @click="openSettingsModal"
                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                    title="Company Settings"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <button type="button" class="relative flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-blue-500"></span>
                </button>
                <button class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-900 text-white transition hover:bg-slate-800">
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
                    <div class="fixed inset-0 bg-black/50" @click="closeCreateModal"></div>

                    <Transition
                        enter-active-class="duration-150 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="duration-100 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showCreateModal" class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Create Trip</h3>
                                    <p class="text-sm text-slate-500">Add a new trip</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeCreateModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
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
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="createForm.errors.price && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                        required
                                    >
                                    <p v-if="createForm.errors.price" class="text-xs text-red-500 mt-1">{{ createForm.errors.price }}</p>
                                </div>

                                <div>
                                    <label for="create-phone" class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number (Optional)</label>
                                    <input
                                        id="create-phone"
                                        type="text"
                                        v-model="createForm.phone_number"
                                        placeholder="e.g. 7999065"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="createForm.errors.phone_number && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                    >
                                    <p v-if="createForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ createForm.errors.phone_number }}</p>
                                    <p class="text-xs text-slate-500 mt-1">This number will appear on ID cards</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeCreateModal"
                                        class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
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

    <!-- Settings Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showSettingsModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/50" @click="closeSettingsModal"></div>

                    <Transition
                        enter-active-class="duration-150 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="duration-100 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showSettingsModal" class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Company Settings</h3>
                                    <p class="text-sm text-slate-500">Update company information</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeSettingsModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitSettings" class="space-y-4">
                                <div>
                                    <label for="company-address" class="block text-sm font-medium text-slate-700 mb-1.5">Company Address</label>
                                    <textarea
                                        id="company-address"
                                        v-model="settingsForm.company_address"
                                        rows="3"
                                        placeholder="Enter company address"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="settingsForm.errors.company_address && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                    ></textarea>
                                    <p v-if="settingsForm.errors.company_address" class="text-xs text-red-500 mt-1">{{ settingsForm.errors.company_address }}</p>
                                </div>

                                <div>
                                    <label for="company-phone" class="block text-sm font-medium text-slate-700 mb-1.5">Company Phone Number</label>
                                    <input
                                        id="company-phone"
                                        type="text"
                                        v-model="settingsForm.company_phone"
                                        placeholder="e.g. 7999065"
                                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        :class="settingsForm.errors.company_phone && 'border-red-300 focus:border-red-500 focus:ring-red-500/20'"
                                    >
                                    <p v-if="settingsForm.errors.company_phone" class="text-xs text-red-500 mt-1">{{ settingsForm.errors.company_phone }}</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeSettingsModal"
                                        class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                                        :disabled="settingsForm.processing"
                                    >
                                        {{ settingsForm.processing ? 'Saving...' : 'Save Changes' }}
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
