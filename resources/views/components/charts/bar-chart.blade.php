{{-- resources/views/components/charts/bar-chart.blade.php --}}
@props(['id' => 'barChart', 'labels' => [], 'datasets' => [], 'title' => null])

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
        type: 'bar',
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
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'rgba(255,255,255,0.9)'
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
                        text: 'Valeur',
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                bar: {
                    borderRadius: 8,
                    borderSkipped: false
                }
            }
        }
    });
});
</script>