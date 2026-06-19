{{-- resources/views/admin/projets/show.blade.php --}}
{{-- 
    PAGE DÉTAILS D'UN PROJET (ADMIN)
    EXPLICATION: Vue détaillée d'un projet avec toutes les informations
--}}

@extends('layouts.app')

@section('title', 'Détails du projet - ConstrucTrack')

@section('page-title', 'Détails du projet')

@section('content')
<div class="container-fluid px-0">
    
    {{-- Fil d'Ariane --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.projets.index') }}">Projets</a></li>
                <li class="breadcrumb-item active">{{ $projet->nom }}</li>
            </ol>
        </nav>
    </div>
    
    {{-- En-tête du projet --}}
    <div class="project-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="project-badges mb-2">
                    <span class="badge-project code">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if($projet->statut == 'en_cours')
                        <span class="badge-project status primary"><i class="fas fa-play-circle"></i> En cours</span>
                    @elseif($projet->statut == 'termine')
                        <span class="badge-project status success"><i class="fas fa-check-circle"></i> Terminé</span>
                    @elseif($projet->statut == 'annule')
                        <span class="badge-project status danger"><i class="fas fa-ban"></i> Annulé</span>
                    @else
                        <span class="badge-project status warning"><i class="fas fa-clock"></i> En attente</span>
                    @endif
                </div>
                <h2 class="project-title mb-2">{{ $projet->nom }}</h2>
                <p class="project-location"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $projet->lieu }}</p>
            </div>
            <div class="project-actions">
                <a href="#" class="btn-export" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Exporter
                </a>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Colonne gauche - Informations générales -->
        <div class="col-lg-4">
            <!-- Carte description -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i> Description</h6>
                </div>
                <div class="info-card-body">
                    <p class="text-muted">{{ $projet->description }}</p>
                </div>
            </div>
            
            <!-- Carte dates -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h6 class="mb-0"><i class="fas fa-calendar me-2 text-primary"></i> Calendrier</h6>
                </div>
                <div class="info-card-body">
                    <div class="date-item">
                        <div class="date-label">Début prévu</div>
                        <div class="date-value">{{ \Carbon\Carbon::parse($projet->date_debut_prevue)->format('d/m/Y') }}</div>
                    </div>
                    <div class="date-item">
                        <div class="date-label">Fin prévue</div>
                        <div class="date-value">{{ \Carbon\Carbon::parse($projet->date_fin_prevue)->format('d/m/Y') }}</div>
                    </div>
                    @if($projet->date_debut_reelle)
                    <div class="date-item">
                        <div class="date-label">Début réel</div>
                        <div class="date-value">{{ \Carbon\Carbon::parse($projet->date_debut_reelle)->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($projet->date_fin_reelle)
                    <div class="date-item">
                        <div class="date-label">Fin réelle</div>
                        <div class="date-value">{{ \Carbon\Carbon::parse($projet->date_fin_reelle)->format('d/m/Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Carte responsable -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i> Responsable</h6>
                </div>
                <div class="info-card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="responsible-avatar">
                            {{ strtoupper(substr($projet->chefProjet->nom ?? 'N/A', 0, 1)) }}
                        </div>
                        <div>
                            <div class="responsible-name">{{ $projet->chefProjet->nom ?? 'Non assigné' }}</div>
                            <div class="responsible-role">Chef de projet</div>
                        </div>
                    </div>
                    @if($projet->chefProjet)
                        <div class="responsible-contact mt-3 pt-2 border-top">
                            <div><i class="fas fa-envelope me-2"></i>{{ $projet->chefProjet->email }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Carte budget -->
            <div class="info-card">
                <div class="info-card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Budget</h6>
                </div>
                <div class="info-card-body">
                    <div class="budget-item">
                        <span class="budget-label">Budget total</span>
                        <span class="budget-amount">{{ number_format($budgetInfo['total'], 0, ',', ' ') }} FC</span>
                    </div>
                    <div class="budget-item">
                        <span class="budget-label">Dépenses</span>
                        <span class="budget-amount text-warning">{{ number_format($budgetInfo['depense'], 0, ',', ' ') }} FC</span>
                    </div>
                    <div class="budget-item">
                        <span class="budget-label">Restant</span>
                        <span class="budget-amount text-success">{{ number_format($budgetInfo['restant'], 0, ',', ' ') }} FC</span>
                    </div>
                    <div class="progress-custom mt-3">
                        <div class="progress-bar-custom" style="width: {{ $budgetInfo['taux'] }}%"></div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">Taux de consommation: {{ $budgetInfo['taux'] }}%</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne droite - Détails -->
        <div class="col-lg-8">
            <!-- Avancement global -->
            <div class="progress-card mb-4">
                <div class="progress-card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> Avancement global</h6>
                </div>
                <div class="progress-card-body">
                    <div class="big-progress">
                        <div class="big-progress-circle">
                            <svg viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#1a56db" stroke-width="8"
                                        stroke-dasharray="339.292" stroke-dashoffset="{{ 339.292 * (1 - $avancementGlobal/100) }}"
                                        transform="rotate(-90 60 60)"/>
                            </svg>
                            <div class="big-progress-value">
                                <span class="value">{{ $avancementGlobal }}</span>
                                <span class="unit">%</span>
                            </div>
                        </div>
                        <div class="big-progress-stats">
                            <div class="stat-item">
                                <span class="stat-num">{{ $statsEtapes['total'] }}</span>
                                <span class="stat-label">Étapes totales</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-num text-success">{{ $statsEtapes['terminees'] }}</span>
                                <span class="stat-label">Terminées</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-num text-warning">{{ $statsEtapes['en_cours'] }}</span>
                                <span class="stat-label">En cours</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-num text-danger">{{ $statsEtapes['en_retard'] }}</span>
                                <span class="stat-label">En retard</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Liste des étapes -->
            <div class="etapes-card">
                <div class="etapes-card-header">
                    <h6 class="mb-0"><i class="fas fa-tasks me-2 text-primary"></i> Planification des étapes</h6>
                </div>
                <div class="etapes-card-body p-0">
                    <div class="etapes-timeline">
                        @forelse($projet->planifications->sortBy('ordre') as $etape)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $etape->statut == 'termine' ? 'completed' : ($etape->statut == 'en_cours' ? 'current' : 'pending') }}">
                                    <i class="fas {{ $etape->statut == 'termine' ? 'fa-check' : ($etape->statut == 'en_cours' ? 'fa-play' : 'fa-circle') }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="timeline-title">{{ $etape->nom_etape }}</h6>
                                            <p class="timeline-desc">{{ $etape->description }}</p>
                                            <div class="timeline-meta">
                                                <span><i class="fas fa-user"></i> {{ $etape->chefChantier->nom ?? 'Non assigné' }}</span>
                                                <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($etape->date_debut_prevue)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($etape->date_fin_prevue)->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="timeline-progress">
                                            <div class="progress-custom">
                                                <div class="progress-bar-custom" style="width: {{ $etape->progression }}%"></div>
                                            </div>
                                            <span class="progress-text">{{ $etape->progression }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-alt fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Aucune étape planifiée pour ce projet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .breadcrumb-custom {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        background: transparent;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb-custom .breadcrumb-item {
        font-size: 0.85rem;
    }
    
    .breadcrumb-custom .breadcrumb-item a {
        color: #6b7280;
        text-decoration: none;
    }
    
    .breadcrumb-custom .breadcrumb-item.active {
        color: #1a56db;
        font-weight: 500;
    }
    
    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #9ca3af;
        padding: 0 0.5rem;
    }
    
    .project-header {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .badge-project {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 500;
        margin-right: 0.5rem;
    }
    
    .badge-project.code {
        background: #f3f4f6;
        color: #4b5563;
        font-family: monospace;
    }
    
    .badge-project.status.primary { background: #dbeafe; color: #1d4ed8; }
    .badge-project.status.success { background: #d1fae5; color: #059669; }
    .badge-project.status.danger { background: #fee2e2; color: #dc2626; }
    .badge-project.status.warning { background: #fef3c7; color: #d97706; }
    
    .project-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .project-location {
        color: #6b7280;
        font-size: 0.85rem;
        margin: 0;
    }
    
    .btn-export {
        background: #f3f4f6;
        color: #4b5563;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    
    .btn-export:hover {
        background: #e5e7eb;
    }
    
    .info-card, .progress-card, .etapes-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .info-card-header, .progress-card-header, .etapes-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        background: #fafbfc;
    }
    
    .info-card-body {
        padding: 1.25rem;
    }
    
    .date-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .date-item:last-child {
        border-bottom: none;
    }
    
    .date-label {
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .date-value {
        font-weight: 500;
        color: #1f2937;
    }
    
    .responsible-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #1a56db, #0e3a8a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }
    
    .responsible-name {
        font-weight: 600;
        color: #1f2937;
    }
    
    .responsible-role {
        font-size: 0.7rem;
        color: #6b7280;
    }
    
    .responsible-contact {
        font-size: 0.8rem;
        color: #4b5563;
    }
    
    .budget-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .budget-label {
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .budget-amount {
        font-weight: 600;
        color: #1f2937;
    }
    
    .big-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
    }
    
    .big-progress-circle {
        position: relative;
        width: 150px;
        height: 150px;
    }
    
    .big-progress-circle svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    
    .big-progress-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    
    .big-progress-value .value {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .big-progress-value .unit {
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .big-progress-stats {
        display: flex;
        gap: 1.5rem;
    }
    
    .big-progress-stats .stat-item {
        text-align: center;
    }
    
    .big-progress-stats .stat-num {
        font-size: 1.25rem;
        font-weight: 700;
        display: block;
    }
    
    .big-progress-stats .stat-label {
        font-size: 0.7rem;
        color: #6b7280;
    }
    
    .etapes-timeline {
        padding: 1rem;
    }
    
    .timeline-item {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-marker {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        z-index: 2;
    }
    
    .timeline-marker.completed {
        background: #d1fae5;
        color: #059669;
    }
    
    .timeline-marker.current {
        background: #dbeafe;
        color: #1d4ed8;
    }
    
    .timeline-marker.pending {
        background: #f3f4f6;
        color: #9ca3af;
    }
    
    .timeline-content {
        flex: 1;
        background: #fafbfc;
        border-radius: 16px;
        padding: 1rem;
    }
    
    .timeline-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin: 0 0 0.25rem;
    }
    
    .timeline-desc {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .timeline-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.7rem;
        color: #9ca3af;
    }
    
    .timeline-progress {
        min-width: 100px;
        text-align: center;
    }
    
    .progress-text {
        font-size: 0.7rem;
        font-weight: 500;
        color: #1a56db;
        display: block;
        margin-top: 0.25rem;
    }
    
    .progress-custom {
        background: #f3f4f6;
        border-radius: 10px;
        height: 6px;
        overflow: hidden;
    }
    
    .progress-bar-custom {
        background: linear-gradient(90deg, #10b981, #1a56db);
        height: 100%;
        border-radius: 10px;
    }
</style>
@endpush