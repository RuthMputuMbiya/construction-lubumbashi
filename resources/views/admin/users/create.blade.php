{{-- resources/views/admin/users/create.blade.php --}}
{{-- 
    PAGE CRÉATION D'UN UTILISATEUR (ADMIN)
    EXPLICATION: Formulaire pour créer un nouveau compte utilisateur
    DESIGN CARRÉ - PROFESSIONNEL
--}}

@extends('layouts.admin')

@section('title', 'Créer un utilisateur - Jenovic')

@section('page-title', 'Créer un nouvel utilisateur')
@section('page-subtitle', 'Ajouter un compte utilisateur au système')

@section('content')
<div class="user-create-container">
    
    {{-- Fil d'Ariane --}}
    <div class="breadcrumb-wrapper">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Utilisateurs</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Créer un utilisateur</span>
    </div>
    
    <div class="create-card">
        <div class="create-card-header">
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="header-info">
                <h5>INFORMATIONS GÉNÉRALES</h5>
                <p>Remplissez les informations pour créer un nouveau compte utilisateur</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="create-card-body">
                <div class="form-grid">
                    
                    {{-- Nom complet --}}
                    <div class="form-group">
                        <label class="form-label">NOM COMPLET <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required placeholder="Jean Dupont">
                        </div>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    {{-- Email --}}
                    <div class="form-group">
                        <label class="form-label">EMAIL <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required placeholder="exemple@email.com">
                        </div>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    {{-- Téléphone --}}
                    <div class="form-group">
                        <label class="form-label">TÉLÉPHONE</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" name="telephone" class="form-control" 
                                   value="{{ old('telephone') }}" placeholder="+243 XXX XXX XXX">
                        </div>
                    </div>
                    
                    {{-- Entreprise --}}
                    <div class="form-group">
                        <label class="form-label">ENTREPRISE / SOCIÉTÉ</label>
                        <div class="input-wrapper">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" name="entreprise" class="form-control" 
                                   value="{{ old('entreprise') }}" placeholder="Nom de l'entreprise">
                        </div>
                    </div>
                    
                    {{-- MOT DE PASSE --}}
                    <div class="form-group">
                        <label class="form-label">MOT DE PASSE <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   required placeholder="••••••••">
                        </div>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    {{-- CONFIRMER MOT DE PASSE --}}
                    <div class="form-group">
                        <label class="form-label">CONFIRMER LE MOT DE PASSE <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-check-circle input-icon"></i>
                            <input type="password" name="password_confirmation" class="form-control" 
                                   required placeholder="Confirmer le mot de passe">
                        </div>
                    </div>
                </div>
                
                {{-- Rôle --}}
                <div class="role-section">
                    <label class="form-label">RÔLE <span class="required">*</span></label>
                    <div class="role-selector">
                        <label class="role-option">
                            <input type="radio" name="role" value="chef_projet" {{ old('role') == 'chef_projet' ? 'checked' : '' }} required>
                            <div class="role-card">
                                <i class="fas fa-chart-line"></i>
                                <div class="role-title">CHEF DE PROJET</div>
                                <div class="role-desc">Gère les projets et la planification</div>
                            </div>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="chef_chantier" {{ old('role') == 'chef_chantier' ? 'checked' : '' }}>
                            <div class="role-card">
                                <i class="fas fa-hard-hat"></i>
                                <div class="role-title">CHEF DE CHANTIER</div>
                                <div class="role-desc">Met à jour l'avancement terrain</div>
                            </div>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="admin" {{ old('role') == 'admin' ? 'checked' : '' }}>
                            <div class="role-card">
                                <i class="fas fa-user-shield"></i>
                                <div class="role-title">ADMINISTRATEUR</div>
                                <div class="role-desc">Gestion complète du système</div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                {{-- Info box --}}
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>INFORMATIONS IMPORTANTES</strong>
                        <p>L'utilisateur pourra modifier son mot de passe lors de sa première connexion.</p>
                    </div>
                </div>
            </div>
            
            <div class="create-card-footer">
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> ANNULER
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> CRÉER L'UTILISATEUR
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   CRÉATION UTILISATEUR - DESIGN CARRÉ
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
    --border: #e5e7eb;
}

.user-create-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 15px;
}

/* ==========================================
   FIL D'ARIANE
   ========================================== */
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

/* ==========================================
   CARTE FORMULAIRE
   ========================================== */
.create-card {
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.create-card-header {
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

.header-info h5 {
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

/* ==========================================
   CORPS DU FORMULAIRE
   ========================================== */
.create-card-body {
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.688rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.required {
    color: var(--danger);
    margin-left: 0.25rem;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 0.875rem;
    pointer-events: none;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.25rem;
    border: 1px solid var(--border);
    font-size: 0.875rem;
    transition: all 0.2s;
    background: var(--white);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

.form-control.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    display: block;
    color: var(--danger);
    font-size: 0.688rem;
    margin-top: 0.25rem;
}

/* ==========================================
   SÉLECTEUR DE RÔLE
   ========================================== */
.role-section {
    margin-bottom: 1.5rem;
}

.role-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 0.5rem;
}

.role-option {
    cursor: pointer;
}

.role-option input {
    display: none;
}

.role-option input:checked + .role-card {
    border-color: var(--primary);
    background: rgba(26,86,219,0.05);
    transform: translateY(-2px);
}

.role-card {
    padding: 1.25rem;
    text-align: center;
    border: 2px solid var(--border);
    transition: all 0.2s;
    cursor: pointer;
}

.role-card i {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 0.75rem;
    display: block;
}

.role-title {
    font-weight: 700;
    color: var(--dark);
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.role-desc {
    font-size: 0.625rem;
    color: var(--gray);
}

/* ==========================================
   INFO BOX
   ========================================== */
.info-box {
    background: #eff6ff;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    border-left: 3px solid var(--primary);
}

.info-box i {
    color: var(--primary);
    font-size: 1.125rem;
    margin-top: 0.125rem;
}

.info-box strong {
    display: block;
    font-size: 0.813rem;
    margin-bottom: 0.25rem;
    color: var(--dark);
}

.info-box p {
    margin: 0;
    font-size: 0.75rem;
    color: var(--gray);
}

/* ==========================================
   FOOTER DU FORMULAIRE
   ========================================== */
.create-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border);
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-cancel {
    background: var(--gray-light);
    padding: 0.625rem 1.5rem;
    color: var(--gray);
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid var(--border);
}

.btn-cancel:hover {
    background: #e5e7eb;
    color: var(--dark);
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    padding: 0.625rem 1.5rem;
    color: var(--white);
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

.btn-submit:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    transform: translateY(-2px);
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .role-selector {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .role-selector {
        grid-template-columns: 1fr;
    }
    
    .create-card-footer {
        flex-direction: column;
    }
    
    .btn-cancel, .btn-submit {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .create-card-body {
        padding: 1rem;
    }
    
    .create-card-header {
        padding: 1rem;
        flex-direction: column;
        text-align: center;
    }
    
    .role-card {
        padding: 1rem;
    }
    
    .role-card i {
        font-size: 1.5rem;
    }
}
</style>
@endpush
@endsection