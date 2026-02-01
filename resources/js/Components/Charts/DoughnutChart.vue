<script setup>
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    ArcElement,
    Title,
    Tooltip,
    Legend
);

defineProps({
    chartData: Object,
    chartOptions: {
        type: Object,
        default: () => ({
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart',
            },
            plugins: {
                legend: {
                    position: 'right',
                },
            },
        }),
    },
});
</script>

<template>
    <Doughnut :data="chartData" :options="chartOptions" />
</template>
