{{-- resources/views/admin/users/index.blade.php --}}
{{-- 
    PAGE GESTION DES UTILISATEURS (ADMIN)
    DESIGN CARRÉ - PROFESSIONNEL - COLORÉ
--}}

@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs - Jenovic')

@section('page-title', 'Gestion des utilisateurs')
@section('page-subtitle', 'Gérez les comptes des chefs de projet et chefs de chantier')

@section('content')
<div class="users-container">
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES RAPIDES (CARTES CARRÉES) --}}
    {{-- ========================================== --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TOTAL UTILISATEURS</span>
                <span class="stat-value">{{ $stats['total'] ?? $users->total() ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">CHEFS DE PROJET</span>
                <span class="stat-value">{{ $stats['chef_projet'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-hard-hat"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">CHEFS DE CHANTIER</span>
                <span class="stat-value">{{ $stats['chef_chantier'] ?? 0 }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">ADMINISTRATEURS</span>
                <span class="stat-value">{{ $stats['admin'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- BARRE D'ACTIONS --}}
    {{-- ========================================== --}}
    <div class="actions-bar">
        <div class="actions-left">
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <i class="fas fa-user-plus"></i> NOUVEL UTILISATEUR
            </a>
        </div>
        <div class="actions-right">
            <form method="GET" action="{{ route('admin.users.index') }}" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Rechercher par nom, email..." value="{{ request('search') }}">
                </div>
                <select name="role" class="filter-select">
                    <option value="">TOUS LES RÔLES</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                    <option value="chef_projet" {{ request('role') == 'chef_projet' ? 'selected' : '' }}>Chef de projet</option>
                    <option value="chef_chantier" {{ request('role') == 'chef_chantier' ? 'selected' : '' }}>Chef de chantier</option>
                </select>
                <button type="submit" class="btn-filter">FILTRER</button>
                @if(request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn-reset">RÉINITIALISER</a>
                @endif
            </form>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- TABLEAU DES UTILISATEURS --}}
    {{-- ========================================== --}}
    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>UTILISATEUR</th>
                        <th>EMAIL</th>
                        <th>RÔLE</th>
                        <th>TÉLÉPHONE</th>
                        <th>INSCRIT LE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="user-cell">
                            <div class="user-avatar" style="background: {{ $user->role == 'admin' ? '#ef4444' : ($user->role == 'chef_projet' ? '#10b981' : '#3b82f6') }}">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-id">#{{ $user->id }}</div>
                            </div>
                        </td>
                        <td class="user-email">
                            <i class="fas fa-envelope"></i> {{ $user->email }}
                        </td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="role-badge role-admin"><i class="fas fa-crown"></i> ADMINISTRATEUR</span>
                            @elseif($user->role == 'chef_projet')
                                <span class="role-badge role-cp"><i class="fas fa-chart-line"></i> CHEF DE PROJET</span>
                            @else
                                <span class="role-badge role-cc"><i class="fas fa-hard-hat"></i> CHEF DE CHANTIER</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $attributs = is_string($user->attributs ?? '') ? json_decode($user->attributs, true) : ($user->attributs ?? []);
                                $telephone = $attributs['telephone'] ?? ($user->telephone ?? '-');
                            @endphp
                            <i class="fas fa-phone-alt"></i> {{ $telephone }}
                        </td>
                        <td class="date-cell">
                            <i class="fas fa-calendar-alt"></i> {{ isset($user->created_at) ? $user->created_at->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Réinitialisation mot de passe UNIQUEMENT pour les administrateurs --}}
                                @if($user->id !== auth()->id() && $user->role == 'admin')
                                    <button type="button" class="action-btn reset" onclick="resetPassword({{ $user->id }})" title="Réinitialiser mot de passe">
                                        <i class="fas fa-key"></i>
                                    </button>
                                @endif
                                @if($user->id !== auth()->id())
                                    <button type="button" class="action-btn delete" onclick="confirmDelete({{ $user->id }})" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>AUCUN UTILISATEUR TROUVÉ</p>
                                <a href="{{ route('admin.users.create') }}" class="btn-primary-sm">AJOUTER UN UTILISATEUR</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="pagination-wrapper">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ========================================== --}}
{{-- FORMULAIRES CACHÉS --}}
{{-- ========================================== --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="reset-password-form" method="POST" style="display: none;">
    @csrf
</form>

@push('styles')
<style>
/* ============================================
   GESTION DES UTILISATEURS - DESIGN CARRÉ
   ============================================ */

:root {
    --primary: #1a56db;
    --primary-dark: #0e3a8a;
    --success: #10b981;
    --success-dark: #059669;
    --warning: #f59e0b;
    --warning-dark: #d97706;
    --danger: #ef4444;
    --danger-dark: #dc2626;
    --info: #3b82f6;
    --dark: #111827;
    --gray: #6b7280;
    --gray-light: #f3f4f6;
    --white: #ffffff;
    --border: #e2e8f0;
}

.users-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}

/* ==========================================
   STATISTIQUES (CARTES CARRÉES)
   ========================================== */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--white);
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon i {
    font-size: 1.5rem;
    color: white;
}

.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
.bg-success { background: linear-gradient(135deg, var(--success), var(--success-dark)); }
.bg-warning { background: linear-gradient(135deg, var(--warning), var(--warning-dark)); }
.bg-danger { background: linear-gradient(135deg, var(--danger), var(--danger-dark)); }

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.688rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    font-weight: 600;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--dark);
    display: block;
    line-height: 1.2;
}

/* ==========================================
   BARRE D'ACTIONS
   ========================================== */
.actions-bar {
    background: var(--white);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.actions-left {
    display: flex;
    gap: 0.75rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    padding: 0.6rem 1.5rem;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,86,219,0.3);
}

.search-form {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 0.875rem;
}

.search-box input {
    padding: 0.6rem 0.75rem 0.6rem 2rem;
    border: 1px solid var(--border);
    font-size: 0.813rem;
    width: 240px;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

.filter-select {
    padding: 0.6rem 0.75rem;
    border: 1px solid var(--border);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--white);
    cursor: pointer;
}

.btn-filter {
    background: var(--primary);
    border: none;
    padding: 0.6rem 1.5rem;
    color: var(--white);
    font-weight: 700;
    font-size: 0.75rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-filter:hover {
    background: var(--primary-dark);
}

.btn-reset {
    background: var(--gray-light);
    padding: 0.6rem 1.25rem;
    color: var(--gray);
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.2s;
}

.btn-reset:hover {
    background: #e5e7eb;
    color: var(--dark);
}

/* ==========================================
   TABLEAU
   ========================================== */
.data-card {
    background: var(--white);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 0.688rem;
    font-weight: 700;
    color: var(--gray);
    text-align: left;
    border-bottom: 1px solid var(--border);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
    font-size: 0.813rem;
}

.data-table tr:hover {
    background: #f8fafc;
}

/* User cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    color: var(--white);
}

.user-name {
    font-weight: 700;
    color: var(--dark);
    font-size: 0.875rem;
}

.user-id {
    font-size: 0.688rem;
    color: var(--gray);
}

.user-email i {
    color: var(--gray);
    margin-right: 0.5rem;
}

/* Role badges */
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 1rem;
    font-size: 0.688rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.role-admin {
    background: #fee2e2;
    color: #dc2626;
}

.role-cp {
    background: #d1fae5;
    color: #059669;
}

.role-cc {
    background: #dbeafe;
    color: #1d4ed8;
}

.date-cell i {
    color: var(--gray);
    margin-right: 0.5rem;
}

/* Action buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: var(--gray);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.action-btn.view:hover {
    background: var(--primary);
    color: white;
}

.action-btn.edit:hover {
    background: var(--warning);
    color: white;
}

.action-btn.reset:hover {
    background: var(--info);
    color: white;
}

.action-btn.delete:hover {
    background: var(--danger);
    color: white;
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
    margin-bottom: 1rem;
    font-weight: 600;
    letter-spacing: 1px;
}

.btn-primary-sm {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
    padding: 0.6rem 1.5rem;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-primary-sm:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
}

/* Pagination */
.pagination-wrapper {
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-form {
        flex-direction: column;
        width: 100%;
    }
    
    .search-box input {
        width: 100%;
    }
    
    .stats-row {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .data-table th, .data-table td {
        padding: 0.75rem;
    }
}

@media (max-width: 480px) {
    .user-cell {
        flex-direction: column;
        text-align: center;
    }
    
    .user-avatar {
        margin-bottom: 0.25rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete(userId) {
        if (confirm('❌ ÊTES-VOUS SÛR DE VOULOIR SUPPRIMER CET UTILISATEUR ?\nCette action est irréversible.')) {
            const form = document.getElementById('delete-form');
            form.action = '/admin/users/' + userId;
            form.submit();
        }
    }
    
    function resetPassword(userId) {
        if (confirm('🔄 RÉINITIALISER LE MOT DE PASSE ?\nL\'utilisateur recevra un nouveau mot de passe temporaire.')) {
            const form = document.getElementById('reset-password-form');
            form.action = '/admin/users/' + userId + '/reset-password';
            form.submit();
        }
    }
</script>
@endpush
@endsection