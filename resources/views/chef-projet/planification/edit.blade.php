@extends('layouts.chef-projet')

@section('title', 'Modifier une étape - Chef de Projet')

@section('page-title', 'Modifier une étape')
@section('page-subtitle', $etape->nom_etape . ' - ' . $planification->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="mb-3">
                <a href="{{ route('chef-projet.planification.etapes', $planification->id) }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Retour aux étapes
                </a>
            </div>
            
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Modifier l'étape</h4>
                    <p class="mb-0 mt-1 small">{{ $planification->nom }} - {{ $etape->nom_etape }}</p>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('chef-projet.planification.update-etape', [$planification->id, $etape->id]) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom de l'étape</label>
                            <input type="text" name="nom_etape" class="form-control" value="{{ $etape->nom_etape }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ $etape->description }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut_prevue" class="form-control" value="{{ $etape->date_debut_prevue->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date de fin</label>
                                <input type="date" name="date_fin_prevue" class="form-control" value="{{ $etape->date_fin_prevue->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label class="form-label fw-bold">Chef de chantier responsable</label>
                            <select name="id_chef_chantier" class="form-select" required>
                                <option value="">-- Sélectionner un chef de chantier --</option>
                                @foreach($chefsChantier as $chef)
                                    <option value="{{ $chef->id }}" {{ $etape->id_chef_chantier == $chef->id ? 'selected' : '' }}>
                                        👷 {{ $chef->nom }} ({{ $chef->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informations :</strong> La progression et le statut sont gérés par le chef de chantier.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('chef-projet.planification.etapes', $planification->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
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
    border-color: #1a56db;
    color: #1a56db;
    transform: translateX(-3px);
}
.card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}
.form-control:focus, .form-select:focus {
    border-color: #1a56db;
    box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
}
</style>
@endpush
@endsection