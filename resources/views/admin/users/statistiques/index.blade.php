{{-- resources/views/admin/statistiques/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Statistiques - Jenovic')

@section('page-title', 'Statistiques des utilisateurs')
@section('page-subtitle', 'Analyse complète de vos utilisateurs')

@section('content')
<div class="stats-container">
    
    {{-- ========================================== --}}
    {{-- CARTES STATISTIQUES --}}
    {{-- ========================================== --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TOTAL UTILISATEURS</span>
                <span class="stat-value">{{ $stats['total_users'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">ADMINISTRATEURS</span>
                <span class="stat-value">{{ $stats['admins'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">CHEFS DE PROJET</span>
                <span class="stat-value">{{ $stats['chefs_projet'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-hard-hat"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">CHEFS DE CHANTIER</span>
                <span class="stat-value">{{ $stats['chefs_chantier'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- GRAPHIQUES --}}
    {{-- ========================================== --}}
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="fas fa-chart-line"></i> ÉVOLUTION DES INSCRIPTIONS</h5>
                <span class="badge">12 derniers mois</span>
            </div>
            <div class="chart-body">
                <canvas id="usersChart" height="280"></canvas>
            </div>
        </div>
        
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="fas fa-chart-pie"></i> RÉPARTITION PAR RÔLE</h5>
            </div>
            <div class="chart-body">
                <canvas id="usersPieChart" height="250"></canvas>
                <div class="legend">
                    <span><span class="legend-color" style="background: #dc2626;"></span> Admins: {{ $stats['admins'] ?? 0 }}</span>
                    <span><span class="legend-color" style="background: #10b981;"></span> Chefs projet: {{ $stats['chefs_projet'] ?? 0 }}</span>
                    <span><span class="legend-color" style="background: #f59e0b;"></span> Chefs chantier: {{ $stats['chefs_chantier'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- TABLEAU DES DERNIERS UTILISATEURS --}}
    {{-- ========================================== --}}
    <div class="table-card">
        <div class="card-header">
            <h5><i class="fas fa-clock"></i> DERNIERS UTILISATEURS INSCRITS</h5>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NOM</th>
                        <th>EMAIL</th>
                        <th>RÔLE</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersUsers ?? [] as $user)
                    <tr>
                        <td><strong>{{ $user->nom ?? $user->name ?? 'N/A' }}</strong></td>
                        <td>{{ $user->email ?? 'N/A' }}</td>
                        <td>
                            @php
                                $role = $user->role ?? 'chef_chantier';
                            @endphp
                            @if($role == 'admin')
                                <span class="role-badge admin">Admin</span>
                            @elseif($role == 'chef_projet')
                                <span class="role-badge cp">Chef projet</span>
                            @else
                                <span class="role-badge cc">Chef chantier</span>
                            @endif
                        </td>
                        <td>{{ isset($user->created_at) ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Aucun utilisateur</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
.stats-container { max-width: 1200px; margin: 0 auto; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
.stat-card { background: white; display: flex; align-items: center; gap: 1rem; padding: 1.25rem; border: 1px solid #e2e8f0; border-radius: 8px; transition: all 0.3s ease; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
.stat-icon { width: 55px; height: 55px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.bg-primary { background: linear-gradient(135deg, #1a56db, #0e3a8a); }
.bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon i { font-size: 1.5rem; color: white; }
.stat-info { flex: 1; }
.stat-label { font-size: 0.688rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; display: block; font-weight: 700; }
.stat-value { font-size: 1.75rem; font-weight: 800; color: #111827; display: block; line-height: 1.2; }
.charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
.chart-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.chart-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
.chart-header h5 { margin: 0; font-size: 0.813rem; font-weight: 700; }
.chart-header h5 i { margin-right: 0.5rem; color: #1a56db; }
.chart-header .badge { background: #1a56db; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.688rem; }
.chart-body { padding: 1.25rem; }
.legend { display: flex; justify-content: center; gap: 1.5rem; margin-top: 1rem; flex-wrap: wrap; }
.legend span { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #4b5563; }
.legend-color { width: 12px; height: 12px; border-radius: 2px; display: inline-block; }
.table-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
.card-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.card-header h5 { margin: 0; font-size: 0.813rem; font-weight: 700; }
.card-header h5 i { margin-right: 0.5rem; color: #1a56db; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 0.875rem 1rem; background: #f8fafc; font-size: 0.688rem; font-weight: 700; color: #6b7280; text-align: left; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; letter-spacing: 1px; }
.data-table td { padding: 0.875rem 1rem; border-bottom: 1px solid #e2e8f0; font-size: 0.813rem; }
.role-badge { display: inline-flex; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.688rem; font-weight: 600; }
.role-badge.admin { background: #fee2e2; color: #dc2626; }
.role-badge.cp { background: #d1fae5; color: #059669; }
.role-badge.cc { background: #dbeafe; color: #1d4ed8; }
.text-center { text-align: center; padding: 2rem; color: #6b7280; }
@media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .charts-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution des inscriptions
    var ctx1 = document.getElementById('usersChart');
    if(ctx1) {
        var labels = @json($evolutionLabels ?? []);
        var data = @json($evolutionDataValues ?? []);
        
        // Si aucune donnée, afficher un message
        if (labels.length === 0) {
            labels = ['Aucune donnée'];
            data = [0];
        }
        
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inscriptions',
                    data: data,
                    borderColor: '#1a56db',
                    backgroundColor: 'rgba(26,86,219,0.05)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#1a56db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 10 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
    
    // Graphique circulaire des rôles
    var ctx2 = document.getElementById('usersPieChart');
    if(ctx2) {
        var admins = {{ $stats['admins'] ?? 0 }};
        var chefsProjet = {{ $stats['chefs_projet'] ?? 0 }};
        var chefsChantier = {{ $stats['chefs_chantier'] ?? 0 }};
        
        // Si tous les totaux sont 0, afficher des données fictives pour le graphique
        var data = [admins, chefsProjet, chefsChantier];
        var hasData = data.some(v => v > 0);
        
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Admins', 'Chefs projet', 'Chefs chantier'],
                datasets: [{
                    data: hasData ? data : [1, 1, 1],
                    backgroundColor: hasData ? ['#dc2626', '#10b981', '#f59e0b'] : ['#e5e7eb', '#e5e7eb', '#e5e7eb'],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection