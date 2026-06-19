{{-- resources/views/components/cards/stat-card.blade.php --}}
@props(['title', 'value', 'icon', 'color' => 'primary', 'trend' => null, 'trendValue' => null])

@php
    $colors = [
        'primary' => ['bg' => '#1a56db', 'light' => 'rgba(26,86,219,0.1)'],
        'success' => ['bg' => '#10b981', 'light' => 'rgba(16,185,129,0.1)'],
        'warning' => ['bg' => '#f59e0b', 'light' => 'rgba(245,158,11,0.1)'],
        'danger' => ['bg' => '#ef4444', 'light' => 'rgba(239,68,68,0.1)'],
        'info' => ['bg' => '#06b6d4', 'light' => 'rgba(6,182,212,0.1)'],
        'purple' => ['bg' => '#8b5cf6', 'light' => 'rgba(139,92,246,0.1)'],
    ];
    $colorStyle = $colors[$color] ?? $colors['primary'];
@endphp

<div class="stat-card" data-aos="fade-up" data-aos-duration="500">
    <div class="stat-card-header">
        <div class="stat-icon" style="background: {{ $colorStyle['light'] }}">
            <i class="{{ $icon }}" style="color: {{ $colorStyle['bg'] }}"></i>
        </div>
        @if($trend)
            <div class="stat-trend {{ $trend === 'up' ? 'trend-up' : 'trend-down' }}">
                <i class="fas fa-arrow-{{ $trend === 'up' ? 'up' : 'down' }}"></i>
                <span>{{ $trendValue }}%</span>
            </div>
        @endif
    </div>
    <div class="stat-card-body">
        <h3 class="stat-value">{{ $value }}</h3>
        <p class="stat-title">{{ $title }}</p>
    </div>
    <div class="stat-card-footer">
        <span class="stat-period">Dernières 30 jours</span>
    </div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, {{ $colorStyle['bg'] }}, transparent);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.5rem;
}

.stat-trend {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.trend-up {
    background: rgba(16,185,129,0.1);
    color: #10b981;
}

.trend-down {
    background: rgba(239,68,68,0.1);
    color: #ef4444;
}

.stat-card-body {
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.stat-title {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 5px 0 0;
}

.stat-card-footer {
    border-top: 1px solid #f3f4f6;
    padding-top: 0.75rem;
}

.stat-period {
    font-size: 0.7rem;
    color: #9ca3af;
}
</style>