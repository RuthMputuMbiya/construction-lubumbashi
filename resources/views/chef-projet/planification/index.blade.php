@extends('layouts.chef-projet')

@section('title', 'Planification - Chef de Projet')

@section('page-title', 'Mes planifications')
@section('page-subtitle', 'Classement et suivi de vos planifications de projets')

@section('content')
<div class="planification-container">
    
    {{-- STATISTIQUES GLOBALES --}}
    <div class="stats-globales">
        <div class="stat-card-global">
            <div class="stat-icon-global bg-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info-global">
                <span class="stat-number">{{ $mesProjets->count() }}</span>
                <span class="stat-label">PLANIFICATIONS TOTALES</span>
            </div>
        </div>
        <div class="stat-card-global">
            <div class="stat-icon-global bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info-global">
                <span class="stat-number">{{ $mesProjets->where('statut_planification', 'en_cours')->count() }}</span>
                <span class="stat-label">EN COURS</span>
            </div>
        </div>
        <div class="stat-card-global">
            <div class="stat-icon-global bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info-global">
                <span class="stat-number">{{ $mesProjets->where('statut_planification', 'validee')->count() }}</span>
                <span class="stat-label">VALIDÉES</span>
            </div>
        </div>
        <div class="stat-card-global">
            <div class="stat-icon-global bg-info">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info-global">
                <span class="stat-number">{{ $mesProjets->sum(function($p) { return $p->etapes_count ?? 0; }) }}</span>
                <span class="stat-label">ÉTAPES PLANIFIÉES</span>
            </div>
        </div>
    </div>
    
    {{-- BARRE D'ACTIONS --}}
    <div class="actions-bar">
        <div class="actions-left">
            <a href="{{ route('chef-projet.planification.create') }}" class="btn-create-planification">
                <i class="fas fa-plus-circle"></i> 
                <span>CRÉER UNE PLANIFICATION</span>
            </a>
        </div>
        <div class="actions-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher une planification..." class="search-input">
            </div>
            <div class="filter-box">
                <i class="fas fa-filter"></i>
                <select id="statusFilter" class="filter-select">
                    <option value="all">📋 Toutes les planifications</option>
                    <option value="en_cours">⏳ En cours</option>
                    <option value="complete">📝 Complètes</option>
                    <option value="validee">✅ Validées</option>
                </select>
            </div>
        </div>
    </div>
    
    {{-- LISTE DES PLANIFICATIONS --}}
    <div class="dossiers-container" id="dossiersContainer">
        @forelse($mesProjets as $index => $planif)
        @php
            $etapesCount = $planif->etapes_count ?? App\Models\EtapeProjet::where('id_projet', $planif->id)->count();
            $etapesTerminees = App\Models\EtapeProjet::where('id_projet', $planif->id)->where('statut', 'termine')->count();
            $pourcentage = $etapesCount > 0 ? round(($etapesTerminees / 5) * 100) : 0;
            $couleurDossier = $pourcentage >= 100 ? 'success' : ($pourcentage >= 50 ? 'warning' : 'primary');
            $devise = $planif->devise ?? 'CDF';
            $budgetAffiche = number_format($planif->budget_previsionnel ?? $planif->budget_total, 0, ',', ' ');
        @endphp
        
        <div class="dossier-card" data-id="{{ $planif->id }}" data-statut="{{ $planif->statut_planification }}">
            <div class="dossier-header" onclick="toggleDossier({{ $planif->id }})">
                <div class="dossier-icon {{ $couleurDossier }}">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="dossier-info">
                    <h3 class="dossier-nom">{{ $planif->nom }}</h3>
                    <div class="dossier-meta">
                        <span class="dossier-code">{{ $planif->code_projet ?? 'PLF-'.str_pad($planif->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="dossier-lieu"><i class="fas fa-map-marker-alt"></i> {{ $planif->lieu }}</span>
                        <span class="dossier-responsable"><i class="fas fa-user-tie"></i> {{ $planif->responsable ?? Auth::user()->nom }}</span>
                        <span class="dossier-date"><i class="fas fa-calendar"></i> {{ Carbon\Carbon::parse($planif->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
                    </div>
                </div>
                <div class="dossier-stats">
                    <div class="stat-pill {{ $couleurDossier }}">
                        <span class="stat-pourcent">{{ $pourcentage }}%</span>
                    </div>
                    <div class="stat-pill info">
                        <i class="fas fa-tasks"></i>
                        <span>{{ $etapesCount }}/5 étapes</span>
                    </div>
                    <div class="stat-pill {{ $planif->statut_planification == 'en_cours' ? 'warning' : ($planif->statut_planification == 'complete' ? 'info' : 'success') }}">
                        <i class="fas {{ $planif->statut_planification == 'en_cours' ? 'fa-clock' : ($planif->statut_planification == 'complete' ? 'fa-pen' : 'fa-check-circle') }}"></i>
                        <span>{{ $planif->statut_planification == 'en_cours' ? 'En cours' : ($planif->statut_planification == 'complete' ? 'Complète' : 'Validée') }}</span>
                    </div>
                    <div class="toggle-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
            
            <div class="dossier-body" id="dossier-{{ $planif->id }}" style="display: none;">
                
                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        <span>INFORMATIONS GÉNÉRALES</span>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>RESPONSABLE</label>
                            <span><i class="fas fa-user-tie"></i> {{ $planif->responsable ?? Auth::user()->nom }}</span>
                        </div>
                        <div class="info-item">
                            <label>TYPE DE PROJET</label>
                            <span>
                                @if($planif->type_projet == 'batiment') 🏢 Bâtiment 
                                @elseif($planif->type_projet == 'route') 🛣️ Route 
                                @elseif($planif->type_projet == 'pont') 🌉 Pont 
                                @else 📐 Autre @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <label>PRIORITÉ</label>
                            <span class="priorite-{{ $planif->priorite ?? 'moyenne' }}">
                                @if(($planif->priorite ?? 'moyenne') == 'haute') 🔴 Haute 
                                @elseif(($planif->priorite ?? 'moyenne') == 'moyenne') 🟡 Moyenne 
                                @else 🟢 Basse @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <label>BUDGET</label>
                            <span>
                                <i class="fas fa-money-bill-wave"></i> 
                                {{ $budgetAffiche }} 
                                @if($devise == 'CDF') FC 
                                @elseif($devise == 'USD') USD ($) 
                                @else EUR (€) @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <label>DATE DÉBUT</label>
                            <span>
                                <i class="fas fa-calendar-alt"></i> 
                                {{ Carbon\Carbon::parse($planif->date_debut_prevue)->locale('fr')->isoFormat('D MMMM YYYY') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>DATE FIN</label>
                            <span>
                                <i class="fas fa-flag-checkered"></i> 
                                {{ Carbon\Carbon::parse($planif->date_fin_prevue)->locale('fr')->isoFormat('D MMMM YYYY') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>DURÉE</label>
                            <span>
                                <i class="fas fa-hourglass-half"></i> 
                                {{ $planif->duree_jours ?? Carbon\Carbon::parse($planif->date_debut_prevue)->diffInDays(Carbon\Carbon::parse($planif->date_fin_prevue)) }} jour(s)
                            </span>
                        </div>
                        <div class="info-item">
                            <label>DATE CRÉATION</label>
                            <span>
                                <i class="fas fa-plus-circle"></i> 
                                {{ Carbon\Carbon::parse($planif->created_at)->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item full-width mt-2">
                        <label>DESCRIPTION</label>
                        <p class="description-text">{{ $planif->description }}</p>
                    </div>
                </div>
                
                {{-- CHEF DE CHANTIER ASSIGNÉ --}}
                @php $chefAssigne = App\Models\EtapeProjet::where('id_projet', $planif->id)->whereNotNull('id_chef_chantier')->first(); @endphp
                @if($chefAssigne && $chefAssigne->chefChantier)
                <div class="chef-section">
                    <div class="section-title">
                        <i class="fas fa-hard-hat"></i>
                        <span>CHEF DE CHANTIER ASSIGNÉ</span>
                    </div>
                    <div class="chef-card">
                        <div class="chef-avatar">{{ strtoupper(substr($chefAssigne->chefChantier->nom, 0, 2)) }}</div>
                        <div class="chef-info">
                            <h4>{{ $chefAssigne->chefChantier->nom }}</h4>
                            <p><i class="fas fa-envelope"></i> {{ $chefAssigne->chefChantier->email }}</p>
                            <small><i class="fas fa-tasks"></i> Responsable des travaux</small>
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- ÉTAPES PLANIFIÉES --}}
                @php $etapesPlanifiees = App\Models\EtapeProjet::where('id_projet', $planif->id)->orderBy('ordre')->get(); @endphp
                
                <div class="etapes-section">
                    <div class="section-title">
                        <i class="fas fa-tasks"></i>
                        <span>ÉTAPES PLANIFIÉES ({{ $etapesPlanifiees->count() }}/5)</span>
                        @if($etapesPlanifiees->count() < 5 && $planif->statut_planification != 'validee')
                            <a href="{{ route('chef-projet.planification.etapes', $planif->id) }}" class="btn-add-etape">
                                <i class="fas fa-plus"></i> AJOUTER UNE ÉTAPE
                            </a>
                        @endif
                    </div>
                    
                    @if($etapesPlanifiees->count() > 0)
                    <div class="etapes-list">
                        @foreach($etapesPlanifiees as $etape)
                        <div class="etape-item-mini">
                            <div class="etape-num-mini">{{ str_pad($etape->ordre, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="etape-content-mini">
                                <div class="etape-nom">{{ $etape->nom_etape }}</div>
                                <div class="etape-chef"><i class="fas fa-user"></i> {{ $etape->chefChantier->nom ?? 'Non assigné' }}</div>
                            </div>
                            <div class="etape-statut">
                                @if($etape->statut == 'termine') 
                                    <span class="badge-status success">Terminée</span>
                                @elseif($etape->statut == 'en_cours') 
                                    <span class="badge-status primary">En cours</span>
                                @else 
                                    <span class="badge-status secondary">À venir</span> 
                                @endif
                            </div>
                            <div class="etape-progress-mini">
                                <div class="progress-bar-mini">
                                    <div class="progress-fill-mini" style="width: {{ $etape->progression }}%"></div>
                                </div>
                                <span>{{ $etape->progression }}%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-etapes">
                        <i class="fas fa-info-circle"></i>
                        <p>Aucune étape planifiée pour le moment.</p>
                        <a href="{{ route('chef-projet.planification.etapes', $planif->id) }}" class="btn-link">Commencer la planification des étapes →</a>
                    </div>
                    @endif
                    
                    {{-- BOUTON TRANSFORMER EN PROJET --}}
                    @if($etapesPlanifiees->count() >= 5 && $planif->statut_planification != 'validee')
                    <form action="{{ route('chef-projet.planification.transformer', $planif->id) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn-transformer">
                            <i class="fas fa-exchange-alt"></i> VALIDER ET CRÉER LE PROJET
                        </button>
                    </form>
                    @endif
                    
                    @if($planif->statut_planification == 'validee')
                    <div class="validation-badge">
                        <i class="fas fa-check-circle"></i> 
                        <span>✅ Planification validée et transformée en projet</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-folder-open fa-4x"></i>
            <h3>AUCUNE PLANIFICATION</h3>
            <p>Commencez par créer une nouvelle planification de projet</p>
            <a href="{{ route('chef-projet.planification.create') }}" class="btn-create">
                <i class="fas fa-plus-circle"></i> CRÉER UNE PLANIFICATION
            </a>
        </div>
        @endforelse
    </div>
</div>

{{-- FORMULAIRE DE SUPPRESSION --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf 
    @method('DELETE')
</form>

@push('styles')
<style>
.planification-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}

.stats-globales {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card-global {
    background: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.stat-card-global:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.stat-icon-global {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon-global i {
    font-size: 1.3rem;
    color: white;
}

.bg-primary { background: linear-gradient(135deg, #1a56db, #0e3a8a); }
.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
.bg-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }

.stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    display: block;
    line-height: 1.2;
}

.stat-label {
    font-size: .65rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    background: white;
    padding: 1rem 1.25rem;
    border: 1px solid #e2e8f0;
}

.actions-left {
    display: flex;
    gap: 0.75rem;
}

.actions-right {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-create-planification {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: white;
    padding: 0.7rem 1.5rem;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-create-planification:hover {
    background: linear-gradient(135deg, #0e3a8a, #1a56db);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,86,219,0.3);
    color: white;
}

.btn-create-planification i {
    font-size: 1.1rem;
}

.search-box {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-input {
    width: 100%;
    padding: 0.7rem 1rem 0.7rem 2.5rem;
    border: 1px solid #e2e8f0;
    font-size: 0.85rem;
    transition: all 0.3s;
}

.search-input:focus {
    outline: none;
    border-color: #1a56db;
    box-shadow: 0 0 0 3px rgba(26,86,219,0.1);
}

.filter-box {
    flex: 0.5;
    min-width: 180px;
    position: relative;
}

.filter-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.filter-select {
    width: 100%;
    padding: 0.7rem 1rem 0.7rem 2.5rem;
    border: 1px solid #e2e8f0;
    font-size: 0.85rem;
    background: white;
    cursor: pointer;
}

.dossiers-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.dossier-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s;
}

.dossier-card:hover {
    border-color: #1a56db;
    box-shadow: 0 8px 25px rgba(26,86,219,0.1);
}

.dossier-header {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.dossier-header:hover {
    background: #dbeafe;
}

.dossier-header.open {
    background: #dbeafe;
}

.dossier-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.dossier-icon.primary { background: #dbeafe; color: #1a56db; }
.dossier-icon.warning { background: #fef3c7; color: #d97706; }
.dossier-icon.success { background: #d1fae5; color: #059669; }

.dossier-icon i {
    font-size: 1.5rem;
}

.dossier-info {
    flex: 1;
}

.dossier-nom {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 0.3rem 0;
    color: #1e293b;
}

.dossier-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: 0.7rem;
    color: #64748b;
}

.dossier-meta i {
    margin-right: 0.3rem;
}

.dossier-stats {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 700;
}

.stat-pill.primary { background: #dbeafe; color: #1a56db; }
.stat-pill.warning { background: #fef3c7; color: #d97706; }
.stat-pill.success { background: #d1fae5; color: #059669; }
.stat-pill.info { background: #f1f5f9; color: #64748b; }

.stat-pourcent {
    font-size: 0.85rem;
    font-weight: 800;
}

.toggle-icon i {
    font-size: 1rem;
    color: #94a3b8;
    transition: transform 0.3s;
}

.dossier-header.open .toggle-icon i {
    transform: rotate(180deg);
}

.dossier-body {
    border-top: 1px solid #e2e8f0;
    padding: 1.5rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-title i {
    color: #1a56db;
}

.section-title span {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #1e293b;
}

.btn-add-etape {
    margin-left: auto;
    background: #10b981;
    color: white;
    padding: 0.3rem 0.8rem;
    font-size: 0.65rem;
    font-weight: 700;
    text-decoration: none;
}

.btn-add-etape:hover {
    background: #059669;
    color: white;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 0.75rem;
}

.info-item {
    background: #f8fafc;
    padding: 0.5rem 0.75rem;
}

.info-item label {
    display: block;
    font-size: 0.6rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item span {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1e293b;
}

.full-width {
    grid-column: 1 / -1;
}

.description-text {
    font-size: 0.8rem;
    color: #475569;
    line-height: 1.5;
    margin: 0;
}

.chef-section {
    margin-top: 1rem;
}

.chef-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #f8fafc;
    padding: 1rem;
}

.chef-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 1rem;
}

.chef-info h4 {
    font-size: 0.9rem;
    font-weight: 800;
    margin: 0 0 0.2rem 0;
}

.chef-info p {
    font-size: 0.7rem;
    color: #64748b;
    margin: 0;
}

.chef-info small {
    font-size: 0.65rem;
    color: #94a3b8;
}

.etapes-section {
    margin-top: 1rem;
}

.etapes-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.etape-item-mini {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    flex-wrap: wrap;
}

.etape-num-mini {
    width: 35px;
    height: 35px;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #1a56db;
}

.etape-content-mini {
    flex: 2;
    min-width: 180px;
}

.etape-nom {
    font-weight: 700;
    font-size: 0.8rem;
}

.etape-chef {
    font-size: 0.65rem;
    color: #64748b;
}

.badge-status {
    padding: 0.25rem 0.7rem;
    font-size: 0.65rem;
    font-weight: 700;
}

.badge-status.success { background: #d1fae5; color: #059669; }
.badge-status.primary { background: #dbeafe; color: #1a56db; }
.badge-status.secondary { background: #f1f5f9; color: #64748b; }

.etape-progress-mini {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 100px;
}

.progress-bar-mini {
    flex: 1;
    height: 5px;
    background: #e2e8f0;
    overflow: hidden;
}

.progress-fill-mini {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #1a56db);
}

.validation-badge {
    background: #d1fae5;
    padding: 0.75rem 1rem;
    margin-top: 1rem;
    text-align: center;
    border-left: 3px solid #059669;
}

.validation-badge i {
    color: #059669;
    margin-right: 0.5rem;
}

.validation-badge span {
    font-weight: 600;
    color: #065f46;
}

.btn-transformer {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.7rem;
    border: none;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-transformer:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16,185,129,0.3);
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
}

.empty-state i {
    color: #1a56db;
    opacity: 0.4;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 0.9rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 1rem;
}

.btn-create {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: white;
    padding: 0.6rem 1.5rem;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    display: inline-block;
}

.btn-create:hover {
    background: linear-gradient(135deg, #0e3a8a, #1a56db);
    color: white;
}

@media (max-width: 992px) {
    .stats-globales {
        grid-template-columns: repeat(2, 1fr);
    }
    .actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
}

@media (max-width: 768px) {
    .stats-globales {
        grid-template-columns: 1fr;
    }
    .actions-right {
        flex-direction: column;
    }
    .dossier-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .dossier-stats {
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    .etape-item-mini {
        flex-direction: column;
        align-items: flex-start;
    }
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleDossier(id) {
    var body = document.getElementById('dossier-' + id);
    var header = body.previousElementSibling;
    
    if (body.style.display === 'none') {
        body.style.display = 'block';
        header.classList.add('open');
    } else {
        body.style.display = 'none';
        header.classList.remove('open');
    }
}

document.getElementById('statusFilter').addEventListener('change', function() {
    var valeur = this.value;
    document.querySelectorAll('.dossier-card').forEach(function(dossier) {
        var statut = dossier.getAttribute('data-statut');
        if (valeur === 'all' || statut === valeur) {
            dossier.style.display = '';
        } else {
            dossier.style.display = 'none';
        }
    });
});

document.getElementById('searchInput').addEventListener('keyup', function() {
    var terme = this.value.toLowerCase();
    document.querySelectorAll('.dossier-card').forEach(function(dossier) {
        var nom = dossier.querySelector('.dossier-nom').textContent.toLowerCase();
        var lieu = dossier.querySelector('.dossier-lieu').textContent.toLowerCase();
        if (nom.includes(terme) || lieu.includes(terme)) {
            dossier.style.display = '';
        } else {
            dossier.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var firstBody = document.querySelector('.dossier-body');
    if (firstBody) {
        firstBody.style.display = 'block';
        firstBody.previousElementSibling.classList.add('open');
    }
});
</script>
@endpush
@endsection