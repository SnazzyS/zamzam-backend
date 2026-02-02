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
            cutout: '70%',
            animation: {
                duration: 750,
                easing: 'easeOutQuart',
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f8fafc',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    boxPadding: 4,
                },
            },
        }),
    },
});
</script>

<template>
    <Doughnut :data="chartData" :options="chartOptions" />
</template>
