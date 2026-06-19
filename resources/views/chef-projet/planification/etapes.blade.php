@extends('layouts.chef-projet')

@section('title', 'Planification des étapes - Chef de Projet')

@section('page-title', 'Planification des étapes')
@section('page-subtitle', $planification->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <a href="{{ route('chef-projet.planification.index', ['idProjet' => $planification->id]) }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Retour à la planification
                </a>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-tasks me-2"></i> {{ $planification->nom }} - Planification des étapes</h4>
                    <p class="mb-0 mt-1 small opacity-75"><i class="fas fa-map-marker-alt me-1"></i> {{ $planification->lieu }}</p>
                </div>
                
                <div class="card-body">
                    {{-- ALERTE CHEF DE PROJET RESPONSABLE --}}
                    <div class="alert alert-primary mb-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-tie fa-2x me-3"></i>
                            </div>
                            <div>
                                <strong>CHEF DE PROJET RESPONSABLE</strong><br>
                                <span class="fs-5">{{ $planification->responsable ?? Auth::user()->nom }}</span>
                                <small class="d-block">Ce projet est sous la supervision de ce chef de projet</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Ordre logique des étapes :</strong> ANALYSE DES BESOINS (01) → ÉTUDE DU SITE (02) → ÉTUDE DE FAISABILITÉ (03) → CONCEPTION (04) → VALIDATION (05)
                    </div>
                    
                    {{-- Grille des étapes prédéfinies --}}
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="mb-3"><i class="fas fa-list-ol text-primary me-2"></i> Étapes obligatoires à planifier :</h5></div>
                        @foreach($etapesObligatoires as $ordre => $e)
                        @php $dejaPlanifie = $etapes->contains('nom_etape', $e['nom']); @endphp
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="card h-100 {{ $dejaPlanifie ? 'border-success bg-success-light' : 'border-secondary' }}">
                                <div class="card-body text-center">
                                    <div class="display-4 mb-2 text-primary">{{ str_pad($ordre, 2, '0', STR_PAD_LEFT) }}</div>
                                    <h6 class="fw-bold">{{ $e['nom'] }}</h6>
                                    <small class="text-muted d-block mb-2">{{ Str::limit($e['desc'], 60) }}</small>
                                    <div class="mt-2">
                                        @if(!$dejaPlanifie)
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEtape" data-nom="{{ $e['nom'] }}" data-description="{{ $e['desc'] }}" data-ordre="{{ $ordre }}"><i class="fas fa-plus me-1"></i> Planifier</button>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Planifiée</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Liste des étapes déjà planifiées --}}
                    @if($etapes->count() > 0)
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-check-circle text-success me-2"></i> Étapes planifiées ({{ $etapes->count() }}/5)</h5>
                            <span class="badge bg-primary">Chef de projet: {{ $planification->responsable ?? Auth::user()->nom }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:60px">Ordre</th>
                                        <th>Étape</th>
                                        <th style="width:220px">Chef de chantier</th>
                                        <th style="width:200px">Dates prévues</th>
                                        <th style="width:100px">Progression</th>
                                        <th style="width:120px">Statut</th>
                                        <th style="width:150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etapes->sortBy('ordre') as $etape)
                                    <tr>
                                        <td class="text-center fw-bold">{{ str_pad($etape->ordre, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <strong>{{ $etape->nom_etape }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($etape->description, 60) }}</small>
                                        </td>
                                        <td>
                                            @if($etape->chefChantier)
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <span><i class="fas fa-user-circle text-primary me-1"></i> {{ $etape->chefChantier->nom }}</span>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" onclick="ouvrirModalModifierChef({{ $planification->id }}, {{ $etape->id }}, '{{ addslashes($etape->nom_etape) }}')" class="btn btn-sm btn-warning" title="Modifier le chef">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" onclick="retirerChef({{ $planification->id }}, {{ $etape->id }})" class="btn btn-sm btn-outline-danger" title="Retirer le chef">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Non assigné</span>
                                                    <button type="button" onclick="ouvrirModalAssignerChef({{ $planification->id }}, {{ $etape->id }}, '{{ addslashes($etape->nom_etape) }}')" class="btn btn-sm btn-primary" title="Assigner un chef">
                                                        <i class="fas fa-user-plus"></i> Assigner
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar-alt text-primary me-1"></i> {{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}<br>
                                            <i class="fas fa-arrow-right text-muted mx-1"></i>
                                            <i class="fas fa-calendar-check text-primary me-1"></i> {{ Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td style="min-width:120px">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px">
                                                    <div class="progress-bar bg-success" style="width:{{ $etape->progression }}%"></div>
                                                </div>
                                                <span class="small fw-bold">{{ $etape->progression }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($etape->statut == 'termine') 
                                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Terminée</span>
                                            @elseif($etape->statut == 'en_cours') 
                                                <span class="badge bg-primary"><i class="fas fa-play-circle"></i> En cours</span>
                                            @elseif($etape->statut == 'en_retard') 
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> En retard</span>
                                            @else 
                                                <span class="badge bg-secondary"><i class="fas fa-clock"></i> À venir</span> 
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('chef-projet.planification.edit-etape', [$planification->id, $etape->id]) }}" class="btn btn-sm btn-info" title="Modifier l'étape">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button type="button" onclick="supprimerEtape({{ $planification->id }}, {{ $etape->id }})" class="btn btn-sm btn-danger" title="Supprimer l'étape">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($etapes->count() >= 5)
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Toutes les étapes sont planifiées et assignées !</strong> Vous pouvez maintenant créer votre projet.
                        </div>
                        <form action="{{ route('chef-projet.planification.transformer', $planification->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-exchange-alt me-2"></i> CRÉER LE PROJET
                            </button>
                        </form>
                        @else
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            Vous devez planifier les 5 étapes obligatoires avant de créer le projet. ({{ $etapes->count() }}/5 étapes planifiées)
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL POUR AJOUTER UNE ÉTAPE --}}
<div class="modal fade" id="modalEtape" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i> Planifier une étape</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEtape" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="ordre" id="modal_ordre">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de l'étape</label>
                        <input type="text" name="nom_etape" id="modal_nom" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" id="modal_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de début</label>
                            <input type="date" name="date_debut_prevue" id="modal_date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de fin</label>
                            <input type="date" name="date_fin_prevue" id="modal_date_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Chef de chantier responsable</label>
                        <select name="id_chef_chantier" class="form-select" required>
                            <option value="">-- Sélectionner un chef de chantier --</option>
                            @foreach($chefsChantier as $chef)
                                <option value="{{ $chef->id }}">{{ $chef->nom }} ({{ $chef->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter l'étape</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL POUR MODIFIER LE CHEF DE CHANTIER --}}
<div class="modal fade" id="modalModifierChef" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i> Modifier le chef de chantier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModifierChef" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Étape concernée</label>
                        <input type="text" id="modal_etape_nom" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nouveau chef de chantier</label>
                        <select name="id_chef_chantier" class="form-select" required>
                            <option value="">-- Sélectionner un chef de chantier --</option>
                            @foreach($chefsChantier as $chef)
                                <option value="{{ $chef->id }}">{{ $chef->nom }} ({{ $chef->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Cette action remplacera le chef de chantier actuel par le nouveau sélectionné.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Modifier le chef</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL POUR ASSIGNER UN CHEF DE CHANTIER --}}
<div class="modal fade" id="modalAssignerChef" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Assigner un chef de chantier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAssignerChef" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Étape concernée</label>
                        <input type="text" id="modal_assign_etape_nom" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chef de chantier</label>
                        <select name="id_chef_chantier" class="form-select" required>
                            <option value="">-- Sélectionner un chef de chantier --</option>
                            @foreach($chefsChantier as $chef)
                                <option value="{{ $chef->id }}">{{ $chef->nom }} ({{ $chef->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display:none;">@csrf @method('DELETE')</form>

@push('styles')
<style>
.btn-back{display:inline-flex;align-items:center;padding:.5rem 1rem;background:white;border:1px solid #e2e8f0;border-radius:40px;color:#64748b;text-decoration:none;font-size:.8rem;transition:all .3s}
.btn-back:hover{border-color:#1a56db;color:#1a56db;transform:translateX(-3px)}
.bg-success-light{background-color:#d1fae5!important}
.card{border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.05)}
.card-header{background:linear-gradient(135deg,#1a56db,#0e3a8a)}
.table th,.table td{vertical-align:middle}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('modalEtape');
    modal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        document.getElementById('modal_nom').value = button.getAttribute('data-nom');
        document.getElementById('modal_description').value = button.getAttribute('data-description');
        document.getElementById('modal_ordre').value = button.getAttribute('data-ordre');
        
        var today = new Date().toISOString().split('T')[0];
        var nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        document.getElementById('modal_date_debut').value = today;
        document.getElementById('modal_date_fin').value = nextMonth.toISOString().split('T')[0];
        
        document.getElementById('formEtape').action = '/chef-projet/planification/{{ $planification->id }}/etapes';
    });
});

function supprimerEtape(planificationId, etapeId) {
    if(confirm('⚠️ Supprimer cette étape ? Cette action est irréversible.')) {
        var form = document.getElementById('delete-form');
        form.action = '/chef-projet/planification/' + planificationId + '/etapes/' + etapeId;
        form.submit();
    }
}

function retirerChef(planificationId, etapeId) {
    if(confirm('Retirer le chef de chantier de cette étape ?')) {
        var form = document.getElementById('delete-form');
        form.action = '/chef-projet/planification/' + planificationId + '/etapes/' + etapeId + '/retirer-chef';
        form.submit();
    }
}

function ouvrirModalModifierChef(planificationId, etapeId, etapeNom) {
    document.getElementById('modal_etape_nom').value = etapeNom;
    var form = document.getElementById('formModifierChef');
    form.action = '/chef-projet/planification/' + planificationId + '/etapes/' + etapeId + '/modifier-chef';
    var modal = new bootstrap.Modal(document.getElementById('modalModifierChef'));
    modal.show();
}

function ouvrirModalAssignerChef(planificationId, etapeId, etapeNom) {
    document.getElementById('modal_assign_etape_nom').value = etapeNom;
    var form = document.getElementById('formAssignerChef');
    form.action = '/chef-projet/planification/' + planificationId + '/etapes/' + etapeId + '/assigner-chef';
    var modal = new bootstrap.Modal(document.getElementById('modalAssignerChef'));
    modal.show();
}
</script>
@endpush
@endsection