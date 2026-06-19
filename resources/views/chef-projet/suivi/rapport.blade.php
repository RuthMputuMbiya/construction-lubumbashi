@extends('layouts.chef-projet')

@section('title', 'Rapport avancement - Chef de Projet')

@section('page-title', 'Rapport d\'avancement')
@section('page-subtitle', $projet->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <a href="{{ route('chef-projet.suivi.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Retour au suivi
                </a>
                <button onclick="window.print()" class="btn-print ms-2">
                    <i class="fas fa-print me-2"></i> Imprimer
                </button>
            </div>
            
            <div class="card" id="rapportContent">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $projet->nom }}</h4>
                    <p class="mb-0 mt-1"><i class="fas fa-map-marker-alt me-1"></i> {{ $projet->lieu }}</p>
                </div>
                <div class="card-body">
                    
                    {{-- Informations générales --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label>Code projet</label>
                                <p>{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label>Responsable</label>
                                <p>{{ $projet->responsable ?? Auth::user()->nom }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label>Date de début</label>
                                <p>{{ Carbon\Carbon::parse($projet->date_debut_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label>Date de fin prévue</label>
                                <p>{{ Carbon\Carbon::parse($projet->date_fin_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label>Budget</label>
                                <p>{{ number_format($projet->budget_previsionnel ?? $projet->budget_total, 0, ',', ' ') }} {{ $projet->devise ?? 'FC' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label>Statut</label>
                                <p>
                                    @if($projet->statut == 'termine')
                                        <span class="badge bg-success">Terminé</span>
                                    @elseif($projet->statut == 'en_cours')
                                        <span class="badge bg-primary">En cours</span>
                                    @elseif($projet->statut == 'annule')
                                        <span class="badge bg-danger">Annulé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Statistiques avancement --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-primary"><i class="fas fa-chart-line me-2"></i> STATISTIQUES D'AVANCEMENT</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-value">{{ $stats['total_etapes'] }}</div>
                                <div class="stat-label">Total étapes</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-value text-success">{{ $stats['terminees'] }}</div>
                                <div class="stat-label">Terminées</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-value text-warning">{{ $stats['en_cours'] }}</div>
                                <div class="stat-label">En cours</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-value text-danger">{{ $stats['en_retard'] }}</div>
                                <div class="stat-label">En retard</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Barre de progression --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Avancement global</span>
                            <span class="fw-bold">{{ $avancement_global }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ $avancement_global }}%">
                                {{ $avancement_global }}%
                            </div>
                        </div>
                    </div>
                    
                    {{-- Détail des étapes --}}
                    <div class="mt-4">
                        <h6 class="fw-bold text-primary"><i class="fas fa-tasks me-2"></i> DÉTAIL DES ÉTAPES</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ordre</th>
                                        <th>Étape</th>
                                        <th>Chef de chantier</th>
                                        <th>Dates</th>
                                        <th>Progression</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etapes as $etape)
                                    <tr>
                                        <td class="text-center">{{ str_pad($etape->ordre, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td><strong>{{ $etape->nom_etape }}</strong><br><small>{{ Str::limit($etape->description, 60) }}</small></td>
                                        <td>{{ $etape->chefChantier->nom ?? 'Non assigné' }}</td>
                                        <td>
                                            {{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}<br>
                                            <i class="fas fa-arrow-down"></i><br>
                                            {{ Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $etape->progression }}%"></div>
                                                </div>
                                                <span>{{ $etape->progression }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($etape->statut == 'termine')
                                                <span class="badge bg-success">Terminée</span>
                                            @elseif($etape->statut == 'en_cours')
                                                <span class="badge bg-primary">En cours</span>
                                            @elseif($etape->statut == 'en_retard')
                                                <span class="badge bg-danger">En retard</span>
                                            @else
                                                <span class="badge bg-secondary">À venir</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    {{-- Pied de rapport --}}
                    <div class="footer mt-4 pt-3 text-center text-muted border-top">
                        <small>Rapport généré le {{ Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY à HH:mm') }}</small>
                        <br>
                        <small>ConstrucTrack - Système de suivi de projets</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.btn-back, .btn-print {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 40px;
    color: #64748b;
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.3s;
}
.btn-back:hover, .btn-print:hover {
    border-color: #1a56db;
    color: #1a56db;
}
.info-group label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    display: block;
    margin-bottom: 0.25rem;
}
.info-group p {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0;
}
.stat-card {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: 800;
}
.stat-label {
    font-size: 0.7rem;
    color: #64748b;
}
@media print {
    .btn-back, .btn-print, .cp-sidebar, .cp-topbar, .no-print {
        display: none !important;
    }
    .cp-main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
@endsection