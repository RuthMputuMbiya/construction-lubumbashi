@extends('layouts.chef-projet')

@section('title', 'Validation du budget - Chef de Projet')

@section('page-title', 'Validation du budget')
@section('page-subtitle', $projet->nom ?? 'Projet')

@section('content')
<div class="validate-container">
    
    {{-- Bouton retour --}}
    <div class="retour-wrapper">
        <a href="{{ route('chef-projet.projets.index') }}" class="btn-retour">
            <i class="fas fa-arrow-left"></i> Retour aux projets
        </a>
    </div>

    {{-- Message de succès ou d'erreur --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- EN-TÊTE --}}
    {{-- ========================================== --}}
    <div class="validate-header">
        <div class="validate-header-left">
            <div class="validate-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h2 class="validate-title">Validation du budget</h2>
                <p class="validate-meta">
                    <span><i class="fas fa-project-diagram"></i> {{ $projet->nom ?? 'Projet' }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu ?? 'N/A' }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ isset($projet->created_at) ? Carbon\Carbon::parse($projet->created_at)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                </p>
            </div>
        </div>
        <div class="validate-header-right">
            <span class="badge-status warning"><i class="fas fa-clock"></i> EN ATTENTE DE VALIDATION</span>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- BANNIÈRE D'INFORMATION --}}
    {{-- ========================================== --}}
    <div class="info-banner">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Vérification avant validation</strong>
            <p>Veuillez vérifier les informations du projet avant de valider le budget.</p>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- GRILLE PRINCIPALE --}}
    {{-- ========================================== --}}
    <div class="validate-grid">

        {{-- ========================================== --}}
        {{-- COLONNE GAUCHE --}}
        {{-- ========================================== --}}
        <div class="validate-left">

            {{-- CARTE INFORMATIONS DU PROJET --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-info-circle"></i> INFORMATIONS DU PROJET
                </div>
                <div class="carte-corps">
                    <div class="ligne-info">
                        <span class="label">Nom</span>
                        <span class="valeur"><strong>{{ $projet->nom ?? 'N/A' }}</strong></span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">Lieu</span>
                        <span class="valeur"><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu ?? 'N/A' }}</span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">Type</span>
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
                        <span class="label">Priorité</span>
                        <span class="valeur">
                            @if(isset($projet->priorite))
                                @if($projet->priorite == 'haute') 🔴 Haute
                                @elseif($projet->priorite == 'moyenne') 🟡 Moyenne
                                @else 🟢 Basse
                                @endif
                            @else 🟡 Moyenne
                            @endif
                        </span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">Date début</span>
                        <span class="valeur"><i class="fas fa-calendar-day"></i> {{ isset($projet->date_debut_prevue) ? Carbon\Carbon::parse($projet->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">Date fin</span>
                        <span class="valeur"><i class="fas fa-flag-checkered"></i> {{ isset($projet->date_fin_prevue) ? Carbon\Carbon::parse($projet->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                    </div>
                    <div class="ligne-info budget-row">
                        <span class="label">Budget prévisionnel</span>
                        <span class="valeur budget"><i class="fas fa-money-bill-wave"></i> {{ number_format($projet->budget_previsionnel ?? $projet->budget_total ?? 0, 0, ',', ' ') }} {{ $projet->devise ?? 'FC' }}</span>
                    </div>
                    <div class="ligne-info">
                        <span class="label">Statut</span>
                        <span class="valeur">
                            <span class="badge-small warning">⏳ En attente de validation</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- CARTE DESCRIPTION --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-align-left"></i> DESCRIPTION
                </div>
                <div class="carte-corps">
                    <p class="desc-texte">{{ $projet->description ?? 'Aucune description' }}</p>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- COLONNE DROITE --}}
        {{-- ========================================== --}}
        <div class="validate-right">

            {{-- CARTE AVANCEMENT --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-chart-line"></i> AVANCEMENT
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
                    </div>
                </div>
            </div>

            {{-- CARTE ÉTAPES --}}
            <div class="carte">
                <div class="carte-titre">
                    <i class="fas fa-tasks"></i> ÉTAPES ({{ $etapes->count() ?? 0 }})
                </div>
                <div class="carte-corps etapes-wrapper">
                    @if(isset($etapes) && $etapes->count() > 0)
                        @foreach($etapes as $etape)
                        <div class="etape-item">
                            <div class="etape-num {{ $etape->progression >= 100 ? 'done' : ($etape->progression > 0 ? 'progress' : 'wait') }}">
                                @if($etape->progression >= 100)
                                    <i class="fas fa-check"></i>
                                @elseif($etape->progression > 0)
                                    <i class="fas fa-play"></i>
                                @else
                                    {{ str_pad($etape->ordre ?? 0, 2, '0', STR_PAD_LEFT) }}
                                @endif
                            </div>
                            <div class="etape-info">
                                <span class="etape-nom">{{ $etape->nom_etape ?? 'Étape' }}</span>
                                <span class="etape-chef"><i class="fas fa-user"></i> {{ $etape->chefChantier->nom ?? 'Non assigné' }}</span>
                            </div>
                            <div class="etape-progress">
                                <span>{{ $etape->progression ?? 0 }}%</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-etapes">
                            <i class="fas fa-tasks"></i>
                            <p>Aucune étape</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CARTE ACTIONS --}}
            <div class="carte actions-card">
                <div class="carte-titre">
                    <i class="fas fa-cogs"></i> VALIDATION
                </div>
                <div class="carte-corps">
                    {{-- Résumé --}}
                    <div class="validation-summary">
                        <div class="summary-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Étapes planifiées : <strong>{{ $etapes->count() }}</strong></span>
                        </div>
                        <div class="summary-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Chefs assignés : <strong>{{ $etapes->whereNotNull('id_chef_chantier')->count() }}</strong></span>
                        </div>
                        <div class="summary-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Budget : <strong>{{ number_format($projet->budget_previsionnel ?? $projet->budget_total ?? 0, 0, ',', ' ') }} {{ $projet->devise ?? 'FC' }}</strong></span>
                        </div>
                    </div>

                    {{-- Bouton Valider --}}
                    <form action="{{ route('chef-projet.projets.validate-budget', $projet->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-validate-budget">
                            <i class="fas fa-check-circle"></i> VALIDER LE BUDGET
                        </button>
                    </form>

                    {{-- Bouton Annuler --}}
                    <form action="{{ route('chef-projet.projets.cancel', $projet->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn-cancel-budget" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce projet ?')">
                            <i class="fas fa-ban"></i> ANNULER LE PROJET
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   VALIDATE BUDGET - DESIGN COMPLET
   ============================================ */

.validate-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}

/* ==========================================
   BOUTON RETOUR
   ========================================== */
.retour-wrapper {
    margin-bottom: 20px;
}
.btn-retour {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    border-radius: 6px;
}
.btn-retour:hover {
    border-color: #1a56db;
    color: #1a56db;
    transform: translateX(-3px);
}

/* ==========================================
   ALERTES
   ========================================== */
.alert {
    padding: 14px 18px;
    margin-bottom: 16px;
    border-radius: 6px;
    border-left: 4px solid;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alert-success {
    background: #ecfdf5;
    border-color: #10b981;
    color: #065f46;
}
.alert-danger {
    background: #fef2f2;
    border-color: #ef4444;
    color: #991b1b;
}
.alert i {
    font-size: 1.2rem;
}

/* ==========================================
   EN-TÊTE
   ========================================== */
.validate-header {
    background: #fff;
    border: 1px solid #e2e8f0;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
    border-radius: 8px;
}
.validate-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}
.validate-icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.6rem;
    border-radius: 10px;
    flex-shrink: 0;
}
.validate-title {
    font-size: 1.2rem;
    font-weight: 800;
    margin: 0;
    color: #1e293b;
}
.validate-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 4px;
}
.validate-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.validate-meta i {
    color: #1a56db;
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    font-size: 0.65rem;
    font-weight: 700;
    border-radius: 4px;
}
.badge-status.warning {
    background: #fef3c7;
    color: #d97706;
}

/* ==========================================
   BANNIÈRE INFO
   ========================================== */
.info-banner {
    background: #dbeafe;
    border-left: 4px solid #1a56db;
    padding: 14px 18px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
    border-radius: 4px;
}
.info-banner i {
    color: #1a56db;
    font-size: 1.2rem;
    margin-top: 2px;
}
.info-banner strong {
    display: block;
    font-size: 0.8rem;
    color: #1e40af;
}
.info-banner p {
    margin: 0;
    font-size: 0.75rem;
    color: #1e40af;
    opacity: 0.8;
}

/* ==========================================
   GRILLE PRINCIPALE
   ========================================== */
.validate-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 20px;
}
.validate-left, .validate-right {
    overflow: hidden;
}

/* ==========================================
   CARTES
   ========================================== */
.carte {
    background: #fff;
    border: 1px solid #e2e8f0;
    margin-bottom: 16px;
    overflow: hidden;
    border-radius: 8px;
}
.carte-titre {
    padding: 10px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.65rem;
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
    padding: 12px 18px;
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
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.valeur {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1e293b;
}
.budget {
    color: #10b981;
    font-weight: 700;
}
.budget-row {
    background: #ecfdf5;
    border-radius: 4px;
    padding: 6px 12px;
}

.badge-small {
    display: inline-block;
    padding: 3px 12px;
    font-size: 0.65rem;
    font-weight: 700;
    border-radius: 4px;
}
.badge-small.warning {
    background: #fef3c7;
    color: #d97706;
}

/* ==========================================
   DESCRIPTION
   ========================================== */
.desc-texte {
    font-size: 0.8rem;
    line-height: 1.6;
    color: #475569;
    margin: 0;
}

/* ==========================================
   AVANCEMENT
   ========================================== */
.avancement-box {
    background: #f8fafc;
    padding: 14px;
    border-radius: 6px;
}
.avancement-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    font-weight: 700;
    margin-bottom: 6px;
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
}
.avancement-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #1a56db);
    border-radius: 4px;
}

/* ==========================================
   ÉTAPES
   ========================================== */
.etapes-wrapper {
    padding: 4px 0 10px;
}
.etape-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}
.etape-item:last-child {
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
    border-radius: 6px;
    flex-shrink: 0;
}
.etape-num.done { background: #d1fae5; color: #059669; }
.etape-num.progress { background: #dbeafe; color: #1a56db; }
.etape-num.wait { background: #f1f5f9; color: #64748b; }
.etape-info {
    flex: 1;
}
.etape-nom {
    font-weight: 600;
    font-size: 0.75rem;
    display: block;
    color: #1e293b;
}
.etape-chef {
    font-size: 0.65rem;
    color: #64748b;
}
.etape-chef i {
    margin-right: 4px;
}
.etape-progress {
    font-size: 0.7rem;
    font-weight: 700;
    color: #1a56db;
    min-width: 40px;
    text-align: right;
}

.empty-etapes {
    text-align: center;
    padding: 16px;
    color: #94a3b8;
}
.empty-etapes i {
    font-size: 1.5rem;
    display: block;
    margin-bottom: 6px;
}
.empty-etapes p {
    margin: 0;
    font-size: 0.8rem;
}

/* ==========================================
   ACTIONS
   ========================================== */
.actions-card {
    border-color: #f59e0b;
}
.actions-card .carte-titre {
    background: #fffbeb;
    border-color: #fef3c7;
}
.actions-card .carte-titre i {
    color: #f59e0b;
}

.validation-summary {
    background: #f8fafc;
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 14px;
}
.summary-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    font-size: 0.75rem;
    color: #475569;
}
.summary-item i {
    font-size: 0.9rem;
}
.summary-item strong {
    color: #1e293b;
}
.text-success { color: #10b981; }

.btn-validate-budget {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    padding: 14px;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-validate-budget:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16,185,129,0.3);
}

.btn-cancel-budget {
    width: 100%;
    background: #fee2e2;
    color: #dc2626;
    padding: 12px;
    border: 1px solid #dc2626;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-cancel-budget:hover {
    background: #dc2626;
    color: #fff;
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .validate-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .validate-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .validate-header-left {
        flex-direction: column;
        align-items: flex-start;
    }
    .validate-meta {
        flex-direction: column;
        gap: 4px;
    }
    .etape-item {
        flex-wrap: wrap;
    }
    .etape-progress {
        text-align: left;
        margin-left: auto;
    }
}
</style>
@endpush