<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    trip: Object,
    bus: Object,
    passengers: Array,
});
</script>

<template>
    <Head :title="`${bus.name} - Passenger List`" />

    <div class="min-h-screen bg-white p-8 print:p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header with Logo -->
            <div class="text-center mb-6">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <img src="/images/logo.png" alt="Zam Zam" class="h-12 w-auto" onerror="this.style.display='none'">
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-800 leading-tight">Zam Zam</p>
                        <p class="text-xs text-slate-600 leading-tight">Hajj & Umra Travel</p>
                    </div>
                </div>

                <!-- Trip Info -->
                <h1 class="text-2xl font-bold text-slate-800 mb-1" dir="rtl" lang="dv">
                    {{ trip.name }}
                </h1>

                <!-- Bus Info -->
                <p class="text-lg text-slate-600" dir="rtl" lang="dv">
                    {{ bus.name }}{{ bus.bus_number ? ` - ${bus.bus_number}` : '' }}
                </p>
            </div>

            <!-- Passengers Table (RTL - # on right) -->
            <table class="w-full border-collapse text-sm" dir="rtl" lang="dv">
                <thead>
                    <tr class="border-b-2 border-slate-300">
                        <th class="py-2 px-3 text-center font-semibold text-slate-700 w-12">#</th>
                        <th class="py-2 px-3 text-right font-semibold text-slate-700">ހާޖީ ނަންބަރު</th>
                        <th class="py-2 px-3 text-right font-semibold text-slate-700">ނަން</th>
                        <th class="py-2 px-3 text-right font-semibold text-slate-700">ރަށް</th>
                        <th class="py-2 px-3 text-right font-semibold text-slate-700">އައިޑީ ކާޑު ނަންބަރު</th>
                        <th class="py-2 px-3 text-right font-semibold text-slate-700">ފޯނު ނަންބަރު</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(passenger, index) in passengers"
                        :key="passenger.id"
                        class="border-b border-slate-200"
                    >
                        <td class="py-2 px-3 text-center">{{ index + 1 }}</td>
                        <td class="py-2 px-3 text-right font-mono">{{ passenger.umrah_id || '-' }}</td>
                        <td class="py-2 px-3 text-right font-medium">{{ passenger.name }}</td>
                        <td class="py-2 px-3 text-right">{{ passenger.island || '-' }}</td>
                        <td class="py-2 px-3 text-right font-mono" dir="ltr">{{ passenger.national_id }}</td>
                        <td class="py-2 px-3 text-right" dir="ltr">{{ passenger.phone_number || '-' }}</td>
                    </tr>
                    <tr v-if="passengers.length === 0">
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            No passengers assigned to this bus
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Footer with count -->
            <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Total: <span class="font-semibold">{{ passengers.length }}</span> passengers
                </p>
                <p class="text-xs text-slate-400 print:hidden">
                    <a
                        :href="route('trips.buses.show', [trip.id, bus.id])"
                        class="text-blue-600 hover:text-blue-800"
                    >
                        Back to Bus
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>
