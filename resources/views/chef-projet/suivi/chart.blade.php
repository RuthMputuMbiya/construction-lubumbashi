@extends('layouts.chef-projet')

@section('title', 'Graphique avancement - Chef de Projet')

@section('page-title', 'Graphique d\'avancement')
@section('page-subtitle', $projet->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <a href="{{ route('chef-projet.suivi.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Retour au suivi
                </a>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Graphique d'avancement - {{ $projet->nom }}</h5>
                </div>
                <div class="card-body">
                    
                    {{-- Résumé du projet --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h3>{{ $avancement_global }}%</h3>
                                <small>Avancement global</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h3>{{ $stats['terminees'] }}/{{ $stats['total_etapes'] }}</h3>
                                <small>Étapes terminées</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h3>{{ $stats['en_retard'] }}</h3>
                                <small>Étapes en retard</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <h3>{{ $projet->budget_formate ?? number_format($projet->budget_total, 0, ',', ' ') . ' ' . ($projet->devise ?? 'FC') }}</h3>
                                <small>Budget</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Graphique en barres --}}
                    <div class="mb-4">
                        <canvas id="avancementChart" height="300"></canvas>
                    </div>
                    
                    {{-- Légende --}}
                    <div class="legend mb-4">
                        <div class="d-flex justify-content-center gap-4">
                            <div><span class="legend-color bg-success"></span> Terminée (≥100%)</div>
                            <div><span class="legend-color bg-primary"></span> En cours (1-99%)</div>
                            <div><span class="legend-color bg-danger"></span> En retard</div>
                            <div><span class="legend-color bg-secondary"></span> Non commencée (0%)</div>
                        </div>
                    </div>
                    
                    {{-- Tableau des données --}}
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
                                    <td><strong>{{ $etape->nom_etape }}</strong><br><small class="text-muted">{{ Str::limit($etape->description, 50) }}</small></td>
                                    <td>
                                        @if($etape->chefChantier)
                                            <i class="fas fa-hard-hat"></i> {{ $etape->chefChantier->nom }}
                                        @else
                                            Non assigné
                                        @endif
                                    </td>
                                    <td>
                                        {{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}<br>
                                        <i class="fas fa-arrow-down"></i><br>
                                        {{ Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: {{ $etape->progression }}%"></div>
                                            </div>
                                            <span class="fw-bold">{{ $etape->progression }}%</span>
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
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.btn-back {
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
.btn-back:hover {
    border-color: #214186;
    color: #1a56db;
    transform: translateX(-3px);
}
.info-card {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.info-card h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    color: #1a56db;
}
.legend-color {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 4px;
    margin-right: 8px;
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('avancementChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Progression (%)',
                data: @json($chartData['progressions']),
                backgroundColor: @json($chartData['couleurs']),
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Progression (%)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Étapes'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Progression: ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection