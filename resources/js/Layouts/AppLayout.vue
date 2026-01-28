<script setup>
import Navbar from '@/Components/Navbar.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';
import { searchIslands, getIslandOptions } from '../utils/maldivesIslands';
import axios from 'axios';

const page = usePage();
const tripId = computed(() => page.props?.trip?.id);
const hasTripSidebar = computed(() => Boolean(tripId.value));

const sidebarItems = computed(() => {
    if (!tripId.value) {
        return [];
    }

    return [
        { name: 'Home', href: route('trips.show', tripId.value), icon: 'home' },
        { name: 'Register', action: 'register', icon: 'user-plus' },
        { name: 'Groups', href: route('trips.groups.index', tripId.value), icon: 'users' },
        { name: 'Hotels', href: route('trips.hotels.index', tripId.value), icon: 'building' },
        { name: 'Flights', href: route('trips.flights.index', tripId.value), icon: 'plane' },
        { name: 'Buses', href: route('trips.buses.index', tripId.value), icon: 'bus' },
    ];
});

const normalizePath = (href) => {
    try {
        return new URL(href, window.location.origin).pathname;
    } catch (error) {
        return href;
    }
};

const isActive = (href) => normalizePath(href) === page.url;

const showRegisterModal = ref(false);
const registerForm = useForm({
    name: '',
    national_id: '',
    date_of_birth: '',
    island: '',
    phone_number: '',
    address: '',
    gender: '',
    customer_type: 'customer',
});

// Customer search state
const isSearching = ref(false);
const existingCustomer = ref(null);
const alreadyAttached = ref(false);
const nationalIdInput = ref('');
let searchTimeout = null;

// Island dropdown state
const islandSearch = ref('');
const islandDropdownOpen = ref(false);
const filteredIslands = computed(() => searchIslands(islandSearch.value));

const selectIsland = (island) => {
    registerForm.island = island.value;
    islandSearch.value = island.label;
    islandDropdownOpen.value = false;
};

const openIslandDropdown = () => {
    if (!existingCustomer.value) {
        islandDropdownOpen.value = true;
    }
};

const closeIslandDropdown = () => {
    setTimeout(() => {
        islandDropdownOpen.value = false;
    }, 200);
};

const openRegisterModal = () => {
    showRegisterModal.value = true;
    existingCustomer.value = null;
    alreadyAttached.value = false;
    nationalIdInput.value = '';
    islandSearch.value = '';
    islandDropdownOpen.value = false;
};

const closeRegisterModal = () => {
    showRegisterModal.value = false;
    registerForm.reset();
    registerForm.clearErrors();
    existingCustomer.value = null;
    alreadyAttached.value = false;
    nationalIdInput.value = '';
    islandSearch.value = '';
    islandDropdownOpen.value = false;
};

// Search for existing customer when national_id changes
const searchCustomer = async (nationalId) => {
    if (!tripId.value || !nationalId || nationalId.length < 5) {
        existingCustomer.value = null;
        alreadyAttached.value = false;
        return;
    }

    isSearching.value = true;
    try {
        const response = await axios.post(route('trips.customers.search', tripId.value), {
            national_id: nationalId,
        });

        const data = response.data;

        if (data.found) {
            existingCustomer.value = data.customer;
            alreadyAttached.value = data.already_attached;

            // Auto-fill the form with existing customer data
            registerForm.name = data.customer.name || '';
            registerForm.date_of_birth = data.customer.date_of_birth || '';
            registerForm.island = data.customer.island || '';
            registerForm.phone_number = data.customer.phone_number || '';
            registerForm.address = data.customer.address || '';
            registerForm.gender = data.customer.gender || '';

            // Set island search display
            if (data.customer.island) {
                const islandOption = getIslandOptions().find(i => i.value === data.customer.island);
                islandSearch.value = islandOption ? islandOption.label : data.customer.island;
            }
        } else {
            existingCustomer.value = null;
            alreadyAttached.value = false;
        }
    } catch (error) {
        console.error('Error searching customer:', error);
        existingCustomer.value = null;
        alreadyAttached.value = false;
    } finally {
        isSearching.value = false;
    }
};

// Watch national_id with debounce
watch(nationalIdInput, (newValue) => {
    // Sync to form
    registerForm.national_id = newValue;

    // Clear existing customer if input changes after auto-fill
    if (existingCustomer.value && newValue !== existingCustomer.value.national_id) {
        existingCustomer.value = null;
        alreadyAttached.value = false;
        // Clear auto-filled values
        registerForm.name = '';
        registerForm.date_of_birth = '';
        registerForm.island = '';
        registerForm.phone_number = '';
        registerForm.address = '';
        islandSearch.value = '';
        registerForm.gender = '';
    }

    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        searchCustomer(newValue);
    }, 500);
});

const submitRegister = () => {
    if (!tripId.value) {
        return;
    }

    if (alreadyAttached.value) {
        return;
    }

    registerForm.post(route('trips.customers.store', tripId.value), {
        onSuccess: () => {
            closeRegisterModal();
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
    <div class="min-h-screen bg-slate-50">
        <Navbar />
        <div
            class="mx-auto flex max-w-[1400px] flex-col-reverse gap-6 p-6 lg:flex-row"
            :dir="hasTripSidebar ? 'ltr' : undefined"
        >
            <main class="min-w-0 flex-1">
                <slot />
            </main>

            <aside v-if="hasTripSidebar" class="w-full lg:w-56">
                <div class="sticky top-20 rounded-xl border border-slate-200 bg-white p-2">
                    <nav class="space-y-1">
                        <template v-for="item in sidebarItems" :key="item.name">
                            <button
                                v-if="item.action === 'register'"
                                type="button"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                @click="openRegisterModal"
                            >
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                {{ item.name }}
                            </button>
                            <Link
                                v-else
                                :href="item.href"
                                :class="[
                                    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                                    isActive(item.href)
                                        ? 'bg-slate-900 text-white'
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                                ]"
                            >
                                <div :class="[
                                    'flex h-8 w-8 items-center justify-center rounded-lg',
                                    isActive(item.href) ? 'bg-white/20' : 'bg-slate-100',
                                ]">
                                    <!-- Home Icon -->
                                    <svg v-if="item.icon === 'home'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <!-- Hotel Icon -->
                                    <svg v-else-if="item.icon === 'building'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <!-- Plane Icon -->
                                    <svg v-else-if="item.icon === 'plane'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <!-- Bus Icon -->
                                    <svg v-else-if="item.icon === 'bus'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8m-8 4h2m4 0h2M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <!-- Users Icon -->
                                    <svg v-else-if="item.icon === 'users'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                {{ item.name }}
                            </Link>
                        </template>
                    </nav>
                </div>
            </aside>
        </div>
    </div>

    <!-- Register Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showRegisterModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeRegisterModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showRegisterModal" class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Register Customer</h3>
                                    <p class="text-sm text-slate-500">
                                        <span v-if="existingCustomer && !alreadyAttached">Existing customer found - can be attached to this trip</span>
                                        <span v-else-if="alreadyAttached">This customer is already registered for this trip</span>
                                        <span v-else>Register a new customer for this trip</span>
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeRegisterModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitRegister" class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name in Dhivehi</label>
                                    <input
                                        :value="registerForm.name"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="existingCustomer && 'bg-slate-50'"
                                        @keydown="handleDhivehiKeydown($event, registerForm, 'name')"
                                        @input="handleDhivehiInput($event, registerForm, 'name')"
                                        :readonly="!!existingCustomer"
                                        required
                                    >
                                    <p v-if="registerForm.errors.name" class="text-xs text-red-500 mt-1">{{ registerForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">National ID</label>
                                    <div class="relative">
                                        <input
                                            v-model="nationalIdInput"
                                            type="text"
                                            class="w-full rounded-lg border px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2"
                                            :class="[
                                                alreadyAttached
                                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
                                                    : existingCustomer
                                                        ? 'border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500/20'
                                                        : 'border-slate-200 focus:border-violet-500 focus:ring-violet-500/20'
                                            ]"
                                            placeholder="A123456"
                                            required
                                        >
                                        <div v-if="isSearching" class="absolute left-3 top-1/2 -translate-y-1/2">
                                            <svg class="h-4 w-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p v-if="registerForm.errors.national_id" class="text-xs text-red-500 mt-1">{{ registerForm.errors.national_id }}</p>
                                    <p v-else-if="alreadyAttached" class="text-xs text-red-500 mt-1">This customer is already registered for this trip</p>
                                    <p v-else-if="existingCustomer" class="text-xs text-emerald-600 mt-1">Customer found - form auto-filled</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
                                    <input
                                        v-model="registerForm.date_of_birth"
                                        type="date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="existingCustomer && 'bg-slate-50'"
                                        :readonly="!!existingCustomer"
                                        required
                                    >
                                    <p v-if="registerForm.errors.date_of_birth" class="text-xs text-red-500 mt-1">{{ registerForm.errors.date_of_birth }}</p>
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Island</label>
                                    <input
                                        v-model="islandSearch"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="existingCustomer && 'bg-slate-50'"
                                        placeholder="Search island..."
                                        @focus="openIslandDropdown"
                                        @blur="closeIslandDropdown"
                                        :readonly="!!existingCustomer"
                                        autocomplete="off"
                                    >
                                    <!-- Dropdown -->
                                    <div
                                        v-if="islandDropdownOpen && !existingCustomer"
                                        class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
                                    >
                                        <button
                                            v-for="island in filteredIslands"
                                            :key="island.value"
                                            type="button"
                                            class="w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-violet-50 hover:text-violet-700"
                                            @mousedown.prevent="selectIsland(island)"
                                        >
                                            {{ island.label }}
                                        </button>
                                        <div v-if="filteredIslands.length === 0" class="px-3 py-2 text-sm text-slate-400">
                                            No islands found
                                        </div>
                                    </div>
                                    <p v-if="registerForm.errors.island" class="text-xs text-red-500 mt-1">{{ registerForm.errors.island }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number</label>
                                    <input
                                        v-model="registerForm.phone_number"
                                        type="number"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="existingCustomer && 'bg-slate-50'"
                                        :readonly="!!existingCustomer"
                                        required
                                    >
                                    <p v-if="registerForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ registerForm.errors.phone_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                                    <input
                                        :value="registerForm.address"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="existingCustomer && 'bg-slate-50'"
                                        @keydown="handleDhivehiKeydown($event, registerForm, 'address')"
                                        @input="handleDhivehiInput($event, registerForm, 'address')"
                                        :readonly="!!existingCustomer"
                                        required
                                    >
                                    <p v-if="registerForm.errors.address" class="text-xs text-red-500 mt-1">{{ registerForm.errors.address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender</label>
                                    <select
                                        v-model="registerForm.gender"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :class="existingCustomer && 'bg-slate-50'"
                                        :disabled="!!existingCustomer"
                                        required
                                    >
                                        <option value="" disabled>Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <p v-if="registerForm.errors.gender" class="text-xs text-red-500 mt-1">{{ registerForm.errors.gender }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                v-model="registerForm.customer_type"
                                                value="customer"
                                                class="text-violet-600 focus:ring-violet-500"
                                            >
                                            <span class="text-sm text-slate-700">Customer</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                v-model="registerForm.customer_type"
                                                value="staff"
                                                class="text-violet-600 focus:ring-violet-500"
                                            >
                                            <span class="text-sm text-slate-700">Staff</span>
                                        </label>
                                    </div>
                                    <p v-if="registerForm.customer_type === 'staff'" class="text-xs text-orange-600 mt-1">Staff members do not require payment</p>
                                </div>

                                <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeRegisterModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                        :disabled="registerForm.processing || alreadyAttached"
                                    >
                                        <span v-if="registerForm.processing">Registering...</span>
                                        <span v-else-if="alreadyAttached">Already Registered</span>
                                        <span v-else-if="existingCustomer">Attach to Trip</span>
                                        <span v-else>Register</span>
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
