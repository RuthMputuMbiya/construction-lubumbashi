{{-- resources/views/components/cards/chart-card.blade.php --}}
@props(['title', 'subtitle' => null, 'id' => 'chart-' . uniqid()])

<div class="chart-card" data-aos="fade-up">
    <div class="chart-card-header">
        <div>
            <h4 class="chart-title">{{ $title }}</h4>
            @if($subtitle)
                <p class="chart-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="chart-actions">
            <button class="chart-action-btn" onclick="window.print()">
                <i class="fas fa-print"></i>
            </button>
            <button class="chart-action-btn" onclick="downloadChart('{{ $id }}')">
                <i class="fas fa-download"></i>
            </button>
        </div>
    </div>
    <div class="chart-card-body">
        <canvas id="{{ $id }}" style="width: 100%; height: 300px;"></canvas>
    </div>
    <div class="chart-card-footer">
        <div class="chart-stats">
            {{ $slot }}
        </div>
    </div>
</div>

<style>
.chart-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
}

.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.chart-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.chart-subtitle {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 5px 0 0;
}

.chart-actions {
    display: flex;
    gap: 0.5rem;
}

.chart-action-btn {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 5px 8px;
    border-radius: 8px;
    transition: all 0.3s;
}

.chart-action-btn:hover {
    background: #f3f4f6;
    color: #1a56db;
}

.chart-card-body {
    margin-bottom: 1rem;
}

.chart-card-footer {
    border-top: 1px solid #f3f4f6;
    padding-top: 1rem;
}

.chart-stats {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 1rem;
}

<script>
function downloadChart(chartId) {
    const canvas = document.getElementById(chartId);
    const link = document.createElement('a');
    link.download = `chart-${Date.now()}.png`;
    link.href = canvas.toDataURL();
    link.click();
}
</script>
</style>