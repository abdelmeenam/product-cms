import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

const salesPerformanceChart = document.getElementById('sales-performance-chart');

if (salesPerformanceChart instanceof HTMLCanvasElement) {
    const labels = JSON.parse(salesPerformanceChart.dataset.labels ?? '[]');
    const values = JSON.parse(salesPerformanceChart.dataset.values ?? '[]');

    if (labels.length > 0 && values.length > 0) {
        new Chart(salesPerformanceChart, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.12)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.16)',
                        },
                    },
                },
            },
        });
    }
}
