{{-- resources/views/chef-chantier/dashboard.blade.php --}}
{{-- DASHBOARD CHEF DE CHANTIER - DESIGN BLEU PROFESSIONNEL --}}
{{-- 🔥 SUPPRESSION DU LIEN "MON PROFIL" DANS LES ACTIONS RAPIDES --}}

@extends('layouts.chef-chantier')

@section('title', 'Dashboard Chef Chantier - ConstrucTrack')

@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Chef de Chantier - Lubumbashi')

@section('content')
<div class="chantier-dashboard">
    
    {{-- ========================================== --}}
    {{-- EN-TÊTE AVEC SALUTATION --}}
    {{-- ========================================== --}}
    <div class="welcome-header">
        <div>
            <h2>Bonjour, <span class="text-primary">{{ Auth::user()->nom ?? 'Chef Chantier' }}</span> 👷‍♂️</h2>
            <p>Voici l'avancement de vos chantiers aujourd'hui</p>
        </div>
        <div class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- STATISTIQUES (CARTES BLEUES) --}}
    {{-- ========================================== --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">MES ÉTAPES</span>
                <span class="stat-value">{{ $stats['total'] }}</span>
                <span class="stat-trend">Assignées</span>
            </div>
            <div class="stat-glow primary-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">TERMINÉES</span>
                <span class="stat-value">{{ $stats['terminees'] }}</span>
                <div class="progress-mini">
                    <div class="progress-mini-fill" style="width: {{ $stats['total'] > 0 ? ($stats['terminees'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
            <div class="stat-glow success-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">EN COURS</span>
                <span class="stat-value">{{ $stats['en_cours'] }}</span>
            </div>
            <div class="stat-glow warning-glow"></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">EN RETARD</span>
                <span class="stat-value">{{ $stats['en_retard'] }}</span>
            </div>
            <div class="stat-glow danger-glow"></div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- AVANCEMENT GLOBAL AVEC CERCLE --}}
    {{-- ========================================== --}}
    <div class="chart-card">
        <div class="chart-header">
            <h5><i class="fas fa-chart-line"></i> AVANCEMENT GLOBAL</h5>
            <span class="badge">{{ $stats['avancement_moyen'] }}% de complétion</span>
        </div>
        <div class="chart-body">
            <div class="global-progress">
                <div class="global-progress-circle">
                    <svg viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#2563eb" stroke-width="8"
                                stroke-dasharray="339.292" stroke-dashoffset="{{ 339.292 * (1 - $stats['avancement_moyen']/100) }}"
                                transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="global-progress-value">
                        <span class="value">{{ $stats['avancement_moyen'] }}</span>
                        <span class="unit">%</span>
                    </div>
                </div>
                <div class="global-progress-stats">
                    <div class="stat-item">
                        <span class="stat-num">{{ $stats['total'] }}</span>
                        <span class="stat-label">Total étapes</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num text-success">{{ $stats['terminees'] }}</span>
                        <span class="stat-label">Terminées</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num text-warning">{{ $stats['en_cours'] }}</span>
                        <span class="stat-label">En cours</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num text-danger">{{ $stats['en_retard'] }}</span>
                        <span class="stat-label">En retard</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- ALERTES URGENTES --}}
    {{-- ========================================== --}}
    @if(isset($urgentes) && $urgentes->count() > 0)
    <div class="alert-card">
        <div class="alert-header">
            <h5><i class="fas fa-bell"></i> ALERTES URGENTES</h5>
            <span class="alert-count">{{ $urgentes->count() }} étape(s)</span>
        </div>
        <div class="alert-body">
            @foreach($urgentes as $etape)
            <div class="alert-item">
                <div class="alert-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="alert-content">
                    <h6>{{ $etape->nom_etape }}</h6>
                    <p>{{ $etape->projet->nom }}</p>
                    <small><i class="fas fa-calendar"></i> Échéance: {{ \Carbon\Carbon::parse($etape->date_fin_prevue)->format('d/m/Y') }}</small>
                </div>
                <a href="{{ route('chef-chantier.etapes.edit', $etape->id) }}" class="alert-btn">
                    <i class="fas fa-arrow-right"></i> METTRE À JOUR
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- ========================================== --}}
    {{-- PROCHAINE ÉCHÉANCE (CARTE BLEUE) --}}
    {{-- ========================================== --}}
    @if(isset($stats['prochain']) && $stats['prochain'])
    <div class="next-card">
        <div class="next-icon">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="next-info">
            <span class="next-label">PROCHAINE ÉCHÉANCE</span>
            <h4>{{ $stats['prochain']->nom_etape }}</h4>
            <p>{{ $stats['prochain']->projet->nom }}</p>
            <small><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($stats['prochain']->date_fin_prevue)->format('d/m/Y') }}</small>
        </div>
        <a href="{{ route('chef-chantier.etapes.edit', $stats['prochain']->id) }}" class="next-btn">
            METTRE À JOUR <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    @endif
    
    {{-- ========================================== --}}
    {{-- MES PROJETS --}}
    {{-- ========================================== --}}
    <div class="table-card">
        <div class="table-header">
            <h5><i class="fas fa-building"></i> MES PROJETS</h5>
            <a href="{{ route('chef-chantier.etapes.index') }}" class="btn-link">VOIR TOUTES MES SUIVIS →</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PROJET</th>
                        <th>LIEU</th>
                        <th>AVANCEMENT</th>
                        <th>ÉTAPES</th>
                        <th>ÉCHÉANCE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projets as $projet)
                    <tr>
                        <td><strong class="text-primary">{{ $projet->nom }}</strong></td>
                        <td><i class="fas fa-map-marker-alt" style="color: #2563eb;"></i> {{ $projet->lieu }}</td>
                        <td>
                            <div class="progress-cell">
                                <div class="progress-bar"><div class="progress-fill" style="width: {{ $projet->avancement }}%"></div></div>
                                <span>{{ $projet->avancement }}%</span>
                            </div>
                        </td>
                        <td>{{ $projet->etapes_count }} étapes ({{ $projet->terminees_count }} terminées)</td>
                        <td class="{{ $projet->jours_restants < 0 ? 'text-danger' : ($projet->jours_restants < 3 ? 'text-warning' : '') }}">
                            <i class="fas fa-calendar"></i> 
                            @if($projet->jours_restants < 0)
                                En retard
                            @elseif($projet->jours_restants == 0)
                                Aujourd'hui
                            @else
                                {{ $projet->jours_restants }} jour(s)
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('chef-chantier.etapes.index', ['projet' => $projet->id]) }}" class="action-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                <i class="fas fa-folder-open"></i>
                                <p>Aucun projet assigné</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- ========================================== --}}
    {{-- ACTIONS RAPIDES --}}
    {{-- ========================================== --}}
    <div class="actions-card">
        <div class="actions-header">
            <h5><i class="fas fa-bolt"></i> ACTIONS RAPIDES</h5>
        </div>
        <div class="actions-grid">
            <a href="{{ route('chef-chantier.etapes.index') }}" class="action-item">
                <i class="fas fa-tasks"></i>
                <span>Suivi avancement</span>
                <small>Voir toutes mes avancement </small>
            </a>
            <a href="{{ route('chef-chantier.planning.index') }}" class="action-item">
                <i class="fas fa-calendar-week"></i>
                <span>MON PLANNING</span>
                <small>Voir mon planning</small>
            </a>
            {{-- SUPPRESSION DU LIEN "MON PROFIL"  --}}
        </div>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   DASHBOARD CHEF CHANTIER - DESIGN BLEU PROFESSIONNEL
   ============================================ */

:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #3b82f6;
    --primary-soft: #dbeafe;
    --secondary: #f59e0b;
    --success: #10b981;
    --danger: #ef4444;
    --gray-bg: #f8fafc;
    --white: #ffffff;
    --text-dark: #1e293b;
    --text-gray: #64748b;
    --border: #e2e8f0;
    --shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    --shadow-hover: 0 20px 35px rgba(37, 99, 235, 0.1);
}

.chantier-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ==========================================
   EN-TÊTE
   ========================================== */
.welcome-header {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    transition: all 0.3s;
}

.welcome-header:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-hover);
}

.welcome-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
}

.text-primary {
    color: var(--primary);
    font-weight: 800;
}

.welcome-header p {
    margin: 0.25rem 0 0;
    color: var(--text-gray);
}

.date-badge {
    background: var(--primary-soft);
    border-radius: 40px;
    padding: 0.5rem 1.2rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-dark);
    font-size: 0.85rem;
    font-weight: 500;
}

/* ==========================================
   STATISTIQUES CARTES
   ========================================== */
.stats-grid {
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
    border-radius: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-light);
    box-shadow: var(--shadow-hover);
}

.stat-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.primary-glow { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
.success-glow { background: linear-gradient(90deg, #10b981, #34d399); }
.warning-glow { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.danger-glow { background: linear-gradient(90deg, #ef4444, #f87171); }

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.5rem;
    color: white;
}

.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.7rem;
    color: var(--text-gray);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    display: block;
    font-weight: 700;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-dark);
    display: block;
    line-height: 1.2;
}

.stat-trend {
    font-size: 0.65rem;
    color: var(--text-gray);
}

.progress-mini {
    margin-top: 0.5rem;
    height: 3px;
    background: var(--border);
    border-radius: 10px;
    overflow: hidden;
}

.progress-mini-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    border-radius: 10px;
}

/* ==========================================
   GRAPHIQUE AVANCEMENT GLOBAL
   ========================================== */
.chart-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s;
}

.chart-card:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-hover);
}

.chart-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--gray-bg);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.chart-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-dark);
}

.chart-header h5 i {
    margin-right: 0.5rem;
    color: var(--primary);
}

.chart-header .badge {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 0.3rem 1rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.chart-body {
    padding: 1.5rem;
}

.global-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.global-progress-circle {
    position: relative;
    width: 150px;
    height: 150px;
}

.global-progress-circle svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.global-progress-circle svg circle:last-child {
    transition: stroke-dashoffset 0.8s ease;
}

.global-progress-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.global-progress-value .value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--primary);
}

.global-progress-value .unit {
    font-size: 0.7rem;
    color: var(--text-gray);
}

.global-progress-stats {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.global-progress-stats .stat-item {
    text-align: center;
}

.global-progress-stats .stat-num {
    font-size: 1.25rem;
    font-weight: 800;
    display: block;
}

.global-progress-stats .stat-num.text-success { color: #10b981; }
.global-progress-stats .stat-num.text-warning { color: #f59e0b; }
.global-progress-stats .stat-num.text-danger { color: #ef4444; }

.global-progress-stats .stat-label {
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--text-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ==========================================
   ALERTES URGENTES
   ========================================== */
.alert-card {
    background: var(--white);
    border: 1px solid #fee2e2;
    border-radius: 20px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s;
}

.alert-card:hover {
    border-color: #ef4444;
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.08);
}

.alert-header {
    padding: 1rem 1.5rem;
    background: #fef2f2;
    border-bottom: 1px solid #fee2e2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 800;
    color: #dc2626;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.alert-header h5 i {
    margin-right: 0.5rem;
}

.alert-count {
    background: #dc2626;
    color: white;
    padding: 0.2rem 0.8rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.alert-body {
    padding: 0;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #fee2e2;
    flex-wrap: wrap;
}

.alert-icon {
    width: 45px;
    height: 45px;
    background: #fee2e2;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc2626;
}

.alert-icon i {
    font-size: 1.2rem;
}

.alert-content {
    flex: 1;
}

.alert-content h6 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
}

.alert-content p {
    margin: 0.25rem 0;
    font-size: 0.75rem;
    color: var(--text-gray);
}

.alert-content small {
    font-size: 0.65rem;
    color: var(--text-gray);
}

.alert-btn {
    background: #dc2626;
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.alert-btn:hover {
    background: #b91c1c;
    transform: translateY(-2px);
    color: white;
}

/* ==========================================
   PROCHAINE ÉCHÉANCE (CARTE BLEUE)
   ========================================== */
.next-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 20px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    color: white;
    transition: all 0.3s;
}

.next-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(37, 99, 235, 0.3);
}

.next-icon {
    width: 55px;
    height: 55px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.next-icon i {
    font-size: 1.5rem;
}

.next-info {
    flex: 1;
}

.next-label {
    font-size: 0.65rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    font-weight: 700;
    opacity: 0.8;
}

.next-info h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
}

.next-info p {
    margin: 0.25rem 0;
    font-size: 0.75rem;
    opacity: 0.9;
}

.next-info small {
    font-size: 0.65rem;
    opacity: 0.7;
}

.next-btn {
    background: white;
    color: var(--primary);
    padding: 0.6rem 1.5rem;
    border-radius: 40px;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s;
}

.next-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    color: var(--primary-dark);
}

/* ==========================================
   TABLEAU DES PROJETS
   ========================================== */
.table-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s;
}

.table-card:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-hover);
}

.table-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--gray-bg);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.table-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-dark);
}

.table-header h5 i {
    margin-right: 0.5rem;
    color: var(--primary);
}

.btn-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    transition: 0.3s;
}

.btn-link:hover {
    text-decoration: underline;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    padding: 1rem 1.25rem;
    background: var(--gray-bg);
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--text-gray);
    text-transform: uppercase;
    letter-spacing: 1px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.data-table td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.85rem;
    vertical-align: middle;
    color: var(--text-dark);
}

.data-table tr:hover {
    background: rgba(37, 99, 235, 0.02);
}

.text-primary {
    color: var(--primary);
    font-weight: 700;
}

.progress-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 100px;
}

.progress-cell .progress-bar {
    flex: 1;
    height: 6px;
    background: var(--border);
    border-radius: 10px;
    overflow: hidden;
}

.progress-cell .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    border-radius: 10px;
}

.progress-cell span {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--primary);
}

.text-danger {
    color: #dc2626;
}

.text-warning {
    color: #d97706;
}

.action-btn {
    width: 34px;
    height: 34px;
    background: var(--primary-soft);
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s;
    color: var(--primary);
}

.action-btn:hover {
    background: var(--primary);
    color: white;
    transform: scale(1.05);
}

.empty-row {
    text-align: center;
    padding: 3rem !important;
}

.empty-row i {
    font-size: 3rem;
    color: var(--primary);
    opacity: 0.5;
    margin-bottom: 0.5rem;
    display: block;
}

.empty-row p {
    color: var(--text-gray);
}

/* ==========================================
   ACTIONS RAPIDES
   ========================================== */
.actions-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s;
}

.actions-card:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-hover);
}

.actions-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--gray-bg);
}

.actions-header h5 {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-dark);
}

.actions-header h5 i {
    margin-right: 0.5rem;
    color: var(--primary);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1.5rem;
}

.action-item {
    background: var(--gray-bg);
    padding: 1.25rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid var(--border);
    border-radius: 16px;
}

.action-item:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    background: white;
    box-shadow: var(--shadow-hover);
}

.action-item i {
    font-size: 1.8rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
    display: block;
}

.action-item span {
    display: block;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.action-item small {
    font-size: 0.65rem;
    color: var(--text-gray);
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 992px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .welcome-header {
        flex-direction: column;
        text-align: center;
    }
    
    .global-progress {
        flex-direction: column;
        text-align: center;
    }
    
    .alert-item {
        flex-direction: column;
        text-align: center;
    }
    
    .next-card {
        flex-direction: column;
        text-align: center;
    }
    
    .data-table th, .data-table td {
        padding: 0.75rem;
    }
    
    .progress-cell {
        min-width: 80px;
    }
}
</style>
@endpush
@endsection