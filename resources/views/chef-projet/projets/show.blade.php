@extends('layouts.chef-projet')

@section('title', 'Détails du projet - Chef de Projet')

@section('page-title', 'Détails du projet')
@section('page-subtitle', $projet->nom ?? 'Projet')

@section('content')
<div class="projet-detail-container">
    
    {{-- Bouton retour --}}
    <div class="retour-wrapper">
        <a href="{{ route('chef-projet.projets.index') }}" class="btn-retour">
            <i class="fas fa-arrow-left"></i> Retour aux projets
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- EN-TÊTE DU PROJET --}}
    {{-- ========================================== --}}
    <div class="projet-header">
        <div class="projet-header-left">
            <div class="icone-projet">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div>
                <h2 class="titre-projet">{{ $projet->nom ?? 'Projet' }}</h2>
                <div class="meta-projet">
                    <span><i class="fas fa-code-branch"></i> {{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu ?? 'N/A' }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ isset($projet->created_at) ? Carbon\Carbon::parse($projet->created_at)->locale('fr')->isoFormat('D MMM YYYY à HH:mm') : 'N/A' }}</span>
                    <span><i class="fas fa-user-tie"></i> {{ $projet->responsable ?? Auth::user()->nom ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="projet-header-right">
            @if(isset($projet->statut))
                @if($projet->statut == 'en_cours')
                    <span class="badge-statut badge-encours"><i class="fas fa-play-circle"></i> EN COURS</span>
                @elseif($projet->statut == 'termine')
                    <span class="badge-statut badge-termine"><i class="fas fa-check-circle"></i> TERMINÉ</span>
                @elseif($projet->statut == 'annule')
                    <span class="badge-statut badge-annule"><i class="fas fa-ban"></i> ANNULÉ</span>
                @else
                    <span class="badge-statut badge-attente"><i class="fas fa-clock"></i> EN ATTENTE</span>
                @endif
            @endif
            @if(isset($estEnRetard) && $estEnRetard)
                <span class="badge-statut badge-retard"><i class="fas fa-exclamation-triangle"></i> RETARD ({{ $joursRetard }}j)</span>
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- GRILLE PRINCIPALE --}}
    {{-- ========================================== --}}
    <div class="detail-grid">

        {{-- Colonne gauche --}}
        <div class="col-gauche">

            {{-- INFORMATIONS GÉNÉRALES --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-info-circle"></i> INFORMATIONS GÉNÉRALES
                </div>
                <div class="carte-corps">
                    <div class="ligne-info">
                        <span class="label">NOM DU PROJET</span>
                        <span class="valeur">{{ $projet->nom ?? 'N/A' }}</span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">LIEU</span>
                        <span class="valeur"><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu ?? 'N/A' }}</span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">RESPONSABLE</span>
                        <span class="valeur"><i class="fas fa-user-tie"></i> {{ $projet->responsable ?? Auth::user()->nom ?? 'N/A' }}</span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">TYPE DE PROJET</span>
                        <span class="valeur">
                            @if(isset($projet->type_projet))
                                @if($projet->type_projet == 'batiment') 🏢 Bâtiment
                                @elseif($projet->type_projet == 'route') 🛣️ Route
                                @elseif($projet->type_projet == 'pont') 🌉 Pont
                                @else 📐 Autre
                                @endif
                            @else N/A
                            @endif
                        </span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">PRIORITÉ</span>
                        <span class="valeur">
                            @if(isset($projet->priorite))
                                @if($projet->priorite == 'haute') 
                                    <span class="priorite-haute">🔴 Haute</span>
                                @elseif($projet->priorite == 'moyenne') 
                                    <span class="priorite-moyenne">🟡 Moyenne</span>
                                @else 
                                    <span class="priorite-basse">🟢 Basse</span>
                                @endif
                            @else 
                                <span class="priorite-moyenne">🟡 Moyenne</span>
                            @endif
                        </span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">BUDGET</span>
                        <span class="valeur budget">
                            <i class="fas fa-money-bill-wave"></i> {{ number_format($projet->budget_total ?? 0, 0, ',', ' ') }} FC
                        </span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">STATUT</span>
                        <span class="valeur">
                            @if(isset($projet->statut))
                                @if($projet->statut == 'en_cours')
                                    <span class="petit-badge success">En cours</span>
                                @elseif($projet->statut == 'termine')
                                    <span class="petit-badge success">Terminé</span>
                                @elseif($projet->statut == 'annule')
                                    <span class="petit-badge danger">Annulé</span>
                                @else
                                    <span class="petit-badge warning">En attente</span>
                                @endif
                            @else
                                <span class="petit-badge warning">En attente</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- CALENDRIER --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-calendar-alt"></i> CALENDRIER
                </div>
                <div class="carte-corps">
                    <div class="cal-grid">
                        <div class="cal-item">
                            <span class="cal-label"><i class="fas fa-calendar-day"></i> DATE DÉBUT</span>
                            <span class="cal-value">{{ isset($projet->date_debut_prevue) ? Carbon\Carbon::parse($projet->date_debut_prevue)->locale('fr')->isoFormat('D MMMM YYYY') : 'N/A' }}</span>
                        </div>
                        <div class="cal-item">
                            <span class="cal-label"><i class="fas fa-flag-checkered"></i> DATE FIN</span>
                            <span class="cal-value">{{ isset($projet->date_fin_prevue) ? Carbon\Carbon::parse($projet->date_fin_prevue)->locale('fr')->isoFormat('D MMMM YYYY') : 'N/A' }}</span>
                        </div>
                        <div class="cal-item full">
                            <span class="cal-label"><i class="fas fa-hourglass-half"></i> DURÉE</span>
                            <span class="cal-value">
                                @if(isset($projet->date_debut_prevue) && isset($projet->date_fin_prevue))
                                    @php
                                        $debut = Carbon\Carbon::parse($projet->date_debut_prevue);
                                        $fin = Carbon\Carbon::parse($projet->date_fin_prevue);
                                    @endphp
                                    {{ $debut->diffInDays($fin) }} jour(s)
                                @else N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-align-left"></i> DESCRIPTION
                </div>
                <div class="carte-corps">
                    <p class="desc-texte">{{ $projet->description ?? 'Aucune description' }}</p>
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="col-droite">

            {{-- AVANCEMENT GLOBAL --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-chart-line"></i> AVANCEMENT GLOBAL
                </div>
                <div class="carte-corps">
                    <div class="avancement-box">
                        <div class="avancement-header">
                            <span>Progression</span>
                            <span class="avancement-pct">{{ $avancementGlobal ?? 0 }}%</span>
                        </div>
                        <div class="avancement-bar">
                            <div class="avancement-fill" style="width: {{ $avancementGlobal ?? 0 }}%"></div>
                        </div>
                        <div class="avancement-stats">
                            <div class="stat">
                                <span class="stat-nb">{{ $statsEtapes['total'] ?? 0 }}</span>
                                <span class="stat-lb">Total étapes</span>
                            </div>
                            <div class="stat">
                                <span class="stat-nb">{{ $statsEtapes['terminees'] ?? 0 }}</span>
                                <span class="stat-lb">Terminées</span>
                            </div>
                            <div class="stat">
                                <span class="stat-nb">{{ $statsEtapes['en_cours'] ?? 0 }}</span>
                                <span class="stat-lb">En cours</span>
                            </div>
                            <div class="stat">
                                <span class="stat-nb">{{ $statsEtapes['a_venir'] ?? 0 }}</span>
                                <span class="stat-lb">À venir</span>
                            </div>
                        </div>
                    </div>
                    @if(isset($estEnRetard) && $estEnRetard)
                        <div class="alerte-retard">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>⚠️ Projet en retard de <strong>{{ $joursRetard ?? 0 }} jour(s)</strong></span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ÉTAPES PLANIFIÉES --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-tasks"></i> ÉTAPES PLANIFIÉES ({{ $etapes->count() ?? 0 }})
                </div>
                <div class="carte-corps etapes-wrapper">
                    @if(isset($etapes) && $etapes->count() > 0)
                        @foreach($etapes as $etape)
                        <div class="etape-block">
                            <div class="etape-num {{ $etape->progression >= 100 ? 'done' : ($etape->progression > 0 ? 'progress' : 'wait') }}">
                                @if($etape->progression >= 100)
                                    <i class="fas fa-check"></i>
                                @elseif($etape->progression > 0)
                                    <i class="fas fa-play"></i>
                                @else
                                    {{ str_pad($etape->ordre ?? 0, 2, '0', STR_PAD_LEFT) }}
                                @endif
                            </div>
                            <div class="etape-body">
                                <div class="etape-head">
                                    <span class="etape-name">{{ $etape->nom_etape ?? 'Étape' }}</span>
                                    <span class="etape-status {{ $etape->progression >= 100 ? 'done' : ($etape->progression > 0 ? 'progress' : 'wait') }}">
                                        @if($etape->progression >= 100) 
                                            <i class="fas fa-check-circle"></i> Terminée
                                        @elseif($etape->progression > 0) 
                                            <i class="fas fa-play-circle"></i> {{ $etape->progression }}%
                                        @else 
                                            <i class="fas fa-clock"></i> À venir
                                        @endif
                                    </span>
                                </div>
                                <div class="etape-desc">{{ Str::limit($etape->description ?? '', 80) }}</div>
                                <div class="etape-dates">
                                    <span><i class="fas fa-calendar-alt"></i> {{ isset($etape->date_debut_prevue) ? Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                                    <span><i class="fas fa-arrow-right"></i></span>
                                    <span><i class="fas fa-calendar-check"></i> {{ isset($etape->date_fin_prevue) ? Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                                </div>
                                <div class="etape-progress-bar">
                                    <div class="etape-progress-fill" style="width: {{ $etape->progression ?? 0 }}%"></div>
                                </div>
                                <div class="etape-chef">
                                    <i class="fas fa-user-hard-hat"></i> 
                                    <span class="chef-nom">{{ $etape->chefChantier->nom ?? 'Non assigné' }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-etapes">
                            <i class="fas fa-tasks"></i>
                            <p>Aucune étape planifiée</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ACTIONS --}}
            @if(isset($projet->statut) && $projet->statut == 'en_attente')
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-cogs"></i> ACTIONS
                </div>
                <div class="carte-corps">
                    <form action="{{ route('chef-projet.projets.validate-budget', $projet->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-action btn-valider">
                            <i class="fas fa-check-circle"></i> VALIDER LE BUDGET
                        </button>
                    </form>
                    <form action="{{ route('chef-projet.projets.cancel', $projet->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn-action btn-annuler" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce projet ?')">
                            <i class="fas fa-ban"></i> ANNULER LE PROJET
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.projet-detail-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 10px;
}

/* ==========================================
   BOUTON RETOUR
   ========================================== */
.retour-wrapper {
    margin-bottom: 15px;
}
.btn-retour {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-retour:hover {
    border-color: #1a56db;
    color: #1a56db;
}

/* ==========================================
   EN-TÊTE
   ========================================== */
.projet-header {
    background: #fff;
    border: 1px solid #e2e8f0;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}
.projet-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.icone-projet {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.2rem;
    border-radius: 8px;
}
.titre-projet {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
    color: #1e293b;
}
.meta-projet {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 2px;
}
.meta-projet i {
    margin-right: 3px;
}
.projet-header-right {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.badge-statut {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    font-size: 0.6rem;
    font-weight: 700;
    border-radius: 4px;
}
.badge-encours { background: #dbeafe; color: #1d4ed8; }
.badge-termine { background: #d1fae5; color: #059669; }
.badge-annule { background: #fee2e2; color: #dc2626; }
.badge-attente { background: #fef3c7; color: #d97706; }
.badge-retard { background: #fee2e2; color: #dc2626; }

/* ==========================================
   GRILLE
   ========================================== */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 15px;
}
.col-gauche, .col-droite {
    overflow: hidden;
}

/* ==========================================
   CARTES
   ========================================== */
.carte {
    background: #fff;
    border: 1px solid #e2e8f0;
    margin-bottom: 14px;
    overflow: hidden;
    border-radius: 8px;
}
.carte-titre {
    padding: 8px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.6rem;
    font-weight: 800;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.carte-titre i {
    color: #1a56db;
}
.carte-corps {
    padding: 10px 16px;
}

/* ==========================================
   LIGNES D'INFORMATIONS
   ========================================== */
.ligne-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
}
.ligne-info:last-child {
    border-bottom: none;
}
.label {
    font-size: 0.6rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.valeur {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e293b;
}
.budget {
    color: #10b981;
}
.priorite-haute { color: #dc2626; }
.priorite-moyenne { color: #d97706; }
.priorite-basse { color: #10b981; }

.petit-badge {
    display: inline-block;
    padding: 2px 10px;
    font-size: 0.6rem;
    font-weight: 700;
    border-radius: 4px;
}
.petit-badge.success { background: #d1fae5; color: #059669; }
.petit-badge.warning { background: #fef3c7; color: #d97706; }
.petit-badge.danger { background: #fee2e2; color: #dc2626; }

/* ==========================================
   CALENDRIER
   ========================================== */
.cal-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.cal-grid .full {
    grid-column: 1 / -1;
}
.cal-item {
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 4px;
}
.cal-label {
    display: block;
    font-size: 0.5rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
}
.cal-label i {
    margin-right: 4px;
}
.cal-value {
    font-size: 0.7rem;
    font-weight: 600;
    color: #1e293b;
}

/* ==========================================
   DESCRIPTION
   ========================================== */
.desc-texte {
    font-size: 0.75rem;
    line-height: 1.6;
    color: #475569;
    margin: 0;
}

/* ==========================================
   AVANCEMENT
   ========================================== */
.avancement-box {
    background: #f8fafc;
    padding: 12px;
    border-radius: 4px;
}
.avancement-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.65rem;
    font-weight: 700;
    margin-bottom: 5px;
}
.avancement-pct {
    color: #1a56db;
    font-size: 1rem;
}
.avancement-bar {
    height: 8px;
    background: #e2e8f0;
    overflow: hidden;
    border-radius: 4px;
    margin-bottom: 10px;
}
.avancement-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #1a56db);
    border-radius: 4px;
}
.avancement-stats {
    display: flex;
    justify-content: space-around;
}
.stat {
    text-align: center;
}
.stat-nb {
    display: block;
    font-size: 0.9rem;
    font-weight: 800;
    color: #1e293b;
}
.stat-lb {
    font-size: 0.5rem;
    color: #64748b;
}

/* ==========================================
   ALERTE RETARD
   ========================================== */
.alerte-retard {
    background: #fee2e2;
    border: 1px solid #dc2626;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    color: #991b1b;
    margin-top: 8px;
    border-radius: 4px;
}
.alerte-retard i {
    color: #dc2626;
}

/* ==========================================
   ÉTAPES
   ========================================== */
.etapes-wrapper {
    padding: 6px 0 12px;
}
.etape-block {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}
.etape-block:last-child {
    border-bottom: none;
}
.etape-num {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.7rem;
    flex-shrink: 0;
    border-radius: 8px;
}
.etape-num.done { background: #d1fae5; color: #059669; }
.etape-num.progress { background: #dbeafe; color: #1a56db; }
.etape-num.wait { background: #f1f5f9; color: #64748b; }
.etape-body {
    flex: 1;
}
.etape-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}
.etape-name {
    font-weight: 700;
    font-size: 0.75rem;
    color: #1e293b;
}
.etape-status {
    font-size: 0.55rem;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 4px;
}
.etape-status.done { background: #d1fae5; color: #059669; }
.etape-status.progress { background: #dbeafe; color: #1a56db; }
.etape-status.wait { background: #f1f5f9; color: #64748b; }
.etape-desc {
    font-size: 0.65rem;
    color: #64748b;
    margin: 3px 0;
}
.etape-dates {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.55rem;
    color: #64748b;
    margin: 3px 0;
}
.etape-dates i {
    margin-right: 2px;
}
.etape-progress-bar {
    height: 4px;
    background: #e2e8f0;
    overflow: hidden;
    border-radius: 4px;
    margin: 4px 0;
}
.etape-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #1a56db);
    border-radius: 4px;
}
.etape-chef {
    font-size: 0.6rem;
    color: #64748b;
    margin-top: 3px;
}
.etape-chef i {
    margin-right: 4px;
    color: #1a56db;
}
.chef-nom {
    font-weight: 600;
    color: #1e293b;
}

/* ==========================================
   EMPTY
   ========================================== */
.empty-etapes {
    text-align: center;
    padding: 20px;
    color: #94a3b8;
}
.empty-etapes i {
    font-size: 1.8rem;
    display: block;
    margin-bottom: 6px;
}
.empty-etapes p {
    margin: 0;
    font-size: 0.75rem;
}

/* ==========================================
   ACTIONS
   ========================================== */
.btn-action {
    width: 100%;
    padding: 10px;
    border: none;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 4px;
}
.btn-valider {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
}
.btn-valider:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16,185,129,0.3);
}
.btn-annuler {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #dc2626;
}
.btn-annuler:hover {
    background: #dc2626;
    color: #fff;
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .projet-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .projet-header-left {
        flex-direction: column;
        align-items: flex-start;
    }
    .cal-grid {
        grid-template-columns: 1fr;
    }
    .etape-block {
        flex-direction: column;
        align-items: flex-start;
    }
    .etape-head {
        flex-direction: column;
        align-items: flex-start;
    }
    .avancement-stats {
        flex-wrap: wrap;
        gap: 6px;
    }
    .meta-projet {
        flex-direction: column;
        gap: 4px;
    }
}
</style>
@endpush