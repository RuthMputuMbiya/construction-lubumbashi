{{-- resources/views/admin/projets/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestion des projets - Admin')

@section('page-title', 'Tous les projets')
@section('page-subtitle', 'Gérez et suivez tous les projets de construction')

@section('content')
<div class="projets-container">
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES GLOBALES --}}
    {{-- ========================================== --}}
    <div class="stats-header">
        <div class="stats-cards">
            <div class="stat-total">
                <span class="stat-number">{{ $stats['total'] }}</span>
                <span class="stat-label">PROJETS TOTAL</span>
            </div>
            <div class="stat-cours">
                <span class="stat-number">{{ $stats['en_cours'] }}</span>
                <span class="stat-label">EN COURS</span>
            </div>
            <div class="stat-attente">
                <span class="stat-number">{{ $stats['en_attente'] }}</span>
                <span class="stat-label">EN ATTENTE</span>
            </div>
            <div class="stat-termine">
                <span class="stat-number">{{ $stats['termine'] }}</span>
                <span class="stat-label">TERMINÉS</span>
            </div>
            <div class="stat-annule">
                <span class="stat-number">{{ $stats['annule'] }}</span>
                <span class="stat-label">ANNULÉS</span>
            </div>
            <div class="stat-avancement">
                <span class="stat-number">{{ $stats['avancement_moyen'] }}<small>%</small></span>
                <span class="stat-label">AVANCEMENT MOYEN</span>
            </div>
        </div>
      
    </div>
    
    {{-- ========================================== --}}
    {{-- BARRE DE FILTRES --}}
    {{-- ========================================== --}}
    <div class="filters-bar">
        <form method="GET" class="filters-form">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Rechercher un projet..." value="{{ request('search') }}">
            </div>
            <div class="filter-wrapper">
                <i class="fas fa-filter"></i>
                <select name="statut">
                    <option value="all" {{ request('statut') == 'all' || !request('statut') ? 'selected' : '' }}>📊 TOUS STATUTS</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>⏳ EN ATTENTE</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>▶️ EN COURS</option>
                    <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>✅ TERMINÉS</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>❌ ANNULÉS</option>
                </select>
            </div>
            <div class="filter-wrapper">
                <i class="fas fa-user"></i>
                <select name="chef_projet">
                    <option value="all" {{ request('chef_projet') == 'all' || !request('chef_projet') ? 'selected' : '' }}>👨‍💼 TOUS CHEFS PROJET</option>
                    @foreach($chefsProjet as $chef)
                        <option value="{{ $chef->id }}" {{ request('chef_projet') == $chef->id ? 'selected' : '' }}>{{ $chef->nom }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">FILTRER</button>
            <a href="{{ route('admin.projets.index') }}" class="btn-reset">RÉINITIALISER</a>
        </form>
    </div>
    
    {{-- ========================================== --}}
    {{-- TABLEAU DES PROJETS --}}
    {{-- ========================================== --}}
    <div class="projets-table-container">
        <table class="projets-table">
            <thead>
                <tr>
                    <th>CODE</th>
                    <th>PROJET</th>
                    <th>LIEU</th>
                    <th>CHEF DE PROJET</th>
                    <th>STATUT</th>
                    <th>AVANCEMENT</th>
                    <th>BUDGET</th>
                    <th>DÉBUT</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projets as $projet)
                <tr>
                    <td class="code">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id,4,'0',STR_PAD_LEFT) }}</td>
                    <td class="nom"><strong>{{ $projet->nom }}</strong></td>
                    <td><i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}</td>
                    <td>{{ $projet->chefProjet->nom ?? 'Non assigné' }}</td>
                    <td>
                        @if($projet->statut == 'en_cours')
                            <span class="status-badge status-cours"><i class="fas fa-play-circle"></i> EN COURS</span>
                        @elseif($projet->statut == 'termine')
                            <span class="status-badge status-termine"><i class="fas fa-check-circle"></i> TERMINÉ</span>
                        @elseif($projet->statut == 'annule')
                            <span class="status-badge status-annule"><i class="fas fa-ban"></i> ANNULÉ</span>
                        @else
                            <span class="status-badge status-attente"><i class="fas fa-clock"></i> EN ATTENTE</span>
                        @endif
                    </td>
                    <td>
                        <div class="progress-cell">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $projet->avancement }}%"></div>
                            </div>
                            <span class="progress-value">{{ $projet->avancement }}%</span>
                        </div>
                    </td>
                    <td class="budget">{{ number_format($projet->budget_total, 0, ',', ' ') }} FC</td>
                    <td class="date">{{ \Carbon\Carbon::parse($projet->date_debut_prevue)->format('d/m/Y') }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.projets.show', $projet->id) }}" class="action-btn view" title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.projets.edit', $projet->id) }}" class="action-btn edit" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($projet->statut != 'termine' && $projet->statut != 'annule')
                        <button type="button" class="action-btn cancel" onclick="showCancelModal({{ $projet->id }})" title="Annuler">
                            <i class="fas fa-ban"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="empty-row">
                        <i class="fas fa-folder-open"></i>
                        <p>Aucun projet trouvé</p>
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

{{-- Modal d'annulation --}}
<div id="cancelModal" class="modal-cancel" style="display: none;">
    <div class="modal-cancel-content">
        <div class="modal-cancel-header">
            <h4><i class="fas fa-ban"></i> ANNULER LE PROJET</h4>
            <span class="close" onclick="closeCancelModal()">&times;</span>
        </div>
        <form id="cancelForm" method="POST">
            @csrf
            <div class="modal-cancel-body">
                <p>Êtes-vous sûr de vouloir annuler ce projet ?</p>
                <div class="form-group">
                    <label>MOTIF DE L'ANNULATION</label>
                    <textarea name="motif_annulation" class="form-control" rows="3" required placeholder="Veuillez expliquer la raison..."></textarea>
                </div>
                <div class="alert-danger-modal">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Cette action est irréversible.</span>
                </div>
            </div>
            <div class="modal-cancel-footer">
                <button type="button" class="btn-cancel" onclick="closeCancelModal()">RETOUR</button>
                <button type="submit" class="btn-confirm">CONFIRMER L'ANNULATION</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   ADMIN PROJETS - DESIGN PROFESSIONNEL
   ============================================ */

.projets-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* ==========================================
   STATISTIQUES
   ========================================== */
.stats-header {
    margin-bottom: 1.5rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

.stats-cards > div {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 1rem;
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.2;
}

.stat-number small {
    font-size: 0.7rem;
    font-weight: 500;
}

.stat-label {
    font-size: 0.6rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-total .stat-number { color: #1a56db; }
.stat-cours .stat-number { color: #10b981; }
.stat-attente .stat-number { color: #f59e0b; }
.stat-termine .stat-number { color: #059669; }
.stat-annule .stat-number { color: #dc2626; }
.stat-avancement .stat-number { color: #8b5cf6; }

.header-actions {
    text-align: right;
}

.btn-nouveau {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: white;
    padding: 0.6rem 1.25rem;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* ==========================================
   FILTRES
   ========================================== */
.filters-bar {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.filters-form {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.search-wrapper {
    position: relative;
    flex: 2;
    min-width: 200px;
}

.search-wrapper i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-wrapper input {
    width: 100%;
    padding: 0.6rem 0.75rem 0.6rem 2rem;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
}

.filter-wrapper {
    position: relative;
    min-width: 180px;
}

.filter-wrapper i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.filter-wrapper select {
    width: 100%;
    padding: 0.6rem 0.75rem 0.6rem 2rem;
    border: 1px solid #e2e8f0;
    font-size: 0.75rem;
    background: white;
    cursor: pointer;
}

.btn-filter, .btn-reset {
    padding: 0.6rem 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
}

.btn-filter {
    background: #1a56db;
    color: white;
}

.btn-reset {
    background: #f1f5f9;
    color: #64748b;
    text-decoration: none;
}

/* ==========================================
   TABLEAU
   ========================================== */
.projets-table-container {
    background: white;
    border: 1px solid #e2e8f0;
    overflow-x: auto;
}

.projets-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.projets-table th {
    padding: 1rem;
    background: #f8fafc;
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.projets-table td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.8rem;
    vertical-align: middle;
}

.code {
    font-family: monospace;
    font-size: 0.7rem;
    color: #64748b;
}

.nom {
    font-weight: 600;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.7rem;
    font-size: 0.65rem;
    font-weight: 700;
}

.status-cours { background: #dbeafe; color: #1d4ed8; }
.status-termine { background: #d1fae5; color: #059669; }
.status-annule { background: #fee2e2; color: #dc2626; }
.status-attente { background: #fef3c7; color: #d97706; }

.progress-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 100px;
}

.progress-bar {
    flex: 1;
    background: #e2e8f0;
    height: 6px;
}

.progress-fill {
    background: linear-gradient(90deg, #10b981, #1a56db);
    height: 100%;
}

.progress-value {
    font-size: 0.7rem;
    font-weight: 700;
    color: #1a56db;
    min-width: 40px;
}

.budget {
    font-weight: 600;
}

.date {
    font-size: 0.7rem;
    color: #64748b;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border-radius: 6px;
}

.action-btn.view { background: #e8f5e9; color: #2e7d32; }
.action-btn.edit { background: #fff3e0; color: #ed6c02; }
.action-btn.cancel { background: #fee2e2; color: #dc2626; border: none; cursor: pointer; }

/* Modal */
.modal-cancel {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-cancel-content {
    background: white;
    width: 90%;
    max-width: 500px;
}

.modal-cancel-header {
    padding: 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-cancel-header h4 {
    margin: 0;
    font-size: 0.9rem;
}

.close {
    font-size: 1.5rem;
    cursor: pointer;
}

.modal-cancel-body {
    padding: 1.25rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
}

.form-control {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
}

.alert-danger-modal {
    background: #fee2e2;
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.7rem;
    color: #dc2626;
}

.modal-cancel-footer {
    padding: 1rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.btn-cancel, .btn-confirm {
    padding: 0.5rem 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
}

.btn-cancel { background: #f1f5f9; color: #64748b; }
.btn-confirm { background: #dc2626; color: white; }

.empty-row {
    text-align: center;
    padding: 3rem !important;
}

.empty-row i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
    display: block;
}

/* Responsive */
@media (max-width: 992px) {
    .stats-cards {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filters-form {
        flex-direction: column;
    }
    
    .search-wrapper, .filter-wrapper {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
    function showCancelModal(projetId) {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = '/admin/projets/' + projetId + '/cancel';
        modal.style.display = 'flex';
    }
    
    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.style.display = 'none';
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target == modal) {
            closeCancelModal();
        }
    }
</script>
@endpush
@endsection