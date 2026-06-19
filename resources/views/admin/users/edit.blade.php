{{-- resources/views/admin/users/edit.blade.php --}}
{{-- 
    PAGE MODIFICATION D'UN UTILISATEUR (ADMIN)
    EXPLICATION: Formulaire pour modifier les informations d'un utilisateur existant
    DESIGN CARRÉ - PROFESSIONNEL - COLORÉ
--}}

@extends('layouts.admin')

@section('title', 'Modifier un utilisateur - Jenovic')

@section('page-title', 'Modifier un utilisateur')
@section('page-subtitle', 'Modifiez les informations de l\'utilisateur')

@section('content')
<div class="edit-user-container">
    
    {{-- Fil d'Ariane --}}
    <div class="breadcrumb-wrapper">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Utilisateurs</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Modifier {{ $user->name }}</span>
    </div>
    
    <div class="edit-user-card">
        <div class="card-header-custom">
            <div class="header-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="header-info">
                <h4>MODIFIER L'UTILISATEUR</h4>
                <p>Modifiez les informations de {{ $user->name }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')
            
            <div class="card-body-custom">
                <div class="form-grid">
                    
                    {{-- Nom complet --}}
                    <div class="form-group">
                        <label class="form-label">NOM COMPLET <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required placeholder="Jean Dupont">
                        </div>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- Email --}}
                    <div class="form-group">
                        <label class="form-label">EMAIL <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required placeholder="exemple@email.com">
                        </div>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- Téléphone --}}
                    <div class="form-group">
                        <label class="form-label">TÉLÉPHONE</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" name="telephone" class="form-input" 
                                   value="{{ old('telephone', $attributs['telephone'] ?? '') }}" placeholder="+243 XXX XXX XXX">
                        </div>
                    </div>
                    
                    {{-- Entreprise --}}
                    <div class="form-group">
                        <label class="form-label">ENTREPRISE / SOCIÉTÉ</label>
                        <div class="input-wrapper">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" name="entreprise" class="form-input" 
                                   value="{{ old('entreprise', $attributs['entreprise'] ?? '') }}" placeholder="Nom de l'entreprise">
                        </div>
                    </div>
                    
                    {{-- NOUVEAU MOT DE PASSE (Optionnel) --}}
                    <div class="form-group">
                        <label class="form-label">NOUVEAU MOT DE PASSE</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" 
                                   placeholder="Laisser vide pour conserver l'actuel">
                        </div>
                        <small class="form-hint">Laissez vide pour conserver le mot de passe actuel</small>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- CONFIRMER LE NOUVEAU MOT DE PASSE --}}
                    <div class="form-group">
                        <label class="form-label">CONFIRMER LE MOT DE PASSE</label>
                        <div class="input-wrapper">
                            <i class="fas fa-check-circle input-icon"></i>
                            <input type="password" name="password_confirmation" class="form-input" 
                                   placeholder="Confirmer le nouveau mot de passe">
                        </div>
                    </div>
                </div>
                
                {{-- Rôle (Admin non modifiable) --}}
                <div class="role-section">
                    <label class="form-label">RÔLE <span class="required">*</span></label>
                    <div class="role-selector">
                        <label class="role-option">
                            <input type="radio" name="role" value="chef_projet" {{ old('role', $user->role) == 'chef_projet' ? 'checked' : '' }} required>
                            <div class="role-card">
                                <i class="fas fa-chart-line"></i>
                                <div class="role-title">CHEF DE PROJET</div>
                                <div class="role-desc">Gère les projets et la planification</div>
                            </div>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="chef_chantier" {{ old('role', $user->role) == 'chef_chantier' ? 'checked' : '' }}>
                            <div class="role-card">
                                <i class="fas fa-hard-hat"></i>
                                <div class="role-title">CHEF DE CHANTIER</div>
                                <div class="role-desc">Met à jour l'avancement terrain</div>
                            </div>
                        </label>
                        @if($user->role == 'admin')
                        <label class="role-option">
                            <input type="radio" name="role" value="admin" {{ old('role', $user->role) == 'admin' ? 'checked' : '' }}>
                            <div class="role-card">
                                <i class="fas fa-user-shield"></i>
                                <div class="role-title">ADMINISTRATEUR</div>
                                <div class="role-desc">Gestion complète du système</div>
                            </div>
                        </label>
                        @endif
                    </div>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Statut du compte --}}
                <div class="form-group full-width">
                    <label class="form-label">STATUT DU COMPTE</label>
                    <div class="select-wrapper">
                        <i class="fas fa-circle input-icon"></i>
                        <select name="status" class="form-select">
                            <option value="actif" {{ ($attributs['status'] ?? 'actif') == 'actif' ? 'selected' : '' }}>🟢 ACTIF</option>
                            <option value="suspendu" {{ ($attributs['status'] ?? '') == 'suspendu' ? 'selected' : '' }}>🟡 SUSPENDU</option>
                            <option value="desactive" {{ ($attributs['status'] ?? '') == 'desactive' ? 'selected' : '' }}>🔴 DÉSACTIVÉ</option>
                        </select>
                    </div>
                </div>
                
                {{-- Information historique --}}
                <div class="info-box">
                    <i class="fas fa-history"></i>
                    <div>
                        <strong>INFORMATIONS COMPTE</strong>
                        <small>Compte créé le {{ $user->created_at->format('d/m/Y à H:i') }}</small>
                        @if(!empty($attributs['updated_by_name']))
                            <br><small>Dernière modification par {{ $attributs['updated_by_name'] }}</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card-footer-custom">
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> ANNULER
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> ENREGISTRER
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   EDIT USER - DESIGN CARRÉ PROFESSIONNEL
   ============================================ */

:root {
    --primary: #1a56db;
    --primary-dark: #0e3a8a;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --dark: #111827;
    --gray: #6b7280;
    --gray-light: #f3f4f6;
    --white: #ffffff;
    --border: #e2e8f0;
}

.edit-user-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Fil d'Ariane */
.breadcrumb-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    padding: 0.5rem 0;
    flex-wrap: wrap;
}

.breadcrumb-link {
    color: var(--gray);
    text-decoration: none;
    font-size: 0.813rem;
    transition: all 0.2s;
}

.breadcrumb-link:hover {
    color: var(--primary);
}

.breadcrumb-sep {
    color: var(--gray);
    font-size: 0.813rem;
}

.breadcrumb-current {
    color: var(--dark);
    font-weight: 600;
    font-size: 0.813rem;
}

/* Carte principale */
.edit-user-card {
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-header-custom {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 1.25rem;
    color: var(--white);
}

.header-info h4 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--dark);
    letter-spacing: 1px;
}

.header-info p {
    margin: 0.25rem 0 0;
    font-size: 0.75rem;
    color: var(--gray);
}

/* Corps du formulaire */
.card-body-custom {
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: span 2;
}

.form-label {
    font-size: 0.688rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: var(--gray);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.required {
    color: var(--danger);
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 12px;
    color: var(--gray);
    font-size: 0.875rem;
}

.input-wrapper .form-input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    border: 1px solid var(--border);
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

.form-input.is-invalid {
    border-color: var(--danger);
}

.form-error {
    color: var(--danger);
    font-size: 0.688rem;
    margin-top: 0.25rem;
}

.form-hint {
    font-size: 0.688rem;
    color: var(--gray);
    margin-top: 0.25rem;
}

/* Selecteur de rôle */
.role-section {
    margin: 1.5rem 0;
}

.role-selector {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 0.5rem;
}

.role-option {
    flex: 1;
    cursor: pointer;
}

.role-option input {
    display: none;
}

.role-card {
    border: 2px solid var(--border);
    padding: 1rem;
    text-align: center;
    transition: all 0.2s;
    cursor: pointer;
}

.role-option input:checked + .role-card {
    border-color: var(--primary);
    background: rgba(26,86,219,0.05);
    transform: translateY(-2px);
}

.role-card i {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
    display: block;
}

.role-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.role-desc {
    font-size: 0.625rem;
    color: var(--gray);
}

/* Selecteur statut */
.select-wrapper {
    position: relative;
}

.select-wrapper .input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 0.75rem;
}

.form-select {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    border: 1px solid var(--border);
    font-size: 0.875rem;
    background: var(--white);
    cursor: pointer;
    appearance: none;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

/* Boîte d'information */
.info-box {
    background: #f8fafc;
    border: 1px solid var(--border);
    padding: 1rem;
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.info-box i {
    font-size: 1.25rem;
    color: var(--primary);
}

.info-box strong {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--dark);
    display: block;
    margin-bottom: 0.25rem;
}

.info-box small {
    font-size: 0.688rem;
    color: var(--gray);
}

/* Footer avec boutons */
.card-footer-custom {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-cancel {
    background: var(--gray-light);
    color: var(--gray);
    padding: 0.625rem 1.5rem;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.2s;
    border: 1px solid var(--border);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-cancel:hover {
    background: #e5e7eb;
    color: var(--dark);
}

.btn-save {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    padding: 0.625rem 1.5rem;
    border: none;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-save:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-group.full-width {
        grid-column: span 1;
    }
    
    .role-selector {
        grid-template-columns: 1fr;
    }
    
    .card-footer-custom {
        flex-direction: column;
    }
    
    .btn-cancel, .btn-save {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .card-header-custom {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    .card-body-custom {
        padding: 1rem;
    }
}
</style>
@endpush
@endsection