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
        { name: 'ރަގިސްޓަރ', action: 'register' },
        { name: 'ހޮޓާ', href: route('trips.hotels.index', tripId.value) },
        { name: 'ފްލައިޓް', href: route('trips.flights.index', tripId.value) },
        { name: 'ބަސް', href: route('trips.buses.index', tripId.value) },
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
    <div class="min-h-screen bg-slate-100" dir="rtl">
        <Navbar />
        <div class="mx-auto flex max-w-[1400px] flex-col gap-6 p-6 lg:flex-row">
            <aside v-if="hasTripSidebar" class="w-full lg:w-56">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <nav class="space-y-2">
                        <template v-for="item in sidebarItems" :key="item.name">
                            <button
                                v-if="item.action === 'register'"
                                type="button"
                                class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                @click="openRegisterModal"
                            >
                                {{ item.name }}
                            </button>
                            <Link
                                v-else
                                :href="item.href"
                                :class="[
                                    'flex items-center rounded-lg px-3 py-2 text-sm font-medium transition',
                                    isActive(item.href)
                                        ? 'bg-slate-900 text-white'
                                        : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700',
                                ]"
                            >
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

    <div
        v-if="showRegisterModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeRegisterModal"
    >
        <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xl font-semibold text-slate-900">ކަސްޓަމަރު ރަގިސްޓަރ</h3>
                <button type="button" @click="closeRegisterModal" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitRegister" class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">name</label>
                    <input
                        :value="registerForm.name"
                        type="text"
                        dir="rtl"
                        lang="dv"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-right text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        @keydown="handleDhivehiKeydown($event, registerForm, 'name')"
                        @input="handleDhivehiInput($event, registerForm, 'name')"
                        required
                    >
                    <p v-if="registerForm.errors.name" class="text-xs text-red-500">{{ registerForm.errors.name }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500" dir="ltr">national_id</label>
                    <input
                        v-model="registerForm.national_id"
                        type="text"
                        dir="ltr"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        placeholder="A123456"
                        required
                    >
                    <p v-if="registerForm.errors.national_id" class="text-xs text-red-500">{{ registerForm.errors.national_id }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">date_of_birth</label>
                    <input
                        v-model="registerForm.date_of_birth"
                        type="date"
                        dir="ltr"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="registerForm.errors.date_of_birth" class="text-xs text-red-500">{{ registerForm.errors.date_of_birth }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">island</label>
                    <input
                        :value="registerForm.island"
                        type="text"
                        dir="rtl"
                        lang="dv"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-right text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        @keydown="handleDhivehiKeydown($event, registerForm, 'island')"
                        @input="handleDhivehiInput($event, registerForm, 'island')"
                        required
                    >
                    <p v-if="registerForm.errors.island" class="text-xs text-red-500">{{ registerForm.errors.island }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">phone_number</label>
                    <input
                        v-model="registerForm.phone_number"
                        type="number"
                        dir="ltr"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-left text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                    <p v-if="registerForm.errors.phone_number" class="text-xs text-red-500">{{ registerForm.errors.phone_number }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">address</label>
                    <input
                        :value="registerForm.address"
                        type="text"
                        dir="rtl"
                        lang="dv"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-right text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        @keydown="handleDhivehiKeydown($event, registerForm, 'address')"
                        @input="handleDhivehiInput($event, registerForm, 'address')"
                        required
                    >
                    <p v-if="registerForm.errors.address" class="text-xs text-red-500">{{ registerForm.errors.address }}</p>
                </div>
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs font-medium text-slate-500">gender</label>
                    <select
                        v-model="registerForm.gender"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        required
                    >
                        <option value="" disabled>Choose</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    <p v-if="registerForm.errors.gender" class="text-xs text-red-500">{{ registerForm.errors.gender }}</p>
                </div>

                <div class="md:col-span-2 flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeRegisterModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-600">
                        ކެންސަލް
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="registerForm.processing"
                    >
                        {{ registerForm.processing ? 'ރަގިސްޓަރ ކުރަނީ...' : 'ރަގިސްޓަރ' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
