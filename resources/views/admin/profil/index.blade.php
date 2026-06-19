{{-- resources/views/admin/profil/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Mon profil - Admin')

@section('page-title', 'Mon profil')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10">
                <i class="fas fa-user-shield text-primary fs-3"></i>
            </div>
            <div>
                <h3 class="stat-value">{{ $user->nom }}</h3>
                <p class="stat-label">Administrateur</p>
            </div>
        </div>
        <div class="table-container">
            <div class="p-3">
                <p><i class="fas fa-envelope me-2"></i> {{ $user->email }}</p>
                <p><i class="fas fa-phone me-2"></i> {{ $attributs['telephone'] ?? 'Non renseigné' }}</p>
                <p><i class="fas fa-calendar me-2"></i> Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
                <p><i class="fas fa-key me-2"></i> Dernier changement: {{ $attributs['last_password_change'] ?? 'Jamais' }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="table-container mb-4">
            <div class="p-3">
                <h5 class="mb-3"><i class="fas fa-edit me-2"></i> Modifier mes informations</h5>
                <form method="POST" action="{{ route('admin.profil.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="nom" class="form-control" value="{{ old('nom', $user->nom) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $attributs['telephone'] ?? '') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-primary-custom"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="table-container">
            <div class="p-3">
                <h5 class="mb-3"><i class="fas fa-key me-2"></i> Changer mon mot de passe</h5>
                <form method="POST" action="{{ route('admin.profil.change-password') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mot de passe actuel</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirmer</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-warning-custom" style="background: #f59e0b; color: white; padding: 0.5rem 1.2rem; border-radius: 40px; border: none;"><i class="fas fa-key me-2"></i>Changer mot de passe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection