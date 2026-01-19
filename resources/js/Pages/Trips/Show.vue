<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { tkGetChar, toDhivehi } from '../../utils/lattinMapping';

const props = defineProps({
    trip: Object,
});

const customers = computed(() => props.trip?.customers || []);
const groups = computed(() => props.trip?.groups || []);

const groupTypeOptions = [
    { value: 'family', label: 'އާއިލާ' },
    { value: 'group', label: 'ގުރޫޕު' },
];

const groupTypeLabel = (type) => {
    if (type === 'family') {
        return 'އާއިލާ';
    }
    if (type === 'friends') {
        return 'ގުރޫޕު';
    }
    return 'ގުރޫޕު';
};

const groupedCustomers = computed(() => {
    const groupMap = new Map();
    groups.value.forEach((group) => {
        groupMap.set(String(group.id), { ...group, customers: [] });
    });

    const individuals = [];

    customers.value.forEach((customer) => {
        const groupId = customer?.pivot?.group_id;
        const groupKey = groupId ? String(groupId) : null;
        if (groupKey && groupMap.has(groupKey)) {
            groupMap.get(groupKey).customers.push(customer);
            return;
        }

        individuals.push(customer);
    });

    const grouped = Array.from(groupMap.values()).sort((a, b) => {
        if (a.type === b.type) {
            return a.name.localeCompare(b.name);
        }

        return a.type.localeCompare(b.type);
    });

    return {
        groups: grouped,
        individuals,
    };
});

const groupForm = useForm({
    name: '',
    type: 'family',
});

const handleDhivehiInput = (event, targetForm, field) => {
    const mapped = toDhivehi(event.target.value);
    if (mapped !== event.target.value) {
        event.target.value = mapped;
    }
    targetForm[field] = mapped;
};

const handleDhivehiKeydown = (event, targetForm, field) => {
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
    targetForm[field] = nextValue;
};

const createGroup = () => {
    groupForm.post(route('trips.groups.store', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            groupForm.reset('name');
        },
    });
};

const assigningCustomerId = ref(null);

const assignGroup = (customerId, groupId) => {
    assigningCustomerId.value = customerId;
    const normalizedGroupId = groupId ? Number(groupId) : null;

    router.put(
        route('trips.customers.assign-group', [props.trip.id, customerId]),
        { group_id: normalizedGroupId },
        {
            preserveScroll: true,
            onFinish: () => {
                assigningCustomerId.value = null;
            },
        }
    );
};
</script>

<template>
    <Head title="ދަތުރު" />

    <main class="space-y-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ trip?.name || 'ދަތުރު' }}</h1>
            <p class="mt-1 text-slate-500">ދަތުރުގެ ކަސްޓަމަރުން އަދި ގުރޫޕުތައް</p>
        </div>

        <!-- Groups Section -->
        <section class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">ގުރޫޕުތައް</h2>
                        <p class="text-sm text-slate-500 mt-0.5">މި ދަތުރުގެ އާއިލާ/ގުރޫޕު ތައުލިއުތައް</p>
                    </div>

                    <form @submit.prevent="createGroup" class="flex flex-wrap items-end gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-slate-700">ކޮންކަހަލަ؟</label>
                            <select
                                v-model="groupForm.type"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                            >
                                <option v-for="option in groupTypeOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                            <p v-if="groupForm.errors.type" class="text-xs text-red-500">{{ groupForm.errors.type }}</p>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-slate-700">ނަން</label>
                            <input
                                :value="groupForm.name"
                                type="text"
                                placeholder="އާއިލާ 1"
                                dir="rtl"
                                lang="dv"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition-all duration-200 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                @keydown="handleDhivehiKeydown($event, groupForm, 'name')"
                                @input="handleDhivehiInput($event, groupForm, 'name')"
                            >
                            <p v-if="groupForm.errors.name" class="text-xs text-red-500">{{ groupForm.errors.name }}</p>
                        </div>

                        <button
                            type="submit"
                            class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-violet-600/25 transition-all duration-200 hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="groupForm.processing"
                        >
                            {{ groupForm.processing ? 'ހަދާލަމުން...' : 'ހަދާ' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Group Cards -->
            <div v-if="groupedCustomers.groups.length > 0" class="space-y-4">
                <div
                    v-for="group in groupedCustomers.groups"
                    :key="group.id"
                    class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm"
                >
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-5 py-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center rounded-lg bg-violet-600 px-2.5 py-1 text-xs font-medium text-white shadow-sm">
                                {{ groupTypeLabel(group.type) }}
                            </span>
                            <h3 class="text-base font-semibold text-slate-800">{{ group.name }}</h3>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ group.customers.length }} ކަސްޓަމަރުން
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50/50 text-xs font-medium uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 text-right">ނަން</th>
                                    <th class="px-5 py-3 text-left" dir="ltr">ނޭޝަނަލް އައިޑީ</th>
                                    <th class="px-5 py-3 text-right">އެކްޝަން</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr v-for="customer in group.customers" :key="customer.id" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-3.5 text-right font-medium">{{ customer.name }}</td>
                                    <td class="px-5 py-3.5 text-left font-mono text-slate-500" dir="ltr">{{ customer.national_id }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-slate-800 disabled:opacity-50"
                                            :disabled="assigningCustomerId === customer.id"
                                            @click.prevent="assignGroup(customer.id, null)"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            އެކަލެޔާއަށް ބަދަލު
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="group.customers.length === 0">
                                    <td class="px-5 py-8 text-center text-slate-400" colspan="3">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                            <span>މި ގުރޫޕުގައި ކަސްޓަމަރު ނެތް</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Empty Groups State -->
            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                    <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">ގުރޫޕު ނެތް</p>
                <p class="text-sm text-slate-400 mt-1">އާއިލާ ނުވަތަ ގުރޫޕު ހެދާލާ</p>
            </div>
        </section>

        <!-- Individual Customers Section -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">އެކަލެޔާ ކަސްޓަމަރުން</h2>
                    <p class="text-sm text-slate-500 mt-0.5">ގުރޫޕެއްގައި ނެތް ކަސްޓަމަރުން</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ groupedCustomers.individuals.length }}
                </span>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/50 text-xs font-medium uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-right">ނަން</th>
                                <th class="px-5 py-3 text-left" dir="ltr">ނޭޝަނަލް އައިޑީ</th>
                                <th class="px-5 py-3 text-left">ގުރޫޕުއަށް އެޅުން</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr v-for="customer in groupedCustomers.individuals" :key="customer.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3.5 text-right font-medium">{{ customer.name }}</td>
                                <td class="px-5 py-3.5 text-left font-mono text-slate-500" dir="ltr">{{ customer.national_id }}</td>
                                <td class="px-5 py-3.5 text-left">
                                    <select
                                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 transition-all duration-200 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10"
                                        :value="customer.pivot?.group_id ?? ''"
                                        :disabled="assigningCustomerId === customer.id || groupedCustomers.groups.length === 0"
                                        @change="assignGroup(customer.id, $event.target.value)"
                                    >
                                        <option value="" disabled>ގުރޫޕު ހޮވާ</option>
                                        <option
                                            v-for="group in groupedCustomers.groups"
                                            :key="group.id"
                                            :value="group.id"
                                        >
                                            {{ groupTypeLabel(group.type) }} · {{ group.name }}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr v-if="groupedCustomers.individuals.length === 0">
                                <td class="px-5 py-8 text-center text-slate-400" colspan="3">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>އެކަލެޔާ ކަސްޓަމަރުން ނެތް</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</template>
