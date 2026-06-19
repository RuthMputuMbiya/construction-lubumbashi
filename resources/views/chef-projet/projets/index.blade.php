@extends('layouts.chef-projet')

@section('title', 'Mes projets - Chef de Projet')

@section('page-title', 'Mes projets')
@section('page-subtitle', 'Liste de tous vos projets de construction')

@section('content')
<div class="projets-container">
    
    {{-- STATISTIQUES AUTOMATIQUES --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TOTAL PROJETS</span>
                <span class="stat-value">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="stat-glow primary-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">EN COURS</span>
                <span class="stat-value">{{ $stats['en_cours'] ?? 0 }}</span>
            </div>
            <div class="stat-glow success-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">EN ATTENTE</span>
                <span class="stat-value">{{ $stats['en_attente'] ?? 0 }}</span>
            </div>
            <div class="stat-glow warning-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-secondary">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TERMINÉS</span>
                <span class="stat-value">{{ $stats['termines'] ?? 0 }}</span>
            </div>
            <div class="stat-glow secondary-glow"></div>
        </div>
    </div>
    
    {{-- BARRE D'ACTIONS --}}
    <div class="actions-bar">
        <div class="actions-left">
            <a href="{{ route('chef-projet.planification.create') }}" class="btn-create">
                <i class="fas fa-plus-circle"></i> NOUVEAU PROJET
            </a>
        </div>
        <div class="actions-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un projet..." class="search-input">
            </div>
            <div class="filter-box">
                <i class="fas fa-filter"></i>
                <select id="statusFilter" class="filter-select">
                    <option value="all">📋 Tous les statuts</option>
                    <option value="en_cours">▶️ En cours</option>
                    <option value="en_attente">⏳ En attente</option>
                    <option value="termine">✅ Terminé</option>
                    <option value="annule">❌ Annulé</option>
                </select>
            </div>
        </div>
    </div>
    
    {{-- LISTE DES PROJETS --}}
    <div class="projets-grid" id="projectsContainer">
        @forelse($projets as $projet)
            @php
                $etapesCount = $projet->etapes_count ?? App\Models\EtapeProjet::where('id_projet', $projet->id)->count();
                $avancement = $projet->avancement ?? 0;
                
                // Récupérer le chef de chantier assigné
                $chefAssigne = App\Models\EtapeProjet::where('id_projet', $projet->id)
                    ->whereNotNull('id_chef_chantier')
                    ->with('chefChantier')
                    ->first();
                $nomChef = $chefAssigne && $chefAssigne->chefChantier ? $chefAssigne->chefChantier->nom : 'Non assigné';
                
                // Déterminer le statut
                if($projet->statut == 'termine') {
                    $badgeClass = 'success';
                    $badgeIcon = 'fa-check-circle';
                    $badgeText = 'TERMINÉ';
                } elseif($projet->statut == 'en_cours') {
                    $badgeClass = 'primary';
                    $badgeIcon = 'fa-play-circle';
                    $badgeText = 'EN COURS';
                } elseif($projet->statut == 'annule') {
                    $badgeClass = 'danger';
                    $badgeIcon = 'fa-ban';
                    $badgeText = 'ANNULÉ';
                } elseif($projet->statut == 'planification') {
                    $badgeClass = 'info';
                    $badgeIcon = 'fa-clipboard-list';
                    $badgeText = 'PLANIFICATION';
                } else {
                    $badgeClass = 'warning';
                    $badgeIcon = 'fa-clock';
                    $badgeText = 'EN ATTENTE';
                }
            @endphp
            <div class="projet-card" data-statut="{{ $projet->statut }}" data-nom="{{ strtolower($projet->nom) }}">
                <div class="projet-card-body">
                    <div class="projet-card-header">
                        <span class="badge {{ $badgeClass }}">
                            <i class="fas {{ $badgeIcon }}"></i> {{ $badgeText }}
                        </span>
                        <span class="projet-code">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    
                    <h5 class="projet-nom">{{ $projet->nom }}</h5>
                    <p class="projet-lieu"><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}</p>
                    <p class="projet-desc">{{ Str::limit($projet->description ?? 'Aucune description', 70) }}</p>
                    
                    <div class="projet-dates">
                        <span><i class="fas fa-calendar-alt"></i> {{ isset($projet->date_debut_prevue) ? Carbon\Carbon::parse($projet->date_debut_prevue)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                        <span><i class="fas fa-arrow-right"></i></span>
                        <span><i class="fas fa-flag-checkered"></i> {{ isset($projet->date_fin_prevue) ? Carbon\Carbon::parse($projet->date_fin_prevue)->locale('fr')->isoFormat('D MMM YYYY') : 'N/A' }}</span>
                    </div>
                    
                    <div class="projet-budget">
                        <span class="budget-label">Budget</span>
                        <span class="budget-value">{{ number_format($projet->budget_previsionnel ?? $projet->budget_total ?? 0, 0, ',', ' ') }} {{ $projet->devise ?? 'FC' }}</span>
                    </div>
                    
                    <div class="projet-chef">
                        <i class="fas fa-hard-hat"></i>
                        <span>{{ $nomChef }}</span>
                    </div>
                    
                    <div class="projet-progress">
                        <div class="progress-label">
                            <span>Avancement</span>
                            <span class="progress-value">{{ $avancement }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $avancement }}%"></div>
                        </div>
                    </div>
                    
                    <div class="projet-etapes">
                        <i class="fas fa-tasks"></i> {{ $etapesCount }} étapes
                    </div>
                </div>
                <div class="projet-card-footer">
                    <a href="{{ route('chef-projet.projets.show', $projet->id) }}" class="btn-detail">
                        <i class="fas fa-eye"></i> VOIR DÉTAILS
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>AUCUN PROJET</h3>
                <p>Commencez par créer une nouvelle planification de projet</p>
                <a href="{{ route('chef-projet.planification.create') }}" class="btn-create-empty">
                    <i class="fas fa-plus-circle"></i> CRÉER UN PROJET
                </a>
            </div>
        @endforelse
    </div>
    
    {{-- PAGINATION AVEC STATISTIQUES --}}
    @if($projets->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            <span class="pagination-text">
                Affichage de {{ $projets->firstItem() ?? 0 }} à {{ $projets->lastItem() ?? 0 }} 
                sur {{ $projets->total() }} projet(s)
            </span>
        </div>
        <div class="pagination-links">
            {{ $projets->links() }}
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.projets-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 10px;
}

/* ==========================================
   STATISTIQUES
   ========================================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: #fff;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.2rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stat-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.primary-glow { background: linear-gradient(90deg, #1a56db, #0e3a8a); }
.success-glow { background: linear-gradient(90deg, #10b981, #059669); }
.warning-glow { background: linear-gradient(90deg, #f59e0b, #d97706); }
.secondary-glow { background: linear-gradient(90deg, #64748b, #475569); }

.stat-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 8px;
}

.stat-icon i {
    font-size: 1.3rem;
    color: #fff;
}

.bg-primary { background: linear-gradient(135deg, #1a56db, #0e3a8a); }
.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.bg-secondary { background: linear-gradient(135deg, #64748b, #475569); }

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.6rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    font-weight: 600;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e293b;
    display: block;
    line-height: 1.2;
}

/* ==========================================
   BARRE D'ACTIONS
   ========================================== */
.actions-bar {
    background: #fff;
    padding: 0.8rem 1.2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.actions-left {
    display: flex;
    gap: 0.75rem;
}

.btn-create {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: #fff;
    padding: 0.5rem 1.2rem;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.btn-create:hover {
    background: linear-gradient(135deg, #0e3a8a, #1a56db);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(26,86,219,0.3);
    color: #fff;
}

.actions-right {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.8rem;
}

.search-input {
    padding: 0.5rem 0.8rem 0.5rem 2.2rem;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
    width: 220px;
    transition: all 0.2s;
    border-radius: 4px;
}

.search-input:focus {
    outline: none;
    border-color: #1a56db;
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

.filter-box {
    position: relative;
}

.filter-box i {
    position: absolute;
    left: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.8rem;
}

.filter-select {
    padding: 0.5rem 0.8rem 0.5rem 2.2rem;
    border: 1px solid #e2e8f0;
    font-size: 0.75rem;
    font-weight: 600;
    background: #fff;
    cursor: pointer;
    appearance: none;
    border-radius: 4px;
}

/* ==========================================
   GRILLE DES PROJETS
   ========================================== */
.projets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.2rem;
}

.projet-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    border-radius: 8px;
}

.projet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border-color: #1a56db;
}

.projet-card-body {
    padding: 1.2rem 1.2rem 0.8rem;
    flex: 1;
}

.projet-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.6rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.7rem;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    border-radius: 4px;
}

.badge.primary { background: #dbeafe; color: #1d4ed8; }
.badge.success { background: #d1fae5; color: #059669; }
.badge.warning { background: #fef3c7; color: #d97706; }
.badge.danger { background: #fee2e2; color: #dc2626; }
.badge.info { background: #e0f2fe; color: #0284c7; }

.projet-code {
    font-size: 0.6rem;
    color: #94a3b8;
    font-family: monospace;
}

.projet-nom {
    font-size: 1rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.2rem 0;
}

.projet-lieu {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0 0 0.4rem 0;
}
.projet-lieu i {
    margin-right: 0.3rem;
}

.projet-desc {
    font-size: 0.7rem;
    color: #64748b;
    margin: 0 0 0.6rem 0;
    line-height: 1.4;
}

.projet-dates {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.6rem;
    color: #64748b;
    margin-bottom: 0.6rem;
}
.projet-dates i {
    margin-right: 0.2rem;
}

.projet-budget {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    padding: 0.3rem 0;
    border-top: 1px solid #f1f5f9;
    margin-bottom: 0.4rem;
}
.budget-label {
    color: #64748b;
}
.budget-value {
    font-weight: 700;
    color: #10b981;
}

.projet-chef {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.7rem;
    color: #1a56db;
    margin-bottom: 0.6rem;
}
.projet-chef i {
    color: #1a56db;
}

.projet-progress {
    margin-bottom: 0.6rem;
}
.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.6rem;
    margin-bottom: 0.2rem;
}
.progress-value {
    font-weight: 700;
    color: #1a56db;
}
.progress-bar {
    height: 5px;
    background: #e2e8f0;
    overflow: hidden;
    border-radius: 4px;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #1a56db);
    border-radius: 4px;
}

.projet-etapes {
    font-size: 0.65rem;
    color: #64748b;
}
.projet-etapes i {
    margin-right: 0.3rem;
}

.projet-card-footer {
    padding: 0.6rem 1.2rem 1rem;
    border-top: 1px solid #f1f5f9;
}

.btn-detail {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.4rem;
    background: #f8fafc;
    color: #1a56db;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
}

.btn-detail:hover {
    background: #1a56db;
    color: #fff;
    border-color: #1a56db;
}

/* ==========================================
   EMPTY STATE
   ========================================== */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    grid-column: 1 / -1;
    border-radius: 8px;
}

.empty-state i {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 0.8rem;
}

.empty-state h3 {
    font-size: 0.9rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 0.4rem;
}

.empty-state p {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 1rem;
}

.btn-create-empty {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: #fff;
    padding: 0.5rem 1.5rem;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
    border-radius: 4px;
}

.btn-create-empty:hover {
    background: linear-gradient(135deg, #0e3a8a, #1a56db);
    transform: translateY(-2px);
    color: #fff;
}

/* ==========================================
   PAGINATION AVEC STATISTIQUES
   ========================================== */
.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 0.8rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.pagination-info {
    display: flex;
    align-items: center;
}

.pagination-text {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

.pagination-links {
    display: flex;
    align-items: center;
}

.pagination-links nav {
    display: flex;
    gap: 0.3rem;
}

.pagination-links .page-item {
    list-style: none;
}

.pagination-links .page-link {
    padding: 0.4rem 0.8rem;
    border: 1px solid #e2e8f0;
    color: #1a56db;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.8rem;
    transition: all 0.2s;
}

.pagination-links .page-link:hover {
    background: #1a56db;
    color: #fff;
    border-color: #1a56db;
}

.pagination-links .active .page-link {
    background: #1a56db;
    color: #fff;
    border-color: #1a56db;
}

.pagination-links .disabled .page-link {
    color: #94a3b8;
    cursor: not-allowed;
    opacity: 0.6;
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .projets-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    .pagination-wrapper {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .actions-right {
        flex-direction: column;
    }
    .search-input {
        width: 100%;
    }
    .filter-select {
        width: 100%;
    }
    .projets-grid {
        grid-template-columns: 1fr;
    }
    .pagination-links nav {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('statusFilter').addEventListener('change', function() {
    const valeur = this.value;
    const cards = document.querySelectorAll('.projet-card');
    cards.forEach(card => {
        const statut = card.getAttribute('data-statut');
        if (valeur === 'all' || statut === valeur) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

document.getElementById('searchInput').addEventListener('keyup', function() {
    const terme = this.value.toLowerCase();
    const cards = document.querySelectorAll('.projet-card');
    cards.forEach(card => {
        const nom = card.getAttribute('data-nom');
        if (nom.includes(terme)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Animation des cartes au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.projet-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * index);
    });
});
</script>
@endpush
@endsection