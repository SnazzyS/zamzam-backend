<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const navigateToCustomer = (customerId) => {
    router.visit(route('trips.customers.show', [props.trip.id, customerId]));
};

const props = defineProps({
    trip: Object,
});

const customers = computed(() => props.trip?.customers || []);
const groups = computed(() => props.trip?.groups || []);

const groupMap = computed(() => {
    const map = new Map();
    groups.value.forEach((group, index) => {
        map.set(group.id, `G${index + 1}`);
    });
    return map;
});

const getGroupTag = (groupId) => {
    if (!groupId) return '-';
    return groupMap.value.get(groupId) || '-';
};

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
</script>

<template>
    <Head title="Trip" />

    <main class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ trip?.name || 'Trip' }}</h1>
            <p class="mt-1 text-slate-500" dir="rtl" lang="dv">ކަސްޓަމަރު ލިސްޓް</p>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-slate-100 text-xs font-semibold text-slate-600" dir="rtl" lang="dv">
                        <tr>
                            <th class="border border-slate-200 px-3 py-2 text-center" dir="rtl" lang="dv">ގްރޫޕް</th>
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
                            v-for="(customer, index) in customers"
                            :key="customer.id"
                            class="even:bg-slate-50/70 cursor-pointer hover:bg-violet-50 transition-colors"
                            @click="navigateToCustomer(customer.id)"
                        >
                            <td class="border border-slate-200 px-3 py-2 text-center" dir="ltr">
                                <span
                                    v-if="customer.pivot?.group_id"
                                    class="inline-flex items-center rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700"
                                >
                                    {{ getGroupTag(customer.pivot.group_id) }}
                                </span>
                                <span v-else class="text-slate-400">-</span>
                            </td>
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
                                <span class="font-medium text-slate-800">{{ customer.name }}</span>
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-right font-mono text-slate-600" dir="rtl">
                                {{ customer.pivot?.umrah_id || '-' }}
                            </td>
                            <td class="border border-slate-200 px-3 py-2 text-center" dir="ltr">
                                {{ index + 1 }}
                            </td>
                        </tr>
                        <tr v-if="customers.length === 0">
                            <td class="border border-slate-200 px-5 py-8 text-center text-slate-400" colspan="9">
                                No customers
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</template>
