{{-- resources/views/components/charts/pie-chart.blade.php --}}
@props(['id' => 'pieChart', 'labels' => [], 'datasets' => [], 'title' => null])

<div class="chart-container">
    @if($title)
        <h4 class="chart-title">{{ $title }}</h4>
    @endif
    <canvas id="{{ $id }}" style="max-height: 300px;"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $id }}').getContext('2d');
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: @json($labels),
            datasets: @json($datasets)
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 10,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>