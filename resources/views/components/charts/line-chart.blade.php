{{-- resources/views/components/charts/line-chart.blade.php --}}
@props(['id' => 'lineChart', 'labels' => [], 'datasets' => [], 'title' => null])

<div class="chart-container">
    @if($title)
        <h4 class="chart-title">{{ $title }}</h4>
    @endif
    <canvas id="{{ $id }}"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id }}').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: @json($datasets)
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 10
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'rgba(255,255,255,0.9)',
                    borderColor: '#1a56db',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    title: {
                        display: true,
                        text: 'Pourcentage (%)',
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Date',
                        color: '#6b7280'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            elements: {
                line: {
                    tension: 0.4,
                    borderWidth: 3
                },
                point: {
                    radius: 4,
                    hoverRadius: 6,
                    borderWidth: 2
                }
            }
        }
    });
});
</script>

<style>
.chart-container {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.chart-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
}
</style>