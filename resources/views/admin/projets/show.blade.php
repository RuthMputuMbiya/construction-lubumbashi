{{-- resources/views/admin/projets/show.blade.php --}}
{{-- PAGE DÉTAILS PROJET POUR ADMIN --}}

@extends('layouts.admin')

@section('title', 'Détails du projet - Admin')

@section('page-title', 'Détails du projet')
@section('page-subtitle', $projet->nom)

@section('content')
<div class="projet-detail-container">
    
    {{-- ========================================== --}}
    {{-- EN-TÊTE PROJET --}}
    {{-- ========================================== --}}
    <div class="project-header">
        <div class="header-left">
            <div class="project-badges">
                <span class="project-code">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id,4,'0',STR_PAD_LEFT) }}</span>
                @if($projet->statut == 'en_cours')
                    <span class="badge-cours"><i class="fas fa-play-circle"></i> EN COURS</span>
                @elseif($projet->statut == 'termine')
                    <span class="badge-termine"><i class="fas fa-check-circle"></i> TERMINÉ</span>
                @elseif($projet->statut == 'annule')
                    <span class="badge-annule"><i class="fas fa-ban"></i> ANNULÉ</span>
                @else
                    <span class="badge-attente"><i class="fas fa-clock"></i> EN ATTENTE</span>
                @endif
                @if($projet->budget_statut == 'valide')
                    <span class="badge-budget"><i class="fas fa-check-circle"></i> BUDGET VALIDÉ</span>
                @else
                    <span class="badge-budget-attente"><i class="fas fa-clock"></i> BUDGET EN ATTENTE</span>
                @endif
            </div>
            <h1 class="project-title">{{ $projet->nom }}</h1>
            <div class="project-location">
                <i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}
            </div>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.projets.edit', $projet->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> MODIFIER
            </a>
            @if($projet->statut == 'en_attente' || $projet->statut == 'en_cours')
            <button class="btn-cancel" onclick="showCancelModal({{ $projet->id }})">
                <i class="fas fa-ban"></i> ANNULER
            </button>
            @endif
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> EXPORTER
            </button>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES PROJET --}}
    {{-- ========================================== --}}
    <div class="project-stats">
        <div class="stat-item">
            <div class="stat-icon"><i class="fas fa-tasks"></i></div>
            <div class="stat-value">{{ $statsEtapes['total'] }}</div>
            <div class="stat-label">Étapes</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value text-success">{{ $statsEtapes['terminees'] }}</div>
            <div class="stat-label">Terminées</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="fas fa-play-circle"></i></div>
            <div class="stat-value text-primary">{{ $statsEtapes['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-value text-warning">{{ $statsEtapes['non_commence'] }}</div>
            <div class="stat-label">Non commencées</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-value text-danger">{{ $statsEtapes['en_retard'] }}</div>
            <div class="stat-label">En retard</div>
        </div>
    </div>
    
    <div class="row">
        {{-- Colonne gauche --}}
        <div class="col-left">
            {{-- Informations générales --}}
            <div class="info-card">
                <div class="card-title">
                    <i class="fas fa-info-circle"></i> INFORMATIONS
                </div>
                <div class="card-content">
                    <div class="info-row">
                        <span class="info-label">Description</span>
                        <p class="info-text">{{ $projet->description }}</p>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dates</span>
                        <div class="info-dates">
                            <span><i class="fas fa-calendar-alt"></i> Début: {{ \Carbon\Carbon::parse($projet->date_debut_prevue)->format('d/m/Y') }}</span>
                            <span><i class="fas fa-flag-checkered"></i> Fin: {{ \Carbon\Carbon::parse($projet->date_fin_prevue)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Responsable</span>
                        <div class="info-responsable">
                            <div class="resp-avatar">{{ strtoupper(substr($projet->chefProjet->nom ?? 'N/A', 0, 1)) }}</div>
                            <div>
                                <div class="resp-name">{{ $projet->chefProjet->nom ?? 'Non assigné' }}</div>
                                <div class="resp-email">{{ $projet->chefProjet->email ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                    @if($projet->annule_le)
                    <div class="info-row">
                        <span class="info-label">Annulation</span>
                        <div class="info-annulation">
                            <span><i class="fas fa-calendar"></i> Le {{ \Carbon\Carbon::parse($projet->annule_le)->format('d/m/Y') }}</span>
                            <span><i class="fas fa-user"></i> Par {{ $projet->annulePar->nom ?? 'N/A' }}</span>
                            <p class="motif">{{ $projet->motif_annulation }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Budget --}}
            <div class="budget-card">
                <div class="card-title">
                    <i class="fas fa-chart-pie"></i> BUDGET
                </div>
                <div class="card-content">
                    <div class="budget-section">
                        <div class="budget-title">FRANCS CONGOLAIS (FC)</div>
                        <div class="budget-row">
                            <span>Demandé</span>
                            <strong>{{ number_format($budgetInfo['total_fc'], 0, ',', ' ') }} FC</strong>
                        </div>
                        <div class="budget-row">
                            <span>Validé</span>
                            <strong class="text-success">{{ number_format($budgetInfo['valide_fc'], 0, ',', ' ') }} FC</strong>
                        </div>
                        <div class="budget-row">
                            <span>Dépensé</span>
                            <strong class="text-warning">{{ number_format($budgetInfo['depense_fc'], 0, ',', ' ') }} FC</strong>
                        </div>
                        <div class="budget-row">
                            <span>Restant</span>
                            <strong class="text-info">{{ number_format($budgetInfo['valide_fc'] - $budgetInfo['depense_fc'], 0, ',', ' ') }} FC</strong>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $budgetInfo['valide_fc'] > 0 ? round(($budgetInfo['depense_fc'] / $budgetInfo['valide_fc']) * 100, 2) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="budget-section mt-3">
                        <div class="budget-title">DOLLARS AMÉRICAINS (USD)</div>
                        <div class="budget-row">
                            <span>Demandé</span>
                            <strong>{{ number_format($budgetInfo['total_usd'], 0, ',', ' ') }} USD</strong>
                        </div>
                        <div class="budget-row">
                            <span>Validé</span>
                            <strong class="text-success">{{ number_format($budgetInfo['valide_usd'], 0, ',', ' ') }} USD</strong>
                        </div>
                        <div class="budget-row">
                            <span>Dépensé</span>
                            <strong class="text-warning">{{ number_format($budgetInfo['depense_usd'], 0, ',', ' ') }} USD</strong>
                        </div>
                        <div class="budget-row">
                            <span>Restant</span>
                            <strong class="text-info">{{ number_format($budgetInfo['valide_usd'] - $budgetInfo['depense_usd'], 0, ',', ' ') }} USD</strong>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $budgetInfo['valide_usd'] > 0 ? round(($budgetInfo['depense_usd'] / $budgetInfo['valide_usd']) * 100, 2) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    @if($budgetInfo['statut'] == 'valide')
                        <div class="budget-validated">
                            <i class="fas fa-check-circle"></i> Validé le {{ \Carbon\Carbon::parse($budgetInfo['valide_le'])->format('d/m/Y') }} par {{ $budgetInfo['valide_par'] }}
                        </div>
                    @else
                        <div class="budget-alert">
                            <i class="fas fa-clock"></i> Budget en attente de validation
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Colonne droite --}}
        <div class="col-right">
            {{-- Avancement global --}}
            <div class="avancement-card">
                <div class="card-title">
                    <i class="fas fa-chart-line"></i> AVANCEMENT GLOBAL
                </div>
                <div class="card-content">
                    <div class="global-progress">
                        <div class="progress-circle">
                            <svg viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#1a56db" stroke-width="8"
                                        stroke-dasharray="339.292" stroke-dashoffset="{{ 339.292 * (1 - $avancementGlobal/100) }}"
                                        transform="rotate(-90 60 60)"/>
                            </svg>
                            <div class="progress-text">
                                <span class="value">{{ $avancementGlobal }}</span>
                                <span class="unit">%</span>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="info-item">
                                <span class="label">Étapes terminées</span>
                                <span class="value">{{ $statsEtapes['terminees'] }}/{{ $statsEtapes['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Graphique évolution --}}
            <div class="chart-card">
                <div class="card-title">
                    <i class="fas fa-chart-line"></i> ÉVOLUTION DE L'AVANCEMENT
                </div>
                <div class="card-content">
                    <canvas id="evolutionChart" height="250"></canvas>
                </div>
            </div>
            
            {{-- Liste des étapes --}}
            <div class="etapes-card">
                <div class="card-title">
                    <i class="fas fa-tasks"></i> LISTE DES ÉTAPES
                    <span class="card-badge">{{ $statsEtapes['total'] }} étape(s)</span>
                </div>
                <div class="card-content p-0">
                    @forelse($projet->planifications->sortBy('ordre') as $etape)
                    <div class="etape-item">
                        <div class="etape-order">{{ $etape->ordre }}</div>
                        <div class="etape-info">
                            <h6 class="etape-name">{{ $etape->nom_etape }}</h6>
                            <div class="etape-meta">
                                <span><i class="fas fa-user"></i> {{ $etape->chefChantier->nom ?? 'Non assigné' }}</span>
                                <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($etape->date_debut_prevue)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($etape->date_fin_prevue)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="etape-progress">
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: {{ $etape->progression }}%"></div>
                            </div>
                            <span>{{ $etape->progression }}%</span>
                        </div>
                        <div class="etape-status">
                            @if($etape->statut == 'termine')
                                <span class="status-success">Terminée</span>
                            @elseif($etape->statut == 'en_cours')
                                <span class="status-primary">En cours</span>
                            @elseif($etape->statut == 'en_retard')
                                <span class="status-danger">En retard</span>
                            @else
                                <span class="status-secondary">À venir</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="empty-etapes">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Aucune étape planifiée</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal d'annulation --}}
<div id="cancelModal" class="modal-cancel" style="display: none;">
    <div class="modal-cancel-content">
        <div class="modal-cancel-header">
            <h4><i class="fas fa-ban"></i> ANNULER LE PROJET</h4>
            <span class="close" onclick="closeCancelModal()">&times;</span>
        </div>
        <form id="cancelForm" method="POST" action="{{ route('admin.projets.cancel', $projet->id) }}">
            @csrf
            <div class="modal-cancel-body">
                <p>Êtes-vous sûr de vouloir annuler ce projet ?</p>
                <div class="form-group">
                    <label>MOTIF DE L'ANNULATION</label>
                    <textarea name="motif_annulation" class="form-control" rows="3" required placeholder="Veuillez expliquer la raison..."></textarea>
                </div>
                <div class="alert-danger-modal">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Cette action est irréversible.</span>
                </div>
            </div>
            <div class="modal-cancel-footer">
                <button type="button" class="btn-cancel" onclick="closeCancelModal()">RETOUR</button>
                <button type="submit" class="btn-confirm">CONFIRMER L'ANNULATION</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.projet-detail-container { max-width: 1400px; margin: 0 auto; }
.project-header { background: white; border: 1px solid #e2e8f0; padding: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
.project-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem; }
.project-code { background: #f1f5f9; padding: 0.2rem 0.75rem; font-size: 0.7rem; font-family: monospace; }
.badge-cours, .badge-termine, .badge-annule, .badge-attente, .badge-budget, .badge-budget-attente { padding: 0.25rem 0.75rem; font-size: 0.65rem; font-weight: 700; }
.badge-cours { background: #dbeafe; color: #1d4ed8; }
.badge-termine { background: #d1fae5; color: #059669; }
.badge-annule { background: #fee2e2; color: #dc2626; }
.badge-attente { background: #fef3c7; color: #d97706; }
.badge-budget { background: #d1fae5; color: #059669; }
.badge-budget-attente { background: #fef3c7; color: #d97706; }
.project-title { font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem; }
.project-location { color: #64748b; font-size: 0.8rem; }
.btn-edit, .btn-cancel, .btn-print { padding: 0.5rem 1rem; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; border: none; cursor: pointer; }
.btn-edit { background: #dbeafe; color: #1d4ed8; text-decoration: none; display: inline-block; }
.btn-cancel { background: #fee2e2; color: #dc2626; }
.btn-print { background: #f1f5f9; color: #64748b; }
.project-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.stat-item { background: white; border: 1px solid #e2e8f0; padding: 1rem; text-align: center; }
.stat-icon i { font-size: 1.5rem; color: #1a56db; }
.stat-value { font-size: 1.5rem; font-weight: 800; display: block; }
.stat-label { font-size: 0.65rem; color: #64748b; text-transform: uppercase; }
.row { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; }
.info-card, .budget-card, .avancement-card, .chart-card, .etapes-card { background: white; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 1.5rem; }
.card-title { padding: 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; display: flex; justify-content: space-between; align-items: center; }
.card-content { padding: 1rem; }
.info-row { margin-bottom: 1rem; }
.info-label { font-size: 0.65rem; color: #64748b; display: block; margin-bottom: 0.25rem; text-transform: uppercase; }
.info-text { font-size: 0.8rem; color: #1e293b; margin: 0; }
.info-dates { display: flex; gap: 1rem; font-size: 0.75rem; }
.info-responsable { display: flex; align-items: center; gap: 0.75rem; }
.resp-avatar { width: 40px; height: 40px; background: #1a56db; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; }
.budget-section { margin-bottom: 1rem; }
.budget-title { font-size: 0.7rem; font-weight: 700; margin-bottom: 0.5rem; }
.budget-row { display: flex; justify-content: space-between; padding: 0.25rem 0; font-size: 0.75rem; }
.progress-bar { background: #e2e8f0; height: 6px; margin-top: 0.5rem; }
.progress-fill { background: linear-gradient(90deg, #10b981, #1a56db); height: 100%; }
.budget-validated, .budget-alert { margin-top: 1rem; padding: 0.5rem; font-size: 0.7rem; display: flex; align-items: center; gap: 0.5rem; }
.budget-validated { background: #d1fae5; color: #059669; }
.budget-alert { background: #fef3c7; color: #d97706; }
.global-progress { display: flex; align-items: center; justify-content: center; gap: 2rem; }
.progress-circle { position: relative; width: 120px; height: 120px; }
.progress-circle svg { width: 100%; height: 100%; transform: rotate(-90deg); }
.progress-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
.progress-text .value { font-size: 1.5rem; font-weight: 800; }
.etape-item { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; }
.etape-order { width: 30px; height: 30px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; }
.etape-info { flex: 1; }
.etape-name { font-size: 0.85rem; font-weight: 600; margin: 0; }
.etape-meta { display: flex; gap: 1rem; font-size: 0.65rem; color: #64748b; }
.etape-progress { width: 100px; display: flex; align-items: center; gap: 0.5rem; }
.progress-bar-custom { flex: 1; background: #e2e8f0; height: 4px; }
.status-success, .status-primary, .status-danger, .status-secondary { padding: 0.2rem 0.5rem; font-size: 0.6rem; font-weight: 700; }
.status-success { background: #d1fae5; color: #059669; }
.status-primary { background: #dbeafe; color: #1d4ed8; }
.status-danger { background: #fee2e2; color: #dc2626; }
.status-secondary { background: #f1f5f9; color: #64748b; }
.modal-cancel { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-cancel-content { background: white; width: 90%; max-width: 500px; }
.modal-cancel-header { padding: 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.modal-cancel-body { padding: 1.25rem; }
.modal-cancel-footer { padding: 1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem; }
.btn-cancel, .btn-confirm { padding: 0.5rem 1rem; font-size: 0.7rem; font-weight: 700; border: none; cursor: pointer; }
.btn-cancel { background: #f1f5f9; color: #64748b; }
.btn-confirm { background: #dc2626; color: white; }
.empty-etapes { text-align: center; padding: 2rem; }
@media (max-width: 768px) { .row { grid-template-columns: 1fr; } .project-stats { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('evolutionChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($evolutionData['labels']),
                datasets: [{
                    label: 'Avancement (%)',
                    data: @json($evolutionData['data']),
                    borderColor: '#1a56db',
                    backgroundColor: 'rgba(26,86,219,0.05)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }
    
    function showCancelModal(projetId) {
        document.getElementById('cancelModal').style.display = 'flex';
    }
    
    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
</script>
@endpush
@endsection