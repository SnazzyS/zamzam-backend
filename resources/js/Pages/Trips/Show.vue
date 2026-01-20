<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const navigateToCustomer = (customerId) => {
    router.visit(route('trips.customers.show', [props.trip.id, customerId]));
};

const props = defineProps({
    trip: Object,
});

const customers = computed(() => props.trip?.customers || []);
const groups = computed(() => props.trip?.groups || []);

const sortMode = ref('registered');

const groupForm = useForm({
    name: '',
});

const groupTags = computed(() => {
    return groups.value.map((group, index) => ({
        ...group,
        order: index,
        tag: `G${index + 1}`,
    }));
});

const groupMap = computed(() => {
    const map = new Map();
    groupTags.value.forEach((group) => {
        map.set(String(group.id), group);
    });
    return map;
});

const groupLabelFor = (customer) => {
    const groupId = customer?.pivot?.group_id;
    if (!groupId) {
        return '';
    }

    return groupMap.value.get(String(groupId))?.tag ?? '';
};

const customersWithMeta = computed(() => {
    return customers.value.map((customer, index) => ({
        ...customer,
        _order: index,
        _groupLabel: groupLabelFor(customer),
        _groupOrder: groupMap.value.get(String(customer?.pivot?.group_id))?.order ?? null,
    }));
});

const sortedCustomers = computed(() => {
    const list = customersWithMeta.value.slice();
    if (sortMode.value === 'group') {
        list.sort((a, b) => {
            const aOrder = a._groupOrder ?? Number.POSITIVE_INFINITY;
            const bOrder = b._groupOrder ?? Number.POSITIVE_INFINITY;
            if (aOrder === bOrder) {
                return a._order - b._order;
            }
            return aOrder - bOrder;
        });
    }
    return list;
});

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    if (typeof value === 'string' && value.includes('-')) {
        const [year, month, day] = value.split('-');
        if (day) {
            return `${day}/${month}/${year}`;
        }
    }

    return value;
};

const createGroup = () => {
    if (!props.trip?.id) {
        return;
    }

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
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ trip?.name || 'Trip' }}</h1>
                <p class="mt-1 text-slate-500" dir="rtl" lang="dv">ކަސްޓަމަރު ލިސްޓް</p>
            </div>

            <div class="flex flex-wrap items-center gap-3" dir="rtl" lang="dv">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-slate-500">ސޯޓް</label>
                    <select
                        v-model="sortMode"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 transition focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                    >
                        <option value="registered">ރެޖިސްޓަރ އަންގަ</option>
                        <option value="group">ގްރޫޕު އަންގަ</option>
                    </select>
                </div>
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:opacity-60"
                    :disabled="groupForm.processing"
                    @click="createGroup"
                >
                    {{ groupForm.processing ? 'ތައްޔާރު ކުރަނީ...' : 'ގްރޫޕު ޓެގް އެޑް' }}
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-slate-100 text-xs font-semibold text-slate-600" dir="rtl" lang="dv">
                        <tr>
                            <th class="border border-slate-200 px-3 py-2 text-right">އުފަން ތާރީޚް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">ފޯނު ނަންބަރު</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">އައިޑީ ކާޑު</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">އެޑްރެސް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">ރަށް</th>
                            <th class="border border-slate-200 px-3 py-2 text-right">ނަން</th>
                            <th class="border border-slate-200 px-3 py-2 text-center" dir="rtl" lang="dv">ހާޖީ ނަންބަރު</th>
                            <th class="border border-slate-200 px-3 py-2 text-center" dir="ltr">#</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr
                            v-for="(customer, index) in sortedCustomers"
                            :key="customer.id"
                            class="even:bg-slate-50/70 cursor-pointer hover:bg-violet-50 transition-colors"
                            @click="navigateToCustomer(customer.id)"
                        >
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl">
                                {{ formatDate(customer.date_of_birth) }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl">
                                {{ customer.phone_number || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right font-mono text-slate-600" dir="rtl">
                                {{ customer.national_id }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl" lang="dv">
                                {{ customer.address || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl" lang="dv">
                                {{ customer.island || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right" dir="rtl" lang="dv">
                                <div class="flex flex-col gap-1 items-end text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-2 w-full">
                                        <Link
                                            class="font-medium text-right w-full text-slate-800 hover:text-violet-600"
                                            :href="route('trips.customers.show', [trip.id, customer.id])"
                                            dir="rtl"
                                            lang="dv"
                                        >
                                            {{ customer.name }}
                                        </Link>
                                        <span
                                            v-if="customer._groupLabel"
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600"
                                            dir="ltr"
                                        >
                                            {{ customer._groupLabel }}
                                        </span>
                                    </div>
                                    <div v-if="groupTags.length" class="w-fit">
                                        <select
                                            class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs text-slate-700 transition focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                            dir="rtl"
                                            lang="dv"
                                            :value="customer.pivot?.group_id ?? ''"
                                            :disabled="assigningCustomerId === customer.id"
                                            @click.stop
                                            @change="assignGroup(customer.id, $event.target.value)"
                                        >
                                            <option value="">ގްރޫޕު ނުހިމެނޭ</option>
                                            <option
                                                v-for="group in groupTags"
                                                :key="group.id"
                                                :value="group.id"
                                            >
                                                {{ group.tag }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right font-mono text-slate-600" dir="rtl">
                                {{ customer.pivot?.umrah_id || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-center" dir="ltr">
                                {{ index + 1 }}
                            </td>
                        </tr>
                        <tr v-if="sortedCustomers.length === 0">
                            <td class="border border-slate-200 px-5 py-8 text-center text-slate-400" colspan="8">
                                No customers
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</template>
