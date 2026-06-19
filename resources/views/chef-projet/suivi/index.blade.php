@extends('layouts.chef-projet')

@section('title', 'Suivi avancement - Chef de Projet')

@section('page-title', 'Suivi des projets')
@section('page-subtitle', 'Suivez l\'avancement de tous vos projets en temps réel')

@section('content')
<div class="container-fluid">
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES GLOBALES AVEC CALCUL AUTOMATIQUE --}}
    {{-- ========================================== --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">TOTAL PROJETS</h6>
                            <h2 class="mb-0">{{ $stats_globales['total_projets'] }}</h2>
                            <small>
                                {{ $stats_globales['total_projets_reels'] }} projets réels | 
                                {{ $stats_globales['total_planifications'] }} planifications
                            </small>
                        </div>
                        <i class="fas fa-project-diagram fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">ÉTAPES TERMINÉES</h6>
                            <h2 class="mb-0">{{ $stats_globales['etapes_terminees'] }}/{{ $stats_globales['total_etapes'] }}</h2>
                            <small>{{ $stats_globales['total_etapes'] > 0 ? round(($stats_globales['etapes_terminees'] / $stats_globales['total_etapes']) * 100) : 0 }}% de complétion</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">EN COURS</h6>
                            <h2 class="mb-0">{{ $stats_globales['etapes_en_cours'] }}</h2>
                            <small>{{ $stats_globales['projets_en_cours'] }} projets actifs</small>
                        </div>
                        <i class="fas fa-play-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">EN RETARD</h6>
                            <h2 class="mb-0">{{ $stats_globales['etapes_en_retard'] }}</h2>
                            <small>{{ $stats_globales['etapes_en_retard'] > 0 ? 'Attention !' : 'Tout est à jour' }}</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Barre de progression globale --}}
    <div class="card mb-4" style="border-top: 3px solid #1a56db;">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold" style="color: #1a56db;">Avancement global de tous les projets</span>
                <span class="fw-bold" style="color: #1a56db;">{{ $stats_globales['avancement_moyen'] }}%</span>
            </div>
            <div class="progress" style="height: 25px; background-color: #e2e8f0;">
                <div class="progress-bar" style="width: {{ $stats_globales['avancement_moyen'] }}%; background: linear-gradient(90deg, #1a56db, #0e3a8a);">
                    {{ $stats_globales['avancement_moyen'] }}%
                </div>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- BARRE DE RECHERCHE ET FILTRES --}}
    {{-- ========================================== --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text" style="background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white;"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un projet...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="all">📋 Tous les statuts</option>
                        <option value="en_cours">▶️ En cours</option>
                        <option value="termine">✅ Terminé</option>
                        <option value="en_attente">⏳ En attente</option>
                        <option value="planification">📋 Planification</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="avancementFilter" class="form-select">
                        <option value="all">📊 Tous avancements</option>
                        <option value="0">0% - À démarrer</option>
                        <option value="25">1% - 25%</option>
                        <option value="50">26% - 50%</option>
                        <option value="75">51% - 75%</option>
                        <option value="100">76% - 100%</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="refreshBtn" class="btn w-100" style="background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white; border: none;">
                        <i class="fas fa-sync-alt me-2"></i> RAFRAÎCHIR
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- LISTE DE TOUS LES PROJETS --}}
    {{-- ========================================== --}}
    <div id="projectsContainer">
        @forelse($projets as $projet)
            @php
                $avancement = $projet->avancement ?? 0;
                
                // Couleur de la barre de progression
                if($avancement >= 100) {
                    $couleurBarre = 'bg-success';
                } elseif($avancement >= 75) {
                    $couleurBarre = 'bg-info';
                } elseif($avancement >= 50) {
                    $couleurBarre = 'bg-primary';
                } elseif($avancement >= 25) {
                    $couleurBarre = 'bg-warning';
                } else {
                    $couleurBarre = 'bg-secondary';
                }
                
                $badgeClass = $projet->badge_class ?? 'bg-secondary';
                $statutLabel = $projet->statut_label ?? 'EN ATTENTE';
            @endphp
            <div class="card mb-4 project-card" 
                 data-statut="{{ $projet->statut_affichage ?? $projet->statut }}" 
                 data-nom="{{ strtolower($projet->nom) }}"
                 data-avancement="{{ $avancement }}"
                 style="border-left: 4px solid #1a56db;">
                
                <div class="card-header {{ $badgeClass }} text-white" style="background: linear-gradient(135deg, #1a56db, #0e3a8a);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="mb-0">
                                @if($projet->est_planification && !$projet->planification_validee)
                                    <i class="fas fa-clipboard-list me-2"></i>
                                @else
                                    <i class="fas fa-project-diagram me-2"></i>
                                @endif
                                {{ $projet->nom }}
                            </h5>
                            <small><i class="fas fa-map-marker-alt me-1"></i> {{ $projet->lieu }}</small>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <span class="badge bg-light text-dark me-2">
                                {{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="badge bg-white text-dark">
                                <i class="fas {{ $projet->est_planification && !$projet->planification_validee ? 'fa-clipboard-list' : 'fa-play-circle' }} me-1"></i> 
                                {{ $statutLabel }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Informations générales --}}
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="bg-light p-2 rounded" style="border-left: 3px solid #1a56db;">
                                <small class="text-muted">Date début</small><br>
                                <i class="fas fa-calendar-alt me-1" style="color: #1a56db;"></i> 
                                <strong>{{ Carbon\Carbon::parse($projet->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="bg-light p-2 rounded" style="border-left: 3px solid #1a56db;">
                                <small class="text-muted">Date fin prévue</small><br>
                                <i class="fas fa-flag-checkered me-1" style="color: #1a56db;"></i> 
                                <strong>{{ Carbon\Carbon::parse($projet->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="bg-light p-2 rounded" style="border-left: 3px solid #1a56db;">
                                <small class="text-muted">Budget</small><br>
                                <i class="fas fa-money-bill-wave me-1" style="color: #1a56db;"></i> 
                                <strong>{{ number_format($projet->budget_previsionnel ?? $projet->budget_total, 0, ',', ' ') }} {{ $projet->devise ?? 'FC' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="bg-light p-2 rounded" style="border-left: 3px solid #1a56db;">
                                <small class="text-muted">Avancement</small><br>
                                <i class="fas fa-chart-line me-1" style="color: #1a56db;"></i> 
                                <strong class="text-{{ $avancement >= 100 ? 'success' : ($avancement >= 50 ? 'info' : 'warning') }}">
                                    {{ $avancement }}%
                                </strong>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barre de progression --}}
                    <div class="mb-3">
                        <div class="progress" style="height: 20px; background-color: #e2e8f0;">
                            <div class="progress-bar {{ $couleurBarre }} fs-6 fw-bold d-flex align-items-center justify-content-center" 
                                 style="width: {{ $avancement }}%">
                                {{ $avancement }}%
                            </div>
                        </div>
                    </div>
                    
                    {{-- Statistiques des étapes --}}
                    <div class="row mb-3 text-center">
                        <div class="col-3">
                            <div class="bg-light p-2 rounded">
                                <div class="fs-4 fw-bold" style="color: #1a56db;">{{ $projet->total_etapes }}</div>
                                <small class="text-muted">Total étapes</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-light p-2 rounded">
                                <div class="fs-4 fw-bold text-success">{{ $projet->etapes_terminees }}</div>
                                <small class="text-muted">Terminées</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-light p-2 rounded">
                                <div class="fs-4 fw-bold text-warning">{{ $projet->etapes_en_cours }}</div>
                                <small class="text-muted">En cours</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-light p-2 rounded">
                                <div class="fs-4 fw-bold text-danger">{{ $projet->etapes_en_retard }}</div>
                                <small class="text-muted">En retard</small>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
                
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($projet->est_planification && !$projet->planification_validee)
                                <span class="badge" style="background: #1a56db;">Planification en cours</span>
                            @elseif($projet->budget_statut != 'valide')
                                <span class="badge bg-warning me-2">Budget non validé</span>
                            @endif
                            @if($projet->etapes_en_retard > 0)
                                <span class="badge bg-danger">{{ $projet->etapes_en_retard }} étape(s) en retard</span>
                            @endif
                        </div>
                        <a href="{{ route('chef-projet.projets.show', $projet->id) }}" class="btn btn-sm" style="background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white;">
                            <i class="fas fa-eye me-2"></i> VOIR LES DÉTAILS
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert text-center py-5" style="background: #dbeafe; color: #1a56db; border: none;">
                <i class="fas fa-chart-line fa-4x mb-3"></i>
                <h5>Aucun projet trouvé</h5>
                <p>Commencez par créer une planification de projet.</p>
                <a href="{{ route('chef-projet.planification.create') }}" class="btn mt-2" style="background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white;">
                    <i class="fas fa-plus-circle me-2"></i> CRÉER UN PROJET
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.card-header {
    border-bottom: none;
}
.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}
.badge {
    font-size: 0.7rem;
    padding: 0.35rem 0.8rem;
    border-radius: 20px;
}
.project-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 16px;
    overflow: hidden;
}
.project-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(26, 86, 219, 0.15);
}
</style>
@endpush

@push('scripts')
<script>
// Rafraîchir la page
document.getElementById('refreshBtn').addEventListener('click', function() {
    location.reload();
});

// Filtre par statut
document.getElementById('statusFilter').addEventListener('change', function() {
    const valeur = this.value;
    const cards = document.querySelectorAll('.project-card');
    cards.forEach(card => {
        const statut = card.getAttribute('data-statut');
        if (valeur === 'all' || statut === valeur) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Filtre par avancement
document.getElementById('avancementFilter').addEventListener('change', function() {
    const valeur = this.value;
    const cards = document.querySelectorAll('.project-card');
    cards.forEach(card => {
        const avancement = parseInt(card.getAttribute('data-avancement'));
        if (valeur === 'all') {
            card.style.display = '';
        } else if (valeur === '0' && avancement === 0) {
            card.style.display = '';
        } else if (valeur === '25' && avancement > 0 && avancement <= 25) {
            card.style.display = '';
        } else if (valeur === '50' && avancement > 25 && avancement <= 50) {
            card.style.display = '';
        } else if (valeur === '75' && avancement > 50 && avancement <= 75) {
            card.style.display = '';
        } else if (valeur === '100' && avancement > 75 && avancement <= 100) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Recherche
document.getElementById('searchInput').addEventListener('keyup', function() {
    const terme = this.value.toLowerCase();
    const cards = document.querySelectorAll('.project-card');
    cards.forEach(card => {
        const nom = card.getAttribute('data-nom');
        if (nom.includes(terme)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Rafraîchissement automatique toutes les 30 secondes
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush
@endsection