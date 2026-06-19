{{-- resources/views/admin/projets/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des projets - ConstrucTrack')

@section('page-title', 'Tous les projets')

@section('content')
<div class="projects-page">
    
    {{-- ============================================ --}}
    {{-- STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total projets</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['en_cours'] }}</h3>
                <p>En cours</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['termine'] }}</h3>
                <p>Terminés</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($stats['budget_total'] / 1000000, 1) }}M</h3>
                <p>Budget total (FC)</p>
            </div>
        </div>
    </div>
    
    {{-- ============================================ --}}
    {{-- FILTRES --}}
    {{-- ============================================ --}}
    <div class="filters-bar">
        <div class="filter-group">
            <span>Filtrer par statut :</span>
            <div class="filter-btns">
                <a href="?statut=all" class="filter-btn {{ request('statut') == 'all' || !request('statut') ? 'active' : '' }}">Tous</a>
                <a href="?statut=en_attente" class="filter-btn {{ request('statut') == 'en_attente' ? 'active' : '' }}">En attente</a>
                <a href="?statut=en_cours" class="filter-btn {{ request('statut') == 'en_cours' ? 'active' : '' }}">En cours</a>
                <a href="?statut=termine" class="filter-btn {{ request('statut') == 'termine' ? 'active' : '' }}">Terminés</a>
                <a href="?statut=annule" class="filter-btn {{ request('statut') == 'annule' ? 'active' : '' }}">Annulés</a>
            </div>
        </div>
        
        <form method="GET" class="search-form">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Rechercher un projet..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-search">Rechercher</button>
        </form>
    </div>
    
    {{-- ============================================ --}}
    {{-- TABLEAU DES PROJETS --}}
    {{-- ============================================ --}}
    <div class="table-container">
        <table class="projects-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Projet</th>
                    <th>Lieu</th>
                    <th>Chef de projet</th>
                    <th>Statut</th>
                    <th>Avancement</th>
                    <th>Budget</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projets as $projet)
                <tr>
                    <td data-label="Code">
                        <span class="code-badge">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td data-label="Projet">
                        <strong>{{ $projet->nom }}</strong>
                    </td>
                    <td data-label="Lieu">
                        <i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}
                    </td>
                    <td data-label="Chef de projet">
                        {{ $projet->chefProjet->nom ?? 'N/A' }}
                    </td>
                    <td data-label="Statut">
                        @if($projet->statut == 'en_cours')
                            <span class="status-badge primary"><i class="fas fa-play"></i> En cours</span>
                        @elseif($projet->statut == 'termine')
                            <span class="status-badge success"><i class="fas fa-check"></i> Terminé</span>
                        @elseif($projet->statut == 'annule')
                            <span class="status-badge danger"><i class="fas fa-ban"></i> Annulé</span>
                        @else
                            <span class="status-badge warning"><i class="fas fa-clock"></i> En attente</span>
                        @endif
                    </td>
                    <td data-label="Avancement">
                        <div class="progress-cell">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $projet->avancement }}%"></div>
                            </div>
                            <span class="progress-value">{{ $projet->avancement }}%</span>
                        </div>
                    </td>
                    <td data-label="Budget">
                        <div class="budget-cell">
                            <span class="budget-total">{{ number_format($projet->budget_total, 0, ',', ' ') }} FC</span>
                            <small class="budget-used">Consommé: {{ $projet->budget_total > 0 ? round(($projet->budget_depense / $projet->budget_total) * 100) : 0 }}%</small>
                        </div>
                    </td>
                    <td data-label="Date">
                        {{ $projet->created_at->format('d/m/Y') }}
                    </td>
                    <td data-label="Actions">
                        <div class="actions">
                            <a href="{{ route('admin.projets.show', $projet->id) }}" class="action-btn view" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="empty-row">
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>Aucun projet trouvé</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($projets->hasPages())
    <div class="pagination-wrapper">
        {{ $projets->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
    .projects-page {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* Statistiques */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
    }
    
    .stat-icon.bg-primary { background: linear-gradient(135deg, #1a56db, #0e3a8a); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    
    .stat-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }
    
    .stat-info p {
        font-size: 0.7rem;
        color: #6b7280;
        margin: 0;
    }
    
    /* Filtres */
    .filters-bar {
        background: white;
        border-radius: 16px;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #edf2f7;
    }
    
    .filter-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .filter-group span {
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .filter-btns {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        text-decoration: none;
        background: #f3f4f6;
        color: #4b5563;
        transition: all 0.2s;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background: #1a56db;
        color: white;
    }
    
    .search-form {
        display: flex;
        gap: 0.5rem;
    }
    
    .search-wrapper {
        position: relative;
    }
    
    .search-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 0.8rem;
    }
    
    .search-wrapper input {
        padding: 0.5rem 0.75rem 0.5rem 2rem;
        border: 1px solid #e5e7eb;
        border-radius: 30px;
        font-size: 0.8rem;
        width: 220px;
    }
    
    .btn-search {
        background: #f3f4f6;
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 30px;
        cursor: pointer;
    }
    
    /* Tableau */
    .table-container {
        background: white;
        border-radius: 20px;
        overflow-x: auto;
        border: 1px solid #edf2f7;
    }
    
    .projects-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }
    
    .projects-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
    }
    
    .projects-table thead th {
        padding: 1rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        text-align: left;
    }
    
    .projects-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }
    
    .projects-table tbody tr:hover {
        background: #fafbfc;
    }
    
    .projects-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .code-badge {
        background: #f3f4f6;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-family: monospace;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    .status-badge.primary { background: #dbeafe; color: #1d4ed8; }
    .status-badge.success { background: #d1fae5; color: #059669; }
    .status-badge.danger { background: #fee2e2; color: #dc2626; }
    .status-badge.warning { background: #fef3c7; color: #d97706; }
    
    .progress-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 100px;
    }
    
    .progress-bar {
        flex: 1;
        background: #e2e8f0;
        border-radius: 10px;
        height: 6px;
        overflow: hidden;
    }
    
    .progress-fill {
        background: linear-gradient(90deg, #10b981, #1a56db);
        height: 100%;
        border-radius: 10px;
    }
    
    .progress-value {
        font-size: 0.7rem;
        font-weight: 600;
        color: #1a56db;
        min-width: 35px;
    }
    
    .budget-cell {
        display: flex;
        flex-direction: column;
    }
    
    .budget-total {
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .budget-used {
        font-size: 0.65rem;
        color: #6b7280;
    }
    
    .actions {
        display: flex;
        gap: 0.3rem;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .action-btn.view {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .action-btn:hover {
        transform: scale(1.08);
    }
    
    .empty-row td {
        padding: 3rem !important;
    }
    
    .empty-state {
        text-align: center;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
    }
    
    .pagination-wrapper {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }
</style>
@endpush