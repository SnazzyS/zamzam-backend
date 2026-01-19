<script setup>
import Navbar from '@/Components/Navbar.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';

const page = usePage();
const tripId = computed(() => page.props?.trip?.id);
const hasTripSidebar = computed(() => Boolean(tripId.value));

const sidebarItems = computed(() => {
    if (!tripId.value) {
        return [];
    }

    return [
        { name: 'ރަގިސްޓަރ', action: 'register', icon: 'user-plus' },
        { name: 'ހޮޓާ', href: route('trips.hotels.index', tripId.value), icon: 'building' },
        { name: 'ފްލައިޓް', href: route('trips.flights.index', tripId.value), icon: 'plane' },
        { name: 'ބަސް', href: route('trips.buses.index', tripId.value), icon: 'bus' },
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
});

const openRegisterModal = () => {
    showRegisterModal.value = true;
};

const closeRegisterModal = () => {
    showRegisterModal.value = false;
    registerForm.reset();
    registerForm.clearErrors();
};

const submitRegister = () => {
    if (!tripId.value) {
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
    <div class="min-h-screen bg-slate-50" dir="rtl">
        <Navbar />
        <div class="mx-auto flex max-w-[1400px] flex-col gap-6 p-6 lg:flex-row">
            <aside v-if="hasTripSidebar" class="w-full lg:w-60">
                <div class="sticky top-24 rounded-2xl border border-slate-200/60 bg-white p-2 shadow-sm">
                    <nav class="space-y-1">
                        <template v-for="item in sidebarItems" :key="item.name">
                            <button
                                v-if="item.action === 'register'"
                                type="button"
                                class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-100 hover:text-slate-900"
                                @click="openRegisterModal"
                            >
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
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
                                    'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200',
                                    isActive(item.href)
                                        ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20'
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                                ]"
                            >
                                <div :class="[
                                    'flex h-8 w-8 items-center justify-center rounded-lg',
                                    isActive(item.href)
                                        ? 'bg-white/20'
                                        : 'bg-slate-100',
                                ]">
                                    <!-- Hotel Icon -->
                                    <svg v-if="item.icon === 'building'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                                </div>
                                {{ item.name }}
                            </Link>
                        </template>
                    </nav>
                </div>
            </aside>

            <main class="min-w-0 flex-1">
                <slot />
            </main>
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
                        <div v-if="showRegisterModal" class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800">ކަސްޓަމަރު ރަގިސްޓަރ</h3>
                                    <p class="text-sm text-slate-500 mt-0.5">އާ ކަސްޓަމަރެއް ދަތުރަށް އެޅުމަށް</p>
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
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">ނަން</label>
                                    <input
                                        :value="registerForm.name"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        @keydown="handleDhivehiKeydown($event, registerForm, 'name')"
                                        @input="handleDhivehiInput($event, registerForm, 'name')"
                                        required
                                    >
                                    <p v-if="registerForm.errors.name" class="text-xs text-red-500 mt-1">{{ registerForm.errors.name }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">ނޭޝަނަލް އައިޑީ</label>
                                    <input
                                        v-model="registerForm.national_id"
                                        type="text"
                                        dir="ltr"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        placeholder="A123456"
                                        required
                                    >
                                    <p v-if="registerForm.errors.national_id" class="text-xs text-red-500 mt-1">{{ registerForm.errors.national_id }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">އުފަން ތާރީޚު</label>
                                    <input
                                        v-model="registerForm.date_of_birth"
                                        type="date"
                                        dir="ltr"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="registerForm.errors.date_of_birth" class="text-xs text-red-500 mt-1">{{ registerForm.errors.date_of_birth }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">ރަށް</label>
                                    <input
                                        :value="registerForm.island"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        @keydown="handleDhivehiKeydown($event, registerForm, 'island')"
                                        @input="handleDhivehiInput($event, registerForm, 'island')"
                                        required
                                    >
                                    <p v-if="registerForm.errors.island" class="text-xs text-red-500 mt-1">{{ registerForm.errors.island }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">ފޯން ނަންބަރު</label>
                                    <input
                                        v-model="registerForm.phone_number"
                                        type="number"
                                        dir="ltr"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                    <p v-if="registerForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ registerForm.errors.phone_number }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700">އެޑްރެސް</label>
                                    <input
                                        :value="registerForm.address"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        @keydown="handleDhivehiKeydown($event, registerForm, 'address')"
                                        @input="handleDhivehiInput($event, registerForm, 'address')"
                                        required
                                    >
                                    <p v-if="registerForm.errors.address" class="text-xs text-red-500 mt-1">{{ registerForm.errors.address }}</p>
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">ޖިންސު</label>
                                    <select
                                        v-model="registerForm.gender"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        required
                                    >
                                        <option value="" disabled>ހޮވާ</option>
                                        <option value="Male">ފިރިހެން</option>
                                        <option value="Female">އަންހެން</option>
                                    </select>
                                    <p v-if="registerForm.errors.gender" class="text-xs text-red-500 mt-1">{{ registerForm.errors.gender }}</p>
                                </div>

                                <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4">
                                    <button
                                        type="button"
                                        @click="closeRegisterModal"
                                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50"
                                    >
                                        ކެންސަލް
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="registerForm.processing"
                                    >
                                        {{ registerForm.processing ? 'ރަގިސްޓަރ ކުރަނީ...' : 'ރަގިސްޓަރ' }}
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
