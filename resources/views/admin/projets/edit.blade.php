{{-- resources/views/admin/projets/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modifier le projet - Admin')

@section('page-title', 'Modifier le projet')
@section('page-subtitle', $projet->nom)

@section('content')
<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h4><i class="fas fa-edit"></i> MODIFIER LE PROJET</h4>
            <p>Modifiez les informations du projet</p>
        </div>
        
        <form method="POST" action="{{ route('admin.projets.update', $projet->id) }}">
            @csrf
            @method('PUT')
            
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>NOM DU PROJET <span class="required">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                                   value="{{ old('nom', $projet->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CODE PROJET</label>
                            <input type="text" class="form-control" value="{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id,4,'0',STR_PAD_LEFT) }}" readonly disabled>
                            <small class="form-text text-muted">Le code projet est généré automatiquement</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>LIEU <span class="required">*</span></label>
                            <input type="text" name="lieu" class="form-control @error('lieu') is-invalid @enderror" 
                                   value="{{ old('lieu', $projet->lieu) }}" required>
                            @error('lieu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>STATUT</label>
                            <select name="statut" class="form-control">
                                <option value="en_attente" {{ $projet->statut == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                                <option value="en_cours" {{ $projet->statut == 'en_cours' ? 'selected' : '' }}>▶️ En cours</option>
                                <option value="termine" {{ $projet->statut == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                                <option value="annule" {{ $projet->statut == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>DATE DE DÉBUT PRÉVUE <span class="required">*</span></label>
                            <input type="date" name="date_debut_prevue" class="form-control @error('date_debut_prevue') is-invalid @enderror" 
                                   value="{{ old('date_debut_prevue', $projet->date_debut_prevue->format('Y-m-d')) }}" required>
                            @error('date_debut_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>DATE DE FIN PRÉVUE <span class="required">*</span></label>
                            <input type="date" name="date_fin_prevue" class="form-control @error('date_fin_prevue') is-invalid @enderror" 
                                   value="{{ old('date_fin_prevue', $projet->date_fin_prevue->format('Y-m-d')) }}" required>
                            @error('date_fin_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>CHEF DE PROJET RESPONSABLE <span class="required">*</span></label>
                            <select name="id_chef_projet" class="form-control @error('id_chef_projet') is-invalid @enderror" required>
                                <option value="">-- Sélectionner un chef de projet --</option>
                                @foreach($chefsProjet as $chef)
                                    <option value="{{ $chef->id }}" {{ old('id_chef_projet', $projet->id_chef_projet) == $chef->id ? 'selected' : '' }}>
                                        {{ $chef->nom }} ({{ $chef->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_chef_projet')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>DESCRIPTION <span class="required">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="5" required>{{ old('description', $projet->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                
                @if($projet->statut == 'annule')
                <div class="alert-annulation">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Projet annulé</strong><br>
                        <small>Motif: {{ $projet->motif_annulation }}</small><br>
                        <small>Annulé le: {{ \Carbon\Carbon::parse($projet->annule_le)->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="form-footer">
                <a href="{{ route('admin.projets.show', $projet->id) }}" class="btn-cancel">ANNULER</a>
                <button type="submit" class="btn-submit">ENREGISTRER</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.form-header {
    padding: 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.form-header h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-header p {
    margin: 0.25rem 0 0;
    font-size: 0.75rem;
    color: #64748b;
}

.form-body {
    padding: 1.5rem;
}

.row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.col-12 {
    grid-column: span 2;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.7rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.required {
    color: #dc2626;
}

.form-control {
    width: 100%;
    padding: 0.6rem 0.75rem;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #1a56db;
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

.form-control[readonly],
.form-control[disabled] {
    background: #f1f5f9;
    color: #64748b;
    cursor: not-allowed;
}

.form-text {
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 0.25rem;
    display: block;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.invalid-feedback {
    color: #dc2626;
    font-size: 0.7rem;
    margin-top: 0.25rem;
}

.alert-annulation {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.alert-annulation i {
    font-size: 1.25rem;
    color: #dc2626;
}

.alert-annulation strong {
    font-size: 0.7rem;
    text-transform: uppercase;
}

.alert-annulation small {
    font-size: 0.65rem;
    color: #b91c1c;
}

.form-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-cancel, .btn-submit {
    padding: 0.5rem 1.5rem;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel {
    background: #f1f5f9;
    color: #64748b;
    text-decoration: none;
}

.btn-cancel:hover {
    background: #e2e8f0;
}

.btn-submit {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: white;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26,86,219,0.3);
}

@media (max-width: 768px) {
    .row {
        grid-template-columns: 1fr;
    }
    .col-12 {
        grid-column: span 1;
    }
    .form-footer {
        flex-direction: column;
    }
    .btn-cancel, .btn-submit {
        text-align: center;
    }
}
</style>
@endpush
@endsection