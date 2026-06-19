{{-- resources/views/admin/dashboard.blade.php --}}
{{-- 
    DASHBOARD ADMINISTRATEUR
    DESIGN CARRÉ - PROFESSIONNEL - COLORÉ
--}}

@extends('layouts.admin')

@section('title', 'Dashboard Administrateur - Jenovic')

@section('page-title', 'Tableau de bord Administrateur')
@section('page-subtitle', 'Vue d\'ensemble de l\'activité')

@section('content')
<div class="dashboard-container">
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES (CARTES CARRÉES) --}}
    {{-- ========================================== --}}
    <div class="stats-grid">
        <!-- Carte Utilisateurs -->
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TOTAL UTILISATEURS</span>
                <span class="stat-value">{{ $stats['total_users'] ?? 0 }}</span>
                <div class="stat-detail">
                    <span>👑 Admins: {{ $stats['admins'] ?? 0 }}</span>
                    <span>CP: {{ $stats['chefs_projet'] ?? 0 }}</span>
                    <span>CC: {{ $stats['chefs_chantier'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        
        <!-- Carte Projets -->
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TOTAL PROJETS</span>
                <span class="stat-value">{{ $stats['total_projets'] ?? 0 }}</span>
                <div class="stat-detail">
                    <span>Actifs: {{ $stats['projets_en_cours'] ?? 0 }}</span>
                    <span>✅ Terminés: {{ $stats['projets_termines'] ?? 0 }}</span>
                    <span>❌ Annulés: {{ $stats['projets_annules'] ?? 0 }}</span>
                </div>
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
                <span class="badge">📅 30 derniers jours</span>
            </div>
            <div class="chart-body">
                <canvas id="inscriptionsChart" height="280"></canvas>
            </div>
        </div>
        
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="fas fa-chart-bar"></i> NOUVEAUX PROJETS PAR MOIS</h5>
                <span class="badge">📊 6 derniers mois</span>
            </div>
            <div class="chart-body">
                <canvas id="projetsMoisChart" height="280"></canvas>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- GRAPHIQUE RÉPARTITION & PROJETS RÉCENTS --}}
    {{-- ========================================== --}}
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="fas fa-chart-pie"></i> STATUT DES PROJETS</h5>
            </div>
            <div class="chart-body">
                <canvas id="statutChart" height="250"></canvas>
                <div class="legend">
                    <span><span class="legend-color" style="background: #f59e0b;"></span> ⏳ En attente: {{ $projetsStatut['en_attente'] ?? 0 }}</span>
                    <span><span class="legend-color" style="background: #3b82f6;"></span> ▶️ En cours: {{ $projetsStatut['en_cours'] ?? 0 }}</span>
                    <span><span class="legend-color" style="background: #10b981;"></span> ✅ Terminé: {{ $projetsStatut['termine'] ?? 0 }}</span>
                    <span><span class="legend-color" style="background: #ef4444;"></span> ❌ Annulé: {{ $projetsStatut['annule'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="fas fa-clock"></i> PROJETS RÉCENTS</h5>
                <a href="{{ route('admin.projets.index') }}" class="btn-link">📋 VOIR TOUS →</a>
            </div>
            <div class="chart-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>📁 PROJET</th>
                                <th>📍 LIEU</th>
                                <th>👨‍💼 CHEF PROJET</th>
                                <th>📊 STATUT</th>
                                <th>📈 AVANCEMENT</th>
                                <th>📅 DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projetsRecents ?? [] as $projet)
                            <tr>
                                <td><strong>{{ $projet->nom ?? 'N/A' }}</strong></td>
                                <td>{{ $projet->lieu ?? 'N/A' }}</td>
                                <td>{{ $projet->chefProjet->nom ?? $projet->chefProjet->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $statut = $projet->statut ?? 'en_attente';
                                    @endphp
                                    @if($statut == 'en_cours') 
                                        <span class="badge-status primary">▶️ En cours</span>
                                    @elseif($statut == 'termine') 
                                        <span class="badge-status success">✅ Terminé</span>
                                    @elseif($statut == 'annule') 
                                        <span class="badge-status danger">❌ Annulé</span>
                                    @else 
                                        <span class="badge-status warning">⏳ En attente</span> 
                                    @endif
                                </td>
                                <td>
                                    <div class="progress-cell">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $projet->avancement ?? 0 }}%"></div>
                                        </div>
                                        <span>{{ $projet->avancement ?? 0 }}%</span>
                                    </div>
                                 </td>
                                <td>{{ isset($projet->created_at) ? $projet->created_at->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-row">
                                    <i class="fas fa-folder-open"></i>
                                    <p>Aucun projet trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ============================================
   DASHBOARD ADMIN - DESIGN CARRÉ PROFESSIONNEL
   ============================================ */

:root {
    --primary: #1a56db;
    --primary-dark: #0e3a8a;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --dark: #111827;
    --gray: #6b7280;
    --light: #f8fafc;
    --white: #ffffff;
    --border: #e2e8f0;
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}

/* ==========================================
   STATISTIQUES (CARTES CARRÉES)
   ========================================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--white);
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon i {
    font-size: 1.5rem;
    color: white;
}

.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
.bg-success { background: linear-gradient(135deg, var(--success), #059669); }
.bg-warning { background: linear-gradient(135deg, var(--warning), #d97706); }
.bg-info { background: linear-gradient(135deg, var(--info), #0891b2); }

.stat-info {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 0.688rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    font-weight: 700;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--dark);
    display: block;
    line-height: 1.2;
}

.stat-value small {
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-detail {
    margin-top: 0.5rem;
    font-size: 0.688rem;
    color: var(--gray);
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: var(--border);
    margin: 0.5rem 0 0.25rem;
    overflow: hidden;
    border-radius: 2px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--warning), #d97706);
    border-radius: 2px;
    transition: width 0.6s ease;
}

.stars {
    color: #fbbf24;
    font-size: 0.75rem;
    letter-spacing: 2px;
}

/* ==========================================
   GRAPHIQUES
   ========================================== */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.chart-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.chart-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    background: var(--light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.chart-header h5 {
    margin: 0;
    font-size: 0.813rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: var(--dark);
}

.chart-header h5 i {
    margin-right: 0.5rem;
    color: var(--primary);
}

.chart-header .badge {
    background: var(--primary);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.688rem;
    font-weight: 600;
}

.btn-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.688rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.btn-link:hover {
    text-decoration: underline;
}

.chart-body {
    padding: 1.25rem;
}

.chart-body.p-0 {
    padding: 0;
}

/* ==========================================
   TABLEAU
   ========================================== */
.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: var(--light);
}

.data-table th {
    padding: 0.75rem 1rem;
    font-size: 0.688rem;
    font-weight: 700;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 1px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.data-table td {
    padding: 0.75rem 1rem;
    font-size: 0.813rem;
    border-bottom: 1px solid var(--border);
}

.data-table tr:hover {
    background: var(--light);
}

.data-table tr:last-child td {
    border-bottom: none;
}

/* Badges statut */
.badge-status {
    display: inline-flex;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.688rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.badge-status.primary { background: #dbeafe; color: #1d4ed8; }
.badge-status.success { background: #d1fae5; color: #059669; }
.badge-status.danger { background: #fee2e2; color: #dc2626; }
.badge-status.warning { background: #fef3c7; color: #d97706; }

/* Progress cell */
.progress-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 100px;
}

.progress-cell .progress-bar {
    flex: 1;
    height: 6px;
    background: var(--border);
    margin: 0;
}

.progress-cell .progress-fill {
    background: linear-gradient(90deg, var(--success), var(--primary));
}

.progress-cell span {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--primary);
    min-width: 35px;
}

/* Legend */
.legend {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.legend span {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.688rem;
    color: var(--gray);
}

.legend-color {
    width: 10px;
    height: 10px;
    border-radius: 2px;
    display: inline-block;
}

.empty-row {
    text-align: center;
    padding: 2rem;
    color: var(--gray);
}

.empty-row i {
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
    display: block;
}

.empty-row p {
    margin: 0;
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-detail {
        justify-content: center;
    }
    
    .chart-header {
        flex-direction: column;
        text-align: center;
    }
    
    .chart-header .badge {
        margin-top: 0.25rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique inscriptions
    const ctx1 = document.getElementById('inscriptionsChart');
    if(ctx1){
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: @json($chartData['labels'] ?? []),
                datasets: [{
                    label: 'Inscriptions',
                    data: @json($chartData['data'] ?? []),
                    borderColor: '#1a56db',
                    backgroundColor: 'rgba(26,86,219,0.05)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#1a56db',
                    fill: true,
                    tension: 0.4
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
    
    // Graphique projets par mois
    const ctx2 = document.getElementById('projetsMoisChart');
    if(ctx2){
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: @json($projetsMoisLabels ?? []),
                datasets: [{
                    label: 'Projets créés',
                    data: @json($projetsMoisData ?? []),
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        title: { display: true, text: 'Nombre de projets' },
                        ticks: { stepSize: 1 }
                    } 
                }
            }
        });
    }
    
    // Graphique statut projets
    const ctx3 = document.getElementById('statutChart');
    if(ctx3){
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'En cours', 'Terminé', 'Annulé'],
                datasets: [{
                    data: [
                        {{ $projetsStatut['en_attente'] ?? 0 }},
                        {{ $projetsStatut['en_cours'] ?? 0 }},
                        {{ $projetsStatut['termine'] ?? 0 }},
                        {{ $projetsStatut['annule'] ?? 0 }}
                    ],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: true,
                plugins: { 
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush