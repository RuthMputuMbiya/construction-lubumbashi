{{-- resources/views/admin/users/show.blade.php --}}
{{-- 
    PAGE AFFICHAGE DÉTAILS D'UN UTILISATEUR (ADMIN)
    EXPLICATION: Affiche toutes les informations d'un utilisateur
    DESIGN CARRÉ - PROFESSIONNEL
--}}

@extends('layouts.admin')

@section('title', 'Détails utilisateur - Jenovic')

@section('page-title', 'Détails de l\'utilisateur')
@section('page-subtitle', 'Informations complètes du compte')

@section('content')
<div class="user-detail-container">
    
    {{-- Fil d'Ariane --}}
    <div class="breadcrumb-wrapper">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Utilisateurs</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">{{ $user->name }}</span>
    </div>
    
    <div class="detail-grid">
        <!-- Carte Profil -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h4 class="profile-name">{{ $user->name }}</h4>
                <p class="profile-role">
                    @if($user->role == 'admin')
                        <i class="fas fa-crown"></i> Administrateur
                    @elseif($user->role == 'chef_projet')
                        <i class="fas fa-chart-line"></i> Chef de Projet
                    @else
                        <i class="fas fa-hard-hat"></i> Chef de Chantier
                    @endif
                </p>
                <p class="profile-id">ID: #{{ $user->id }}</p>
            </div>
            
            <div class="profile-body">
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-phone"></i> Téléphone</span>
                    <span class="info-value">{{ $attributs['telephone'] ?? 'Non renseigné' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-building"></i> Entreprise</span>
                    <span class="info-value">{{ $attributs['entreprise'] ?? 'Non renseigné' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar-alt"></i> Inscrit le</span>
                    <span class="info-value">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-circle"></i> Statut</span>
                    <span class="info-value">
                        @if(($attributs['status'] ?? 'actif') == 'actif')
                            <span class="status-badge status-active">Actif</span>
                        @elseif(($attributs['status'] ?? '') == 'suspendu')
                            <span class="status-badge status-suspended">Suspendu</span>
                        @else
                            <span class="status-badge status-inactive">Désactivé</span>
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="profile-footer">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> Modifier le profil
                </a>
            </div>
        </div>
        
        <!-- Projets créés -->
        <div class="projects-card">
            <div class="card-header-custom">
                <div>
                    <i class="fas fa-project-diagram"></i>
                    <h5>Projets créés</h5>
                </div>
                <span class="projects-count">{{ is_countable($projets) ? count($projets) : 0 }} projet(s)</span>
            </div>
            
            <div class="projects-list">
                @if($user->role == 'chef_projet')
                    @forelse($projets as $projet)
                        <div class="project-item">
                            <div class="project-info">
                                <div class="project-code">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <h6 class="project-name">{{ $projet->nom }}</h6>
                                <div class="project-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}
                                </div>
                            </div>
                            <div class="project-stats">
                                <div class="stat-badge">
                                    <span class="stat-label">Statut</span>
                                    @if($projet->statut == 'en_cours')
                                        <span class="status-badge status-primary">En cours</span>
                                    @elseif($projet->statut == 'termine')
                                        <span class="status-badge status-success">Terminé</span>
                                    @else
                                        <span class="status-badge status-warning">En attente</span>
                                    @endif
                                </div>
                                <div class="stat-badge">
                                    <span class="stat-label">Date</span>
                                    <span class="stat-value">{{ $projet->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.projets.show', $projet->id) }}" class="project-link" title="Voir le projet">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>Aucun projet créé pour le moment</p>
                        </div>
                    @endforelse
                @elseif($user->role == 'chef_chantier')
                    <div class="info-message">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Informations complémentaires</strong>
                            <p>Ce chef de chantier n'a pas encore été assigné à des étapes de projet.</p>
                        </div>
                    </div>
                @else
                    <div class="info-message">
                        <i class="fas fa-user-cog"></i>
                        <div>
                            <strong>Compte administrateur</strong>
                            <p>Les administrateurs n'ont pas de projets associés.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   DÉTAILS UTILISATEUR - DESIGN CARRÉ
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

.user-detail-container {
    max-width: 1400px;
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
   GRILLE PRINCIPALE
   ========================================== */
.detail-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 1.5rem;
}

/* ==========================================
   CARTE PROFIL
   ========================================== */
.profile-card {
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
    height: fit-content;
}

.profile-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.profile-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--white);
    border: 2px solid rgba(255,255,255,0.3);
}

.profile-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--white);
    margin-bottom: 0.25rem;
}

.profile-role {
    font-size: 0.813rem;
    color: rgba(255,255,255,0.7);
    margin-bottom: 0.5rem;
}

.profile-role i {
    margin-right: 0.375rem;
}

.profile-id {
    font-size: 0.688rem;
    color: rgba(255,255,255,0.5);
    font-family: monospace;
}

.profile-body {
    padding: 1.25rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.813rem;
    color: var(--gray);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-label i {
    width: 18px;
    font-size: 0.813rem;
}

.info-value {
    font-size: 0.813rem;
    font-weight: 500;
    color: var(--dark);
}

.profile-footer {
    padding: 1rem 1.25rem 1.25rem;
    display: flex;
    gap: 0.75rem;
    border-top: 1px solid var(--border);
    background: var(--gray-light);
}

.btn-edit {
    flex: 1;
    text-align: center;
    padding: 0.625rem;
    background: var(--primary);
    color: var(--white);
    text-decoration: none;
    font-size: 0.813rem;
    font-weight: 500;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-edit:hover {
    background: var(--primary-dark);
}

/* ==========================================
   CARTE PROJETS
   ========================================== */
.projects-card {
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-header-custom {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-custom i {
    color: var(--primary);
    margin-right: 0.5rem;
}

.card-header-custom h5 {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
    display: inline;
}

.projects-count {
    background: var(--gray-light);
    padding: 0.25rem 0.75rem;
    font-size: 0.688rem;
    font-weight: 500;
    color: var(--gray);
}

.projects-list {
    padding: 0;
}

.project-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.2s;
}

.project-item:hover {
    background: #fafbfc;
}

.project-info {
    flex: 2;
}

.project-code {
    font-size: 0.688rem;
    color: var(--gray);
    letter-spacing: 0.5px;
    font-family: monospace;
}

.project-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--dark);
    margin: 0.25rem 0;
}

.project-location {
    font-size: 0.688rem;
    color: var(--gray);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.project-location i {
    font-size: 0.625rem;
}

.project-stats {
    display: flex;
    gap: 1rem;
    margin-right: 1rem;
}

.stat-badge {
    text-align: left;
}

.stat-label {
    font-size: 0.625rem;
    color: var(--gray);
    display: block;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--dark);
}

.project-link {
    width: 32px;
    height: 32px;
    background: var(--gray-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray);
    transition: all 0.2s;
}

.project-link:hover {
    background: var(--primary);
    color: var(--white);
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    font-size: 0.688rem;
    font-weight: 500;
}

.status-active {
    background: #d1fae5;
    color: #059669;
}

.status-suspended {
    background: #fef3c7;
    color: #d97706;
}

.status-inactive {
    background: #fee2e2;
    color: #dc2626;
}

.status-primary {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-success {
    background: #d1fae5;
    color: #059669;
}

.status-warning {
    background: #fef3c7;
    color: #d97706;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--gray);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 0.813rem;
}

/* Info message */
.info-message {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    background: #eff6ff;
    border-left: 3px solid var(--primary);
    margin: 1rem;
}

.info-message i {
    color: var(--primary);
    font-size: 1.25rem;
}

.info-message strong {
    display: block;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    color: var(--dark);
}

.info-message p {
    margin: 0;
    font-size: 0.813rem;
    color: var(--gray);
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .project-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .project-stats {
        margin-right: 0;
        flex-wrap: wrap;
    }
    
    .profile-footer {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .profile-avatar {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .profile-name {
        font-size: 1rem;
    }
    
    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>
@endpush
@endsection