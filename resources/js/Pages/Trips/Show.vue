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
    <Head title="Trip" />

    <main class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">{{ trip?.name || 'Trip' }}</h1>
            <p class="text-sm text-slate-500">ދަތުރުގެ ކަސްޓަމަރުން</p>
        </div>

        <section class="space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold text-slate-900">Groups</h2>
                    <p class="text-xs text-slate-500">Family, friends, or other groups per trip</p>
                </div>

                <form @submit.prevent="createGroup" class="flex flex-wrap items-end gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-500">ކޮންކަހަލަ؟</label>
                        <select
                            v-model="groupForm.type"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                        >
                            <option v-for="option in groupTypeOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <p v-if="groupForm.errors.type" class="text-xs text-red-500">{{ groupForm.errors.type }}</p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-500">ނަން</label>
                        <input
                            :value="groupForm.name"
                            type="text"
                            placeholder="Family 1"
                            dir="rtl"
                            lang="dv"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-right text-sm text-slate-700 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                            @keydown="handleDhivehiKeydown($event, groupForm, 'name')"
                            @input="handleDhivehiInput($event, groupForm, 'name')"
                        >
                        <p v-if="groupForm.errors.name" class="text-xs text-red-500">{{ groupForm.errors.name }}</p>
                    </div>

                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="groupForm.processing"
                    >
                        {{ groupForm.processing ? 'ހަދާލަމުން...' : 'ހަދާ' }}
                    </button>
                </form>
            </div>

            <div v-if="groupedCustomers.groups.length > 0" class="space-y-4">
                <div
                    v-for="group in groupedCustomers.groups"
                    :key="group.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-slate-900 px-2 py-0.5 text-xs font-semibold text-white">
                                {{ groupTypeLabel(group.type) }}
                            </span>
                            <h3 class="text-sm font-semibold text-slate-900">{{ group.name }}</h3>
                        </div>
                        <span class="text-xs text-slate-500">
                            {{ group.customers.length }} customers
                        </span>
                    </div>
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-right font-medium">name</th>
                                <th class="px-4 py-3 text-left font-medium" dir="ltr">national_id</th>
                                <th class="px-4 py-3 text-right font-medium">action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr v-for="customer in group.customers" :key="customer.id">
                                <td class="px-4 py-3 text-right">{{ customer.name }}</td>
                                <td class="px-4 py-3 text-left" dir="ltr">{{ customer.national_id }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        class="text-xs font-semibold text-slate-500 transition hover:text-slate-700 disabled:opacity-50"
                                        :disabled="assigningCustomerId === customer.id"
                                        @click.prevent="assignGroup(customer.id, null)"
                                    >
                                        Move to individuals
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="group.customers.length === 0">
                                <td class="px-4 py-6 text-center text-slate-400" colspan="3">No customers in this group</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-400">
                No groups yet. Create one to start organizing families or friends.
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Individuals</h2>
                <span class="text-xs text-slate-500">
                    {{ groupedCustomers.individuals.length }} customers
                </span>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium">name</th>
                            <th class="px-4 py-3 text-left font-medium" dir="ltr">national_id</th>
                            <th class="px-4 py-3 text-left font-medium">assign to group</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr v-for="customer in groupedCustomers.individuals" :key="customer.id">
                            <td class="px-4 py-3 text-right">{{ customer.name }}</td>
                            <td class="px-4 py-3 text-left" dir="ltr">{{ customer.national_id }}</td>
                            <td class="px-4 py-3 text-left">
                                <select
                                    class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-300"
                                    :value="customer.pivot?.group_id ?? ''"
                                    :disabled="assigningCustomerId === customer.id || groupedCustomers.groups.length === 0"
                                    @change="assignGroup(customer.id, $event.target.value)"
                                >
                                    <option value="" disabled>Choose group</option>
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
                            <td class="px-4 py-6 text-center text-slate-400" colspan="3">No individual customers</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
