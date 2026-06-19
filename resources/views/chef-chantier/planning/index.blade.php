@extends('layouts.chef-chantier')

@section('title', 'Mon planning - Chef de Chantier')

@section('page-title', 'Mon planning')
@section('page-subtitle', "Vue d'ensemble de vos missions")

@section('content')
<div class="container-fluid px-0">
    
    {{-- ONGLETS DE NAVIGATION --}}
    <div class="planning-tabs mb-4">
        <div class="d-flex gap-2">
            <a href="{{ route('chef-chantier.planning.index') }}" class="tab-btn active">
                <i class="fas fa-list me-2"></i> VUE LISTE
            </a>
            <a href="{{ route('chef-chantier.planning.calendar') }}" class="tab-btn">
                <i class="fas fa-calendar-alt me-2"></i> VUE CALENDRIER
            </a>
        </div>
    </div>
    
    {{-- FILTRES PAR STATUT --}}
    <div class="status-filters mb-4">
        <div class="status-filters-title">
            <i class="fas fa-filter me-2"></i> FILTRER PAR STATUT :
        </div>
        <div class="status-pills">
            <a href="{{ route('chef-chantier.planning.index', array_merge(request()->except('statut'), ['statut' => 'all'])) }}" 
               class="status-pill {{ !request('statut') || request('statut') == 'all' ? 'active' : '' }}">
                <i class="fas fa-list-ul me-1"></i> Tous
                <span class="pill-count">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('chef-chantier.planning.index', array_merge(request()->except('statut'), ['statut' => 'a_venir'])) }}" 
               class="status-pill secondary {{ request('statut') == 'a_venir' ? 'active' : '' }}">
                <i class="fas fa-clock me-1"></i> Non commencées
                <span class="pill-count">{{ $stats['non_commence'] }}</span>
            </a>
            <a href="{{ route('chef-chantier.planning.index', array_merge(request()->except('statut'), ['statut' => 'en_cours'])) }}" 
               class="status-pill primary {{ request('statut') == 'en_cours' ? 'active' : '' }}">
                <i class="fas fa-play-circle me-1"></i> En cours
                <span class="pill-count">{{ $stats['en_cours'] }}</span>
            </a>
            <a href="{{ route('chef-chantier.planning.index', array_merge(request()->except('statut'), ['statut' => 'termine'])) }}" 
               class="status-pill success {{ request('statut') == 'termine' ? 'active' : '' }}">
                <i class="fas fa-check-circle me-1"></i> Terminées
                <span class="pill-count">{{ $stats['terminees'] }}</span>
            </a>
            <a href="{{ route('chef-chantier.planning.index', array_merge(request()->except('statut'), ['statut' => 'en_retard'])) }}" 
               class="status-pill danger {{ request('statut') == 'en_retard' ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle me-1"></i> En retard
                <span class="pill-count">{{ $stats['en_retard'] }}</span>
            </a>
        </div>
    </div>
    
    {{-- FILTRES PÉRIODE --}}
    <div class="filters-bar mb-4">
        <form method="GET" class="d-flex flex-wrap gap-3 align-items-center">
            @if(request('statut') && request('statut') != 'all')
                <input type="hidden" name="statut" value="{{ request('statut') }}">
            @endif
            <div class="filter-group">
                <label class="filter-label"><i class="fas fa-clock me-1"></i> PÉRIODE :</label>
                <select name="periode" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('periode') == 'all' || !request('periode') ? 'selected' : '' }}>📅 Toutes</option>
                    <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>📆 Cette semaine</option>
                    <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>📅 Ce mois</option>
                    <option value="trimestre" {{ request('periode') == 'trimestre' ? 'selected' : '' }}>📊 Ce trimestre</option>
                </select>
            </div>
            <div class="ms-auto">
                <button type="submit" class="btn-filter-apply"><i class="fas fa-check me-1"></i> APPLIQUER</button>
                <a href="{{ route('chef-chantier.planning.index') }}" class="btn-filter-reset"><i class="fas fa-undo me-1"></i> RÉINITIALISER</a>
            </div>
        </form>
    </div>
    
    {{-- STATISTIQUES RAPIDES --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon-sm bg-primary-soft">
                    <i class="fas fa-tasks text-primary"></i>
                </div>
                <div>
                    <h4 class="stat-number">{{ $stats['total'] }}</h4>
                    <p class="stat-text">Étapes totales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon-sm bg-success-soft">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div>
                    <h4 class="stat-number text-success">{{ $stats['terminees'] }}</h4>
                    <p class="stat-text">Terminées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon-sm bg-warning-soft">
                    <i class="fas fa-play-circle text-warning"></i>
                </div>
                <div>
                    <h4 class="stat-number text-warning">{{ $stats['en_cours'] }}</h4>
                    <p class="stat-text">En cours</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon-sm bg-danger-soft">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                <div>
                    <h4 class="stat-number text-danger">{{ $stats['en_retard'] }}</h4>
                    <p class="stat-text">En retard</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- PLANNING PAR SEMAINE --}}
    @forelse($planningParSemaine as $semaine)
    <div class="week-card mb-4">
        <div class="week-header">
            <div class="d-flex align-items-center gap-3">
                <div class="week-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div>
                    <h5 class="week-title">
                        Semaine du {{ $semaine['date_debut']->locale('fr')->isoFormat('D MMMM YYYY') }} 
                        au {{ $semaine['date_fin']->locale('fr')->isoFormat('D MMMM YYYY') }}
                    </h5>
                    <p class="week-count">{{ count($semaine['etapes']) }} étape(s) planifiée(s)</p>
                </div>
            </div>
        </div>
        <div class="week-body">
            @foreach($semaine['etapes'] as $etape)
            <div class="planning-item">
                <div class="planning-date">
                    <div class="day">{{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('DD') }}</div>
                    <div class="month">{{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('MMM') }}</div>
                    <div class="weekday">{{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('ddd') }}</div>
                </div>
                <div class="planning-info">
                    <h6 class="planning-title">{{ $etape->nom_etape }}</h6>
                    <p class="planning-project"><i class="fas fa-building me-1"></i> {{ $etape->projet->nom }}</p>
                    <div class="planning-meta">
                        <span><i class="fas fa-calendar-alt me-1"></i> 
                            {{ Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('D MMMM YYYY') }} 
                            - 
                            {{ Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                </div>
                <div class="planning-progress">
                    <div class="progress-label">
                        <span>Avancement</span>
                        <span class="progress-value">{{ $etape->progression }}%</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" style="width: {{ $etape->progression }}%"></div>
                    </div>
                </div>
                <div class="planning-status">
                    @if($etape->statut == 'termine')
                        <span class="badge-status success"><i class="fas fa-check-circle"></i> Terminée</span>
                    @elseif($etape->statut == 'en_cours')
                        <span class="badge-status primary"><i class="fas fa-play-circle"></i> En cours</span>
                    @elseif($etape->statut == 'en_retard')
                        <span class="badge-status danger"><i class="fas fa-exclamation-triangle"></i> En retard</span>
                    @else
                        <span class="badge-status secondary"><i class="fas fa-clock"></i> À venir</span>
                    @endif
                </div>
                <div class="planning-actions">
                    <a href="{{ route('chef-chantier.etapes.show', $etape->id) }}" class="btn-icon" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($etape->statut !== 'termine')
                    <a href="{{ route('chef-chantier.etapes.edit', $etape->id) }}" class="btn-icon update" title="Mettre à jour l'avancement">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="empty-planning">
        <i class="fas fa-calendar-alt fa-4x mb-3"></i>
        <h5>Aucune étape planifiée</h5>
        <p class="text-muted">Vous n'avez pas d'étapes pour la période sélectionnée.</p>
    </div>
    @endforelse
    
    {{-- PROCHAINE ÉCHÉANCE --}}
    @if(isset($stats['prochain']) && $stats['prochain'])
    <div class="next-task-card mt-4">
        <div class="next-task-icon">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="next-task-info">
            <span class="next-label"><i class="fas fa-bell me-1"></i> PROCHAINE ÉCHÉANCE</span>
            <h6 class="next-title">{{ $stats['prochain']->nom_etape }}</h6>
            <p class="next-project"><i class="fas fa-building me-1"></i> {{ $stats['prochain']->projet->nom }}</p>
            <small class="next-date"><i class="fas fa-calendar-alt me-1"></i> 
                {{ Carbon\Carbon::parse($stats['prochain']->date_fin_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </small>
        </div>
        <a href="{{ route('chef-chantier.etapes.edit', $stats['prochain']->id) }}" class="next-task-btn">
            METTRE À JOUR <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    @endif
</div>

@push('styles')
<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #3b82f6;
    --primary-soft: #dbeafe;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --dark: #1e293b;
    --gray: #64748b;
    --gray-light: #f8fafc;
    --gray-border: #e2e8f0;
    --white: #ffffff;
    --shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    --shadow-hover: 0 8px 25px rgba(37, 99, 235, 0.1);
}

.status-filters {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.status-filters-title {
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--gray);
    letter-spacing: 1px;
    text-transform: uppercase;
}

.status-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.2rem;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    background: var(--gray-light);
    color: var(--gray);
    border: 1px solid var(--gray-border);
}

.status-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.status-pill.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pill-count {
    background: rgba(0, 0, 0, 0.1);
    padding: 0.1rem 0.5rem;
    border-radius: 30px;
    font-size: 0.65rem;
    font-weight: 800;
}

.status-pill.active .pill-count {
    background: rgba(255, 255, 255, 0.25);
}

.planning-tabs {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 16px;
    padding: 0.5rem;
    display: inline-flex;
    gap: 0.5rem;
}

.tab-btn {
    padding: 0.6rem 1.8rem;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: var(--gray);
    transition: all 0.3s;
    text-transform: uppercase;
    border-radius: 12px;
}

.tab-btn.active {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.tab-btn:hover:not(.active) {
    background: var(--primary-soft);
    color: var(--primary-dark);
}

.filters-bar {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 20px;
    padding: 1.25rem;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.filter-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--gray);
    letter-spacing: 1px;
    text-transform: uppercase;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 1px solid var(--gray-border);
    border-radius: 12px;
    font-size: 0.8rem;
    background: var(--white);
    cursor: pointer;
}

.btn-filter-apply {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    cursor: pointer;
    text-transform: uppercase;
}

.btn-filter-reset {
    background: var(--gray-light);
    color: var(--gray);
    border: 1px solid var(--gray-border);
    padding: 0.5rem 1.2rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    text-decoration: none;
    margin-left: 0.5rem;
    text-transform: uppercase;
}

.stat-card-simple {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 20px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon-sm {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-soft { background: var(--primary-soft); }
.bg-success-soft { background: #d1fae5; }
.bg-warning-soft { background: #fef3c7; }
.bg-danger-soft { background: #fee2e2; }

.stat-icon-sm i { font-size: 1.4rem; }
.stat-number { font-size: 1.5rem; font-weight: 800; margin: 0; line-height: 1.2; }
.stat-text { font-size: 0.65rem; font-weight: 700; color: var(--gray); margin: 0; text-transform: uppercase; }

.week-card {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 20px;
    overflow: hidden;
}

.week-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--gray-light), var(--white));
    border-bottom: 1px solid var(--gray-border);
}

.week-icon {
    width: 45px;
    height: 45px;
    background: var(--primary-soft);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.week-title { font-size: 0.9rem; font-weight: 800; margin: 0; }
.week-count { font-size: 0.65rem; font-weight: 600; color: var(--gray); margin: 0.2rem 0 0; }

.planning-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-border);
    flex-wrap: wrap;
}

.planning-date {
    text-align: center;
    min-width: 65px;
    padding: 0.5rem;
    background: var(--white);
    border-radius: 16px;
}

.planning-date .day { font-size: 1.5rem; font-weight: 800; line-height: 1; color: var(--dark); }
.planning-date .month { font-size: 0.65rem; font-weight: 700; color: var(--gray); text-transform: uppercase; }
.planning-date .weekday { font-size: 0.6rem; color: var(--primary); font-weight: 600; text-transform: uppercase; }

.planning-info { flex: 2; min-width: 200px; }
.planning-title { font-size: 0.85rem; font-weight: 700; margin-bottom: 0.25rem; }
.planning-project { font-size: 0.7rem; color: var(--gray); margin-bottom: 0.25rem; }
.planning-meta { font-size: 0.65rem; color: var(--gray); }

.planning-progress { width: 160px; }
.progress-label { display: flex; justify-content: space-between; font-size: 0.65rem; font-weight: 600; margin-bottom: 0.3rem; }
.progress-value { font-weight: 800; color: var(--primary); }
.progress-bar-custom { background: var(--gray-border); height: 6px; border-radius: 10px; overflow: hidden; }
.progress-fill { background: linear-gradient(90deg, var(--primary), var(--primary-light)); height: 100%; border-radius: 10px; transition: width 0.6s ease; }

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 1rem;
    font-size: 0.65rem;
    font-weight: 800;
    border-radius: 40px;
    text-transform: uppercase;
}

.badge-status.success { background: #d1fae5; color: #059669; }
.badge-status.primary { background: var(--primary-soft); color: var(--primary-dark); }
.badge-status.danger { background: #fee2e2; color: #dc2626; }
.badge-status.secondary { background: var(--gray-light); color: var(--gray); }

.btn-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 10px;
    color: var(--gray);
    transition: all 0.3s;
}

.btn-icon:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.next-task-card {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    padding: 1rem 1.5rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    color: white;
}

.next-task-btn {
    background: white;
    color: var(--primary-dark);
    padding: 0.6rem 1.5rem;
    border-radius: 40px;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
}

@media (max-width: 768px) {
    .planning-item { flex-direction: column; align-items: stretch; }
    .planning-progress { width: 100%; }
    .status-filters { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush
@endsection