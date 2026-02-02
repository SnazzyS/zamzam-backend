<script setup>
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
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
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            size: 11,
                        },
                    },
                    border: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            size: 11,
                        },
                        callback: (value) => {
                            if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'k';
                            }
                            return value;
                        },
                    },
                    border: {
                        display: false,
                    },
                },
            },
        }),
    },
});
</script>

<template>
    <Bar :data="chartData" :options="chartOptions" />
</template>
