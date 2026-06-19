@extends('layouts.chef-chantier')

@section('title', 'Mes étapes - Chef de Chantier')

@section('page-title', 'Suivi avancement')
@section('page-subtitle', 'Sélectionnez un projet pour suivre l')

@section('content')
<div class="etapes-container">
    
    {{-- ========================================== --}}
    {{-- EN-TÊTE AVEC STATISTIQUES --}}
    {{-- ========================================== --}}
    <div class="stats-header">
        <div class="stats-cards">
            <div class="stat-total">
                <span class="stat-number">{{ $stats['toutes'] }}</span>
                <span class="stat-label">TOTAL ÉTAPES</span>
                <div class="stat-progress"></div>
            </div>
            <div class="stat-termine">
                <span class="stat-number">{{ $stats['terminees'] }}</span>
                <span class="stat-label">TERMINÉES</span>
                <div class="stat-progress success"></div>
            </div>
            <div class="stat-cours">
                <span class="stat-number">{{ $stats['en_cours'] }}</span>
                <span class="stat-label">EN COURS</span>
                <div class="stat-progress warning"></div>
            </div>
            <div class="stat-retard">
                <span class="stat-number">{{ $stats['en_retard'] }}</span>
                <span class="stat-label">EN RETARD</span>
                <div class="stat-progress danger"></div>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- LISTE DES PROJETS (STYLE DOSSIER) --}}
    {{-- ========================================== --}}
    
    @php
        $etapesParProjet = $etapes->groupBy(function($etape) {
            return $etape->projet->id;
        });
    @endphp
    
    <div class="projets-dossiers">
        @forelse($etapesParProjet as $projetId => $etapesGroupe)
            @php
                $projet = $etapesGroupe->first()->projet;
                $totalEtapes = $etapesGroupe->count();
                $terminees = $etapesGroupe->where('statut', 'termine')->count();
                $enCours = $etapesGroupe->where('statut', 'en_cours')->count();
                $enRetard = $etapesGroupe->where('statut', 'en_retard')->count();
                $avancementProjet = $totalEtapes > 0 ? round(($terminees / $totalEtapes) * 100, 1) : 0;
                
                if ($avancementProjet >= 100) {
                    $couleur = 'success';
                    $iconeProjet = 'fa-check-circle';
                } elseif ($avancementProjet >= 50) {
                    $couleur = 'warning';
                    $iconeProjet = 'fa-chart-line';
                } elseif ($enRetard > 0) {
                    $couleur = 'danger';
                    $iconeProjet = 'fa-exclamation-triangle';
                } else {
                    $couleur = 'primary';
                    $iconeProjet = 'fa-folder-open';
                }
                
                $projetUniqueId = 'projet_' . $projetId;
            @endphp
            
            <div class="dossier-projet" data-projet-id="{{ $projetId }}">
                <div class="dossier-header" onclick="toggleProjet('{{ $projetUniqueId }}')">
                    <div class="dossier-icon {{ $couleur }}">
                        <i class="fas {{ $iconeProjet }}"></i>
                    </div>
                    <div class="dossier-info">
                        <h3 class="dossier-nom">{{ $projet->nom }}</h3>
                        <div class="dossier-lieu">
                            <i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}
                        </div>
                    </div>
                    <div class="dossier-stats">
                        <div class="stat-pill {{ $couleur }}">
                            <span class="stat-pourcent">{{ $avancementProjet }}%</span>
                        </div>
                        <div class="stat-pill info">
                            <i class="fas fa-tasks"></i>
                            <span>{{ $totalEtapes }} étapes</span>
                        </div>
                        <div class="stat-pill success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $terminees }} terminées</span>
                        </div>
                        @if($enRetard > 0)
                        <div class="stat-pill danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>{{ $enRetard }} retard</span>
                        </div>
                        @endif
                        <div class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
                
                <div class="dossier-body" id="{{ $projetUniqueId }}" style="display: none;">
                    {{-- Barre d'avancement du projet --}}
                    <div class="projet-avancement">
                        <div class="avancement-label">
                            <span>Avancement du projet</span>
                            <span class="avancement-value">{{ $avancementProjet }}%</span>
                        </div>
                        <div class="avancement-bar">
                            <div class="avancement-fill" style="width: {{ $avancementProjet }}%"></div>
                        </div>
                    </div>
                    
                    {{-- Filtres rapides --}}
                    <div class="etapes-filters">
                        <button class="filter-etape-btn active" data-filter="all" onclick="filterEtapes('{{ $projetUniqueId }}', 'all', this)">📋 Toutes</button>
                        <button class="filter-etape-btn" data-filter="en_cours" onclick="filterEtapes('{{ $projetUniqueId }}', 'en_cours', this)">▶️ En cours</button>
                        <button class="filter-etape-btn" data-filter="termine" onclick="filterEtapes('{{ $projetUniqueId }}', 'termine', this)">✅ Terminées</button>
                        <button class="filter-etape-btn" data-filter="en_retard" onclick="filterEtapes('{{ $projetUniqueId }}', 'en_retard', this)">⚠️ En retard</button>
                        <button class="filter-etape-btn" data-filter="a_venir" onclick="filterEtapes('{{ $projetUniqueId }}', 'a_venir', this)">⏳ Non commencées</button>
                    </div>
                    
                    {{-- Liste des étapes --}}
                    <div class="etapes-list-container">
                        @foreach($etapesGroupe->sortBy('ordre') as $etape)
                        <div class="etape-card" data-etape-statut="{{ $etape->statut }}">
                            <div class="etape-status">
                                @if($etape->statut == 'termine')
                                    <span class="status-badge success"><i class="fas fa-check-circle"></i> TERMINÉE</span>
                                @elseif($etape->statut == 'en_cours')
                                    <span class="status-badge primary"><i class="fas fa-play-circle"></i> EN COURS</span>
                                @elseif($etape->statut == 'en_retard')
                                    <span class="status-badge danger"><i class="fas fa-exclamation-triangle"></i> EN RETARD</span>
                                @else
                                    <span class="status-badge secondary"><i class="fas fa-clock"></i> NON COMMENCÉE</span>
                                @endif
                            </div>
                            
                            <div class="etape-content">
                                <h5 class="etape-title">{{ $etape->nom_etape }}</h5>
                                <p class="etape-description">{{ Str::limit($etape->description, 100) }}</p>
                                
                                <div class="etape-meta">
                                    <span><i class="fas fa-calendar-alt"></i> 
                                        {{ \Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('D MMMM YYYY') }}
                                    </span>
                                    <span><i class="fas fa-arrow-right"></i></span>
                                    <span><i class="fas fa-flag-checkered"></i> 
                                        {{ \Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('D MMMM YYYY') }}
                                    </span>
                                </div>
                                
                                <div class="progress-section">
                                    <div class="progress-header">
                                        <span>AVANCEMENT</span>
                                        <span class="progress-percent">{{ $etape->progression }}%</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-fill" style="width: {{ $etape->progression }}%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="etape-actions">
                                <a href="{{ route('chef-chantier.etapes.show', $etape->id) }}" class="btn-view" title="Voir détails">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                @if($etape->statut !== 'termine')
                                    <a href="{{ route('chef-chantier.etapes.edit', $etape->id) }}" class="btn-update">
                                        <i class="fas fa-sync-alt"></i> Mettre à jour
                                    </a>
                                    <button onclick="terminerEtape({{ $etape->id }}, '{{ addslashes($etape->nom_etape) }}', {{ $etape->ordre }})" 
                                            class="btn-terminer">
                                        <i class="fas fa-check-double"></i> TERMINER
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open fa-4x mb-3"></i>
                <h3>AUCUN PROJET ASSIGNÉ</h3>
                <p>Vous n'avez pas encore été assigné à des projets</p>
                <p class="text-muted">Contactez votre chef de projet pour être assigné à des étapes</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Formulaire pour terminer --}}
<form id="terminer-form" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

@push('styles')
<style>
/* ============================================
   DESIGN PROFESSIONNEL POUR CHEF DE CHANTIER
   ============================================ */

:root {
    --primary: #1e3a5f;
    --primary-dark: #0f2b4a;
    --primary-light: #2a4a7a;
    --primary-soft: #e8f0fe;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --dark: #1e293b;
    --gray: #64748b;
    --gray-light: #f8fafc;
    --gray-border: #e2e8f0;
    --white: #ffffff;
    --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 12px 35px rgba(30, 58, 95, 0.12);
}

.etapes-container {
    max-width: 1400px;
    margin: 0 auto;
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ==========================================
   STATISTIQUES EN-TÊTE
   ========================================== */
.stats-header {
    margin-bottom: 2rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.stat-total, .stat-termine, .stat-cours, .stat-retard {
    background: var(--white);
    padding: 1rem 1.25rem;
    border-radius: 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
    border: 1px solid var(--gray-border);
}

.stat-total:hover, .stat-termine:hover, .stat-cours:hover, .stat-retard:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-sm);
    border-color: var(--primary);
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.2;
}

.stat-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--gray);
    margin-top: 0.25rem;
}

.stat-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
}

.stat-progress.success {
    background: linear-gradient(90deg, var(--success), #34d399);
}

.stat-progress.warning {
    background: linear-gradient(90deg, var(--warning), #fbbf24);
}

.stat-progress.danger {
    background: linear-gradient(90deg, var(--danger), #f87171);
}

.stat-total .stat-number { color: var(--primary); }
.stat-termine .stat-number { color: var(--success); }
.stat-cours .stat-number { color: var(--warning); }
.stat-retard .stat-number { color: var(--danger); }

/* ==========================================
   DOSSIERS PROJETS
   ========================================== */
.projets-dossiers {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.dossier-projet {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s;
}

.dossier-projet:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-hover);
}

.dossier-header {
    background: linear-gradient(135deg, #fafbfc, var(--white));
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.dossier-header:hover {
    background: var(--primary-soft);
}

.dossier-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.dossier-icon.primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
.dossier-icon.success { background: linear-gradient(135deg, var(--success), #059669); color: white; }
.dossier-icon.warning { background: linear-gradient(135deg, var(--warning), #d97706); color: white; }
.dossier-icon.danger { background: linear-gradient(135deg, var(--danger), #dc2626); color: white; }

.dossier-icon i {
    font-size: 1.3rem;
}

.dossier-info {
    flex: 1;
}

.dossier-nom {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 0.25rem 0;
    color: var(--dark);
}

.dossier-lieu {
    font-size: 0.7rem;
    color: var(--gray);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.dossier-stats {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.9rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.stat-pill.primary { background: var(--primary-soft); color: var(--primary); }
.stat-pill.success { background: #d1fae5; color: #059669; }
.stat-pill.danger { background: #fee2e2; color: #dc2626; }
.stat-pill.warning { background: #fef3c7; color: #d97706; }
.stat-pill.info { background: #f1f5f9; color: var(--gray); }

.stat-pourcent {
    font-size: 0.85rem;
    font-weight: 800;
}

.toggle-icon i {
    font-size: 1rem;
    color: var(--gray);
    transition: transform 0.3s;
}

.dossier-header.open .toggle-icon i {
    transform: rotate(180deg);
}

/* ==========================================
   DOSSIER BODY
   ========================================== */
.dossier-body {
    border-top: 1px solid var(--gray-border);
    padding: 1.25rem;
    background: var(--white);
}

.projet-avancement {
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-border);
}

.avancement-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.avancement-value {
    font-weight: 800;
    color: var(--primary);
}

.avancement-bar {
    background: var(--gray-border);
    height: 6px;
    border-radius: 10px;
    overflow: hidden;
}

.avancement-fill {
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* ==========================================
   FILTRES
   ========================================== */
.etapes-filters {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}

.filter-etape-btn {
    padding: 0.35rem 1rem;
    font-size: 0.65rem;
    font-weight: 700;
    background: var(--gray-light);
    border: 1px solid var(--gray-border);
    border-radius: 40px;
    color: var(--gray);
    cursor: pointer;
    transition: all 0.3s;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-etape-btn:hover {
    background: var(--primary-soft);
    border-color: var(--primary);
    color: var(--primary);
}

.filter-etape-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

/* ==========================================
   ÉTAPES
   ========================================== */
.etapes-list-container {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.etape-card {
    background: var(--gray-light);
    padding: 1rem;
    border-radius: 16px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    transition: all 0.3s;
    border: 1px solid transparent;
}

.etape-card:hover {
    background: white;
    border-color: var(--primary-light);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 1rem;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 40px;
}

.status-badge.success { background: #d1fae5; color: #059669; }
.status-badge.primary { background: var(--primary-soft); color: var(--primary); }
.status-badge.danger { background: #fee2e2; color: #dc2626; }
.status-badge.secondary { background: #f1f5f9; color: var(--gray); }

.etape-content {
    flex: 2;
    min-width: 250px;
}

.etape-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 0.3rem 0;
}

.etape-description {
    font-size: 0.75rem;
    color: var(--gray);
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.etape-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.65rem;
    color: var(--gray);
}

.etape-meta i {
    color: var(--primary);
}

.progress-section {
    margin-top: 0.25rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.65rem;
    margin-bottom: 0.3rem;
    color: var(--gray);
    font-weight: 700;
}

.progress-percent {
    font-weight: 800;
    color: var(--primary);
}

.progress-bar-container {
    background: var(--gray-border);
    height: 6px;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* ==========================================
   BOUTONS ACTIONS
   ========================================== */
.etape-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.btn-view, .btn-update, .btn-terminer {
    padding: 0.35rem 1rem;
    font-size: 0.65rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 40px;
    cursor: pointer;
}

.btn-view {
    background: white;
    border: 1px solid var(--gray-border);
    color: var(--gray);
}

.btn-view:hover {
    background: var(--primary-soft);
    border-color: var(--primary);
    color: var(--primary);
}

.btn-update {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border: none;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3);
}

.btn-terminer {
    background: linear-gradient(135deg, var(--success), #059669);
    color: white;
    border: none;
}

.btn-terminer:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* ==========================================
   ÉTAT VIDE
   ========================================== */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 24px;
}

.empty-state i {
    color: var(--primary);
    opacity: 0.4;
}

.empty-state h3 {
    font-size: 1rem;
    font-weight: 800;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.empty-state p {
    font-size: 0.75rem;
    color: var(--gray);
    margin-bottom: 0.25rem;
}

.text-muted {
    color: var(--gray);
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .dossier-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .dossier-stats {
        width: 100%;
        justify-content: flex-start;
    }
    
    .etape-card {
        flex-direction: column;
    }
    
    .etape-status {
        width: 100%;
    }
    
    .etape-actions {
        width: 100%;
        justify-content: flex-end;
    }
}

@media (max-width: 480px) {
    .etape-actions {
        flex-direction: column;
    }
    
    .btn-view, .btn-update, .btn-terminer {
        width: 100%;
        justify-content: center;
    }
    
    .etapes-filters {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleProjet(projetId) {
    const body = document.getElementById(projetId);
    const header = body.previousElementSibling;
    
    if (body.style.display === 'none') {
        body.style.display = 'block';
        header.classList.add('open');
    } else {
        body.style.display = 'none';
        header.classList.remove('open');
    }
}

function filterEtapes(projetId, statut, button) {
    const container = document.getElementById(projetId);
    const etapes = container.querySelectorAll('.etape-card');
    const buttons = container.querySelectorAll('.filter-etape-btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    
    etapes.forEach(etape => {
        const etapeStatut = etape.getAttribute('data-etape-statut');
        if (statut === 'all' || etapeStatut === statut) {
            etape.style.display = '';
            etape.style.animation = 'fadeInUp 0.3s ease-out';
        } else {
            etape.style.display = 'none';
        }
    });
}

function terminerEtape(etapeId, etapeNom, ordre) {
    let message = `✅ Voulez-vous marquer l'étape "${etapeNom}" comme TERMINÉE ?\n\n`;
    message += `Cette action mettra la progression à 100%.\n`;
    
    if (ordre == 5) {
        message += `\n🏆 C'est la DERNIÈRE ÉTAPE (Validation) !\nLe projet sera automatiquement marqué comme TERMINÉ !`;
    }
    
    if(confirm(message)) {
        const form = document.getElementById('terminer-form');
        form.action = '/chef-chantier/etapes/' + etapeId + '/terminer';
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const firstDossier = document.querySelector('.dossier-body');
    if (firstDossier) {
        firstDossier.style.display = 'block';
        firstDossier.previousElementSibling.classList.add('open');
    }
});
</script>
@endpush
@endsection