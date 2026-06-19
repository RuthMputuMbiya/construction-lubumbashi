{{-- resources/views/chef-projet/dashboard.blade.php --}}
@extends('layouts.chef-projet')

@section('title', 'Dashboard Chef Projet - Jenovic')

@section('page-title', 'Tableau de bord Chef de Projet')
@section('page-subtitle', 'Gérez vos planifications et projets de construction')

@section('content')
<div class="chef-dashboard">
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES (CARTES) --}}
    {{-- ========================================== --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">PLANIFICATIONS</span>
                <span class="stat-value">{{ $stats['total_planifications'] ?? 0 }}</span>
                <span class="stat-trend">En cours de planification</span>
            </div>
            <div class="stat-glow primary-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TOTAL PROJETS</span>
                <span class="stat-value">{{ $stats['total_projets'] ?? 0 }}</span>
                <span class="stat-trend">Tous projets confondus</span>
            </div>
            <div class="stat-glow success-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">PROJETS EN COURS</span>
                <span class="stat-value">{{ $stats['projets_en_cours'] ?? 0 }}</span>
                <span class="stat-trend">Actifs</span>
            </div>
            <div class="stat-glow warning-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">PROJETS TERMINÉS</span>
                <span class="stat-value">{{ $stats['projets_termines'] ?? 0 }}</span>
                <span class="stat-trend">Validés</span>
            </div>
            <div class="stat-glow info-glow"></div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- GRAPHIQUE ÉVOLUTION DES PROJETS --}}
    {{-- ========================================== --}}
    <div class="chart-card mb-4">
        <div class="chart-header">
            <h5><i class="fas fa-chart-bar"></i> ÉVOLUTION DES PROJETS</h5>
            <span class="badge">Cette année</span>
        </div>
        <div class="chart-body">
            <canvas id="projetsChart" height="250"></canvas>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- PLANIFICATIONS EN COURS --}}
    {{-- ========================================== --}}
    <div class="table-card mb-4">
        <div class="table-header">
            <h5><i class="fas fa-clipboard-list"></i> 📋 PLANIFICATIONS EN COURS</h5>
            <a href="{{ route('chef-projet.planification.create') }}" class="btn-create-small">
                <i class="fas fa-plus"></i> NOUVELLE PLANIFICATION
            </a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NOM DU PROJET</th>
                        <th>LIEU</th>
                        <th>BUDGET PRÉVU</th>
                        <th>DATE DÉBUT</th>
                        <th>DATE FIN</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($planifications ?? [] as $planif)
                    <tr>
                        <td class="fw-bold">{{ $planif->nom ?? 'N/A' }}</td>
                        <td><i class="fas fa-map-marker-alt"></i> {{ $planif->lieu ?? 'N/A' }}</td>
                        <td class="text-success fw-bold">
                            {{ isset($planif->budget_previsionnel) ? number_format($planif->budget_previsionnel, 0, ',', ' ') : '0' }} 
                            {{ $planif->devise ?? 'FC' }}
                        </td>
                        <td>
                            <i class="fas fa-calendar-alt"></i> 
                            {{ isset($planif->date_debut_prevue) ? \Carbon\Carbon::parse($planif->date_debut_prevue)->locale('fr')->isoFormat('D MMMM YYYY') : 'N/A' }}
                        </td>
                        <td>
                            <i class="fas fa-flag-checkered"></i> 
                            {{ isset($planif->date_fin_prevue) ? \Carbon\Carbon::parse($planif->date_fin_prevue)->locale('fr')->isoFormat('D MMMM YYYY') : 'N/A' }}
                        </td>
                        <td>
                            <a href="{{ route('chef-projet.planification.etapes', $planif->id) }}" class="action-btn" title="Planifier les étapes">
                                <i class="fas fa-tasks"></i>
                            </a>
                            <a href="{{ route('chef-projet.planification.show', $planif->id) }}" class="action-btn" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Aucune planification en cours</p>
                                <a href="{{ route('chef-projet.planification.create') }}" class="btn-create">Démarrer une planification</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- PROJETS RÉCENTS --}}
    {{-- ========================================== --}}
    <div class="table-card">
        <div class="table-header">
            <h5><i class="fas fa-clock"></i> 📁 MES PROJETS RÉCENTS</h5>
            <a href="{{ route('chef-projet.projets.index') }}" class="btn-link">VOIR TOUS →</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PROJET</th>
                        <th>LIEU</th>
                        <th>STATUT</th>
                        <th>AVANCEMENT</th>
                        <th>DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projetsRecents ?? [] as $projet)
                    <tr>
                        <td class="fw-bold">{{ $projet->nom ?? 'N/A' }}</td>
                        <td><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu ?? 'N/A' }}</td>
                        <td>
                            @php
                                $statut = $projet->statut ?? 'en_attente';
                            @endphp
                            @if($statut == 'en_cours')
                                <span class="badge primary"><i class="fas fa-play"></i> En cours</span>
                            @elseif($statut == 'termine')
                                <span class="badge success"><i class="fas fa-check"></i> Terminé</span>
                            @elseif($statut == 'annule')
                                <span class="badge danger"><i class="fas fa-ban"></i> Annulé</span>
                            @else
                                <span class="badge warning"><i class="fas fa-clock"></i> En attente</span>
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
                        <td>
                            <i class="fas fa-calendar-alt"></i> 
                            {{ isset($projet->created_at) ? $projet->created_at->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td>
                            <a href="{{ route('chef-projet.projets.show', $projet->id) }}" class="action-btn" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                <i class="fas fa-folder-open"></i>
                                <p>Aucun projet trouvé</p>
                                <small>Créez votre premier projet</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- ACTIVITÉS RÉCENTES --}}
    {{-- ========================================== --}}
    @if(!empty($activitesRecentes))
    <div class="activities-card mt-4">
        <div class="activities-header">
            <h5><i class="fas fa-bell"></i> ACTIVITÉS RÉCENTES</h5>
        </div>
        <div class="activities-list">
            @foreach($activitesRecentes as $activite)
                <div class="activity-item">
                    <div class="activity-icon bg-{{ $activite->color ?? 'primary' }}">
                        <i class="fas fa-{{ $activite->icon ?? 'bell' }}"></i>
                    </div>
                    <div class="activity-content">
                        <p>
                            <strong>{{ $activite->user ?? 'Système' }}</strong>
                            {{ $activite->action ?? 'a effectué une action' }}
                            <strong>{{ $activite->cible ?? '' }}</strong>
                        </p>
                        <small>{{ isset($activite->date) ? \Carbon\Carbon::parse($activite->date)->diffForHumans() : 'Récemment' }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- ========================================== --}}
    {{-- ACTIONS RAPIDES --}}
    {{-- ========================================== --}}
    <div class="actions-card mt-4">
        <div class="actions-header">
            <h5><i class="fas fa-bolt"></i> ACTIONS RAPIDES</h5>
        </div>
        <div class="actions-grid">
            <a href="{{ route('chef-projet.planification.create') }}" class="action-item">
                <i class="fas fa-clipboard-list"></i>
                <span>PLANIFIER</span>
                <small>Créer une planification</small>
            </a>
            
            <a href="{{ route('chef-projet.projets.index') }}" class="action-item">
                <i class="fas fa-project-diagram"></i>
                <span>MES PROJETS</span>
                <small>Voir tous mes projets</small>
            </a>
            
            <a href="{{ route('chef-projet.suivi.index') }}" class="action-item">
                <i class="fas fa-chart-line"></i>
                <span>SUIVI</span>
                <small>Voir avancement</small>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
.chef-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.stat-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.primary-glow { background: linear-gradient(90deg, #1a56db, #0e3a8a); }
.success-glow { background: linear-gradient(90deg, #10b981, #059669); }
.warning-glow { background: linear-gradient(90deg, #f59e0b, #d97706); }
.info-glow { background: linear-gradient(90deg, #06b6d4, #0891b2); }

.stat-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon i {
    font-size: 1.5rem;
    color: white;
}

.bg-primary { background: linear-gradient(135deg, #1a56db, #0e3a8a); }
.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.bg-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }

.stat-info {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 0.65rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    font-weight: 600;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    display: block;
    line-height: 1.2;
}

.stat-trend {
    font-size: 0.6rem;
    color: #94a3b8;
    display: block;
    margin-top: 0.25rem;
}

.chart-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.chart-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.chart-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
}

.chart-header h5 i {
    margin-right: 0.5rem;
    color: #1a56db;
}

.chart-header .badge {
    background: #1a56db;
    color: white;
    padding: 0.25rem 0.75rem;
    font-size: 0.65rem;
    font-weight: 600;
}

.chart-body {
    padding: 1.25rem;
}

.table-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.table-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.table-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #1e293b;
}

.table-header h5 i {
    margin-right: 0.5rem;
    color: #1a56db;
}

.btn-link {
    color: #1a56db;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 600;
}

.btn-link:hover {
    text-decoration: underline;
}

.btn-create-small {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.4rem 1.2rem;
    text-decoration: none;
    font-size: 0.65rem;
    font-weight: 700;
    transition: all 0.3s;
}

.btn-create-small:hover {
    transform: translateY(-2px);
    color: white;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    padding: 0.75rem 1rem;
    background: #f8fafc;
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.data-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.8rem;
    vertical-align: middle;
}

.data-table tr:hover {
    background: #f8fafc;
}

.fw-bold {
    font-weight: 700;
}

.text-success {
    color: #059669 !important;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.65rem;
    font-weight: 600;
}

.badge.primary { background: #dbeafe; color: #1d4ed8; }
.badge.success { background: #d1fae5; color: #059669; }
.badge.danger { background: #fee2e2; color: #dc2626; }
.badge.warning { background: #fef3c7; color: #d97706; }

.progress-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 100px;
}

.progress-cell .progress-bar {
    flex: 1;
    height: 6px;
    background: #e2e8f0;
}

.progress-cell .progress-fill {
    background: linear-gradient(90deg, #10b981, #1a56db);
    height: 100%;
    transition: width 0.6s ease;
}

.progress-cell span {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1a56db;
    min-width: 40px;
}

.action-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #dbeafe;
    color: #1a56db;
    transition: all 0.2s;
    text-decoration: none;
    margin: 0 2px;
}

.action-btn:hover {
    transform: scale(1.05);
    background: #bfdbfe;
}

.activities-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.activities-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.activities-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
}

.activities-header h5 i {
    margin-right: 0.5rem;
    color: #1a56db;
}

.activities-list {
    padding: 0.5rem 0;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}

.activity-item:hover {
    background: #f8fafc;
}

.activity-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.activity-icon.bg-primary { background: #1a56db; }
.activity-icon.bg-success { background: #10b981; }
.activity-icon.bg-warning { background: #f59e0b; }
.activity-icon.bg-danger { background: #ef4444; }
.activity-icon.bg-info { background: #06b6d4; }

.activity-content {
    flex: 1;
}

.activity-content p {
    margin: 0;
    font-size: 0.8rem;
    color: #334155;
}

.activity-content p strong {
    color: #1e293b;
}

.activity-content small {
    font-size: 0.6rem;
    color: #94a3b8;
}

.actions-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.actions-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.actions-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
}

.actions-header h5 i {
    margin-right: 0.5rem;
    color: #1a56db;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1.25rem;
}

.action-item {
    background: #f8fafc;
    padding: 1.25rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.action-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    border-color: #1a56db;
    background: white;
}

.action-item i {
    font-size: 1.8rem;
    color: #1a56db;
    margin-bottom: 0.75rem;
    display: block;
}

.action-item span {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.action-item small {
    font-size: 0.6rem;
    color: #64748b;
}

.empty-row td {
    text-align: center;
    padding: 2rem !important;
}

.empty-row i {
    font-size: 2.5rem;
    color: #94a3b8;
    margin-bottom: 0.5rem;
    display: block;
}

.empty-row p {
    color: #64748b;
    margin-bottom: 0.5rem;
}

.empty-row small {
    color: #94a3b8;
    font-size: 0.7rem;
}

.btn-create {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: white;
    padding: 0.4rem 1.2rem;
    text-decoration: none;
    font-size: 0.65rem;
    font-weight: 700;
    display: inline-block;
    margin-top: 0.5rem;
}

@media (max-width: 992px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .actions-grid {
        grid-template-columns: 1fr;
    }
    .table-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('projetsChart');
    if (ctx) {
        const chartData = @json($chartData ?? ['labels' => [], 'data' => []]);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels || ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Projets créés',
                    data: chartData.data || [0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(26, 86, 219, 0.7)',
                    borderColor: '#1a56db',
                    borderWidth: 1,
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
                        ticks: {
                            stepSize: 1
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