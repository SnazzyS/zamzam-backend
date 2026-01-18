<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../utils/lattinMapping';

const page = usePage();
const currentUrl = computed(() => page.url);

const navItems = [
    { name: 'ޑޭޝްބޯޑޫ', href: '/dashboard' },
    { name: 'ދަތުރެއް ހެދުން', action: 'createTrip' },
    { name: 'ހޮޓާ ހެދުން', href: '/hotels' },
    { name: 'މާލީ ހިސާބު', href: '/finance' },
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
    <nav class="sticky top-0 z-50 border-b border-white/10 bg-slate-800">
        <div class="mx-auto flex h-14 max-w-[1400px] items-center gap-2 px-6">
            <!-- Logo -->
            <div class="ml-4 flex items-center">
                <svg class="h-7 w-7 text-indigo-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 17L9 11L13 15L21 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 7L9 13L13 9L21 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                </svg>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center gap-1">
                <template v-for="item in navItems" :key="item.name">
                    <button
                        v-if="item.action === 'createTrip'"
                        type="button"
                        @click="openCreateModal"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-slate-200"
                    >
                        {{ item.name }}
                    </button>
                    <Link
                        v-else
                        :href="item.href"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition',
                            isActive(item.href)
                                ? 'bg-white/10 text-white'
                                : 'text-slate-400 hover:bg-white/5 hover:text-slate-200',
                        ]"
                    >
                        {{ item.name }}
                    </Link>
                </template>
            </div>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Right side - Login/User -->
            <div class="mr-2 flex items-center gap-4">
                <!-- Notification Bell -->
                <button type="button" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/5 hover:text-slate-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <!-- User Avatar - Static Login -->
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 transition hover:scale-105">
                    <span class="text-[0.625rem] font-semibold text-white">ލޮގިން</span>
                </div>
            </div>
        </div>
    </nav>

    <div v-if="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @click.self="closeCreateModal">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xl font-semibold text-slate-900">ދަތުރެއް ހެދުން</h3>
                <button type="button" @click="closeCreateModal" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitCreate" class="space-y-5">
                <div class="space-y-2">
                    <label for="create-name" class="text-sm font-medium text-slate-700 text-right">ދަތުރުގެ ނަން</label>
                    <input
                        id="create-name"
                        type="text"
                        :value="createForm.name"
                        dir="rtl"
                        lang="dv"
                        class="w-full rounded-lg border px-3 py-2 text-sm text-slate-900 transition focus:outline-none focus:ring-2 text-right"
                        @keydown="handleDhivehiKeydown($event, createForm, 'name')"
                        @input="handleDhivehiInput($event, createForm, 'name')"
                        :class="createForm.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200'"
                        required
                    >
                    <p v-if="createForm.errors.name" class="text-xs text-red-500 text-left" dir="ltr">{{ createForm.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <label for="create-departure-date" class="text-sm font-medium text-slate-700 text-right">ފުރާ ތާރީޚު</label>
                    <input
                        id="create-departure-date"
                        type="date"
                        v-model="createForm.departure_date"
                        dir="ltr"
                        class="w-full rounded-lg border px-3 py-2 text-sm text-slate-900 transition focus:outline-none focus:ring-2"
                        :class="createForm.errors.departure_date ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200'"
                        required
                    >
                    <p v-if="createForm.errors.departure_date" class="text-xs text-red-500 text-left" dir="ltr">{{ createForm.errors.departure_date }}</p>
                </div>

                <div class="space-y-2">
                    <label for="create-price" class="text-sm font-medium text-slate-700 text-right">އަގު (MVR)</label>
                    <input
                        id="create-price"
                        type="number"
                        v-model="createForm.price"
                        dir="ltr"
                        class="w-full rounded-lg border px-3 py-2 text-sm text-slate-900 transition focus:outline-none focus:ring-2"
                        :class="createForm.errors.price ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200'"
                        required
                    >
                    <p v-if="createForm.errors.price" class="text-xs text-red-500 text-left" dir="ltr">{{ createForm.errors.price }}</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeCreateModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-slate-600">ކެންސަލް</button>
                    <button type="submit" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-70" :disabled="createForm.processing">
                        {{ createForm.processing ? 'ހެދަމުން...' : 'ދަތުރު ހެދުން' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
