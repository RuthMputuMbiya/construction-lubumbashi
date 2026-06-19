@extends('layouts.chef-chantier')

@section('title', 'Détails de l\'étape - Chef de Chantier')

@section('page-title', 'Détails de l\'étape')
@section('page-subtitle', $etape->nom_etape)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            
            {{-- Bouton retour --}}
            <div class="mb-4">
                <a href="{{ route('chef-chantier.etapes.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                </a>
            </div>
            
            {{-- Message de succès --}}
            @if(session('success'))
            <div class="alert alert-success-custom alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            {{-- Carte principale --}}
            <div class="detail-card">
                {{-- En-tête --}}
                <div class="detail-header">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <h1 class="detail-title">{{ $etape->nom_etape }}</h1>
                            <p class="detail-project">
                                <i class="fas fa-building me-1"></i> {{ $etape->projet->nom }}
                                <span class="separator">|</span>
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $etape->projet->lieu }}
                            </p>
                        </div>
                    </div>
                    <div class="header-right">
                        @if($etape->statut == 'termine')
                            <span class="status-badge success"><i class="fas fa-check-circle"></i> TERMINÉE</span>
                        @elseif($etape->statut == 'en_cours')
                            <span class="status-badge primary"><i class="fas fa-play-circle"></i> EN COURS</span>
                        @elseif($etape->statut == 'en_retard')
                            <span class="status-badge danger"><i class="fas fa-exclamation-triangle"></i> EN RETARD</span>
                        @else
                            <span class="status-badge secondary"><i class="fas fa-clock"></i> À VENIR</span>
                        @endif
                    </div>
                </div>
                
                {{-- Corps --}}
                <div class="detail-body">
                    
                    {{-- Section Description --}}
                    <div class="info-card">
                        <div class="card-title">
                            <i class="fas fa-align-left"></i>
                            <span>DESCRIPTION</span>
                            <div class="title-line"></div>
                        </div>
                        <div class="description-content">
                            <p class="description-text">{{ $etape->description ?: 'Aucune description fournie.' }}</p>
                        </div>
                    </div>
                    
                    {{-- Section Calendrier --}}
                    <div class="info-card">
                        <div class="card-title">
                            <i class="fas fa-calendar-alt"></i>
                            <span>CALENDRIER</span>
                            <div class="title-line"></div>
                        </div>
                        <div class="dates-grid">
                            <div class="date-card">
                                <div class="date-icon"><i class="fas fa-play-circle"></i></div>
                                <div class="date-info">
                                    <div class="date-label">Début prévu</div>
                                    <div class="date-value">{{ \Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</div>
                                </div>
                            </div>
                            <div class="date-card">
                                <div class="date-icon"><i class="fas fa-flag-checkered"></i></div>
                                <div class="date-info">
                                    <div class="date-label">Fin prévue</div>
                                    <div class="date-value">{{ \Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</div>
                                </div>
                            </div>
                            @if($etape->date_debut_reelle)
                            <div class="date-card success">
                                <div class="date-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="date-info">
                                    <div class="date-label">Début réel</div>
                                    <div class="date-value">{{ \Carbon\Carbon::parse($etape->date_debut_reelle)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</div>
                                </div>
                            </div>
                            @endif
                            @if($etape->date_fin_reelle)
                            <div class="date-card success">
                                <div class="date-icon"><i class="fas fa-check-double"></i></div>
                                <div class="date-info">
                                    <div class="date-label">Fin réelle</div>
                                    <div class="date-value">{{ \Carbon\Carbon::parse($etape->date_fin_reelle)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Section Avancement --}}
                    <div class="info-card">
                        <div class="card-title">
                            <i class="fas fa-chart-line"></i>
                            <span>AVANCEMENT</span>
                            <div class="title-line"></div>
                        </div>
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Progression actuelle</span>
                                <span class="progress-value">{{ $etape->progression }}%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ $etape->progression }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Section Analyses --}}
                    <div class="info-card">
                        <div class="card-title">
                            <i class="fas fa-chart-simple"></i>
                            <span>ANALYSES</span>
                            <div class="title-line"></div>
                        </div>
                        <div class="metrics-grid">
                            <div class="metric-card">
                                <div class="metric-icon"><i class="fas fa-hourglass-start"></i></div>
                                <div class="metric-value">{{ $dureePrevue }}</div>
                                <div class="metric-label">jours prévus</div>
                            </div>
                            <div class="metric-card {{ $estEnRetard ? 'danger' : 'success' }}">
                                <div class="metric-icon"><i class="fas {{ $estEnRetard ? 'fa-exclamation-triangle' : 'fa-hourglass-half' }}"></i></div>
                                <div class="metric-value">
                                    @if($estEnRetard)
                                        En retard
                                    @else
                                        {{ number_format($joursRestants, 0) }} jours
                                    @endif
                                </div>
                                <div class="metric-label">délai restant</div>
                            </div>
                            @if($dureeReelle > 0)
                            <div class="metric-card">
                                <div class="metric-icon"><i class="fas fa-clock"></i></div>
                                <div class="metric-value">{{ $dureeReelle }}</div>
                                <div class="metric-label">jours écoulés</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Section Commentaires --}}
                    @if($etape->commentaires_chantier)
                    <div class="info-card">
                        <div class="card-title">
                            <i class="fas fa-comment"></i>
                            <span>COMMENTAIRES</span>
                            <div class="title-line"></div>
                        </div>
                        <div class="comment-section">
                            <div class="comment-avatar">
                                {{ strtoupper(substr(Auth::user()->nom ?? 'CC', 0, 2)) }}
                            </div>
                            <div class="comment-content">
                                <div class="comment-author">{{ Auth::user()->nom ?? 'Chef Chantier' }}</div>
                                <p class="comment-text">{{ $etape->commentaires_chantier }}</p>
                                <div class="comment-date">
                                    <i class="fas fa-calendar-alt me-1"></i> 
                                    {{ \Carbon\Carbon::parse($etape->updated_at)->locale('fr')->isoFormat('dddd D MMMM YYYY à HH:mm') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Section Justification de retard --}}
                    @if($etape->justification_retard)
                    <div class="info-card warning">
                        <div class="card-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>JUSTIFICATION DU RETARD</span>
                            <div class="title-line"></div>
                        </div>
                        <div class="justification-section">
                            <i class="fas fa-file-alt"></i>
                            <p>{{ $etape->justification_retard }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                {{-- Footer avec actions --}}
                <div class="detail-footer">
                    @if($etape->statut !== 'termine')
                        <a href="{{ route('chef-chantier.etapes.edit', $etape->id) }}" class="btn-update">
                            <i class="fas fa-sync-alt me-2"></i> METTRE À JOUR
                        </a>
                        <button onclick="terminerEtape({{ $etape->id }}, '{{ addslashes($etape->nom_etape) }}', {{ $etape->ordre }})" 
                                class="btn-terminer">
                            <i class="fas fa-check-double me-2"></i> TERMINER
                        </button>
                    @endif
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print me-2"></i> IMPRIMER
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Formulaire pour terminer --}}
<form id="terminer-form" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

@push('styles')
<style>
/* ============================================
   DESIGN PROFESSIONNEL - DÉTAILS DE L'ÉTAPE
   ============================================ */

:root {
    --primary: #1e3a5f;
    --primary-dark: #0f2b4a;
    --primary-light: #2a4a7a;
    --primary-soft: #e8f0fe;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --dark: #1e293b;
    --gray: #64748b;
    --gray-light: #f8fafc;
    --gray-border: #e2e8f0;
    --white: #ffffff;
    --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 12px 35px rgba(30, 58, 95, 0.12);
}

/* Bouton retour */
.btn-back {
    display: inline-flex;
    align-items: center;
    padding: 0.6rem 1.2rem;
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 40px;
    color: var(--gray);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateX(-3px);
    background: var(--primary-soft);
}

/* Alert */
.alert-success-custom {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: none;
    color: #065f46;
    border-radius: 16px;
    padding: 1rem 1.25rem;
}

/* Carte principale */
.detail-card {
    background: var(--white);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
}

.detail-card:hover {
    box-shadow: var(--shadow-hover);
}

/* En-tête */
.detail-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    padding: 1.75rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 55px;
    height: 55px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 1.5rem;
    color: white;
}

.detail-title {
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0;
    color: white;
}

.detail-project {
    font-size: 0.75rem;
    margin: 0.25rem 0 0;
    opacity: 0.8;
    color: white;
}

.detail-project .separator {
    margin: 0 0.5rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.2rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.status-badge.success { background: var(--success); color: white; }
.status-badge.primary { background: var(--info); color: white; }
.status-badge.danger { background: var(--danger); color: white; }
.status-badge.secondary { background: var(--secondary, #6c757d); color: white; }

/* Corps */
.detail-body {
    padding: 2rem;
}

.info-card {
    margin-bottom: 1.75rem;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--gray-border);
}

.card-title i {
    font-size: 1rem;
    color: var(--primary);
}

.card-title span {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: var(--dark);
    text-transform: uppercase;
}

.title-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, var(--gray-border), transparent);
}

/* Description */
.description-content {
    background: var(--gray-light);
    border-radius: 16px;
    padding: 1.25rem;
}

.description-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: var(--gray);
    margin: 0;
}

/* Dates grid */
.dates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
}

.date-card {
    background: var(--gray-light);
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s;
    border-left: 3px solid var(--primary);
}

.date-card:hover {
    transform: translateY(-2px);
    background: var(--primary-soft);
}

.date-card.success {
    border-left-color: var(--success);
}

.date-icon {
    width: 45px;
    height: 45px;
    background: var(--white);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.date-icon i {
    font-size: 1.2rem;
    color: var(--primary);
}

.date-card.success .date-icon i {
    color: var(--success);
}

.date-info {
    flex: 1;
}

.date-label {
    font-size: 0.6rem;
    font-weight: 700;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.date-value {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--dark);
}

/* Progress section */
.progress-section {
    background: var(--gray-light);
    border-radius: 16px;
    padding: 1.25rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--dark);
}

.progress-value {
    color: var(--primary);
    font-weight: 800;
}

.progress-bar-container {
    background: var(--gray-border);
    border-radius: 10px;
    height: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Metrics grid */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem;
}

.metric-card {
    background: var(--gray-light);
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s;
}

.metric-card:hover {
    transform: translateY(-3px);
    background: var(--primary-soft);
}

.metric-card.danger {
    background: #fef2f2;
}

.metric-card.success {
    background: #d1fae5;
}

.metric-icon {
    width: 40px;
    height: 40px;
    background: var(--white);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}

.metric-icon i {
    font-size: 1.1rem;
    color: var(--primary);
}

.metric-card.danger .metric-icon i {
    color: var(--danger);
}

.metric-card.success .metric-icon i {
    color: var(--success);
}

.metric-value {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--dark);
}

.metric-label {
    font-size: 0.6rem;
    font-weight: 700;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

/* Comment section */
.comment-section {
    display: flex;
    gap: 1rem;
    background: var(--gray-light);
    border-radius: 20px;
    padding: 1.25rem;
}

.comment-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 1rem;
}

.comment-content {
    flex: 1;
}

.comment-author {
    font-weight: 800;
    font-size: 0.85rem;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.comment-text {
    font-size: 0.85rem;
    color: var(--gray);
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.comment-date {
    font-size: 0.65rem;
    color: var(--gray);
}

/* Justification section */
.justification-section {
    background: #fffbeb;
    border-radius: 16px;
    padding: 1rem 1.25rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.justification-section i {
    font-size: 1.2rem;
    color: var(--warning);
}

.justification-section p {
    margin: 0;
    font-size: 0.85rem;
    color: #92400e;
    flex: 1;
    line-height: 1.5;
}

/* Footer */
.detail-footer {
    padding: 1.25rem 2rem;
    background: var(--gray-light);
    border-top: 1px solid var(--gray-border);
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-update, .btn-terminer, .btn-print {
    padding: 0.7rem 1.5rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.3s;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-update {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    text-decoration: none;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 58, 95, 0.35);
}

.btn-terminer {
    background: linear-gradient(135deg, var(--success), #059669);
    color: white;
}

.btn-terminer:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
}

.btn-print {
    background: var(--white);
    border: 1px solid var(--gray-border);
    color: var(--gray);
}

.btn-print:hover {
    background: var(--primary-soft);
    border-color: var(--primary);
    color: var(--primary);
}

/* Responsive */
@media (max-width: 768px) {
    .detail-header {
        padding: 1.25rem;
        flex-direction: column;
        text-align: center;
    }
    
    .header-left {
        flex-direction: column;
        text-align: center;
    }
    
    .detail-body {
        padding: 1.25rem;
    }
    
    .dates-grid {
        grid-template-columns: 1fr;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-footer {
        padding: 1rem;
        flex-direction: column;
    }
    
    .btn-update, .btn-terminer, .btn-print {
        width: 100%;
        text-align: center;
    }
}

@media print {
    .btn-back, .detail-footer, .btn-print, .sidebar, .topbar, .no-print, .alert-success-custom {
        display: none !important;
    }
    
    .detail-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .detail-header {
        background: #f0f0f0;
        color: #000;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endpush

@push('scripts')
<script>
function terminerEtape(etapeId, etapeNom, ordre) {
    let message = `✅ Voulez-vous marquer l'étape "${etapeNom}" comme TERMINÉE ?\n\n`;
    message += `Cette action mettra la progression à 100%.\n`;
    
    if (ordre == 5) {
        message += `\n🏆 C'est la DERNIÈRE ÉTAPE (Validation) !\nLe projet sera automatiquement marqué comme TERMINÉ !`;
    }
    
    if(confirm(message)) {
        const form = document.getElementById('terminer-form');
        form.action = '/chef-chantier/etapes/' + etapeId + '/terminer';
        form.submit();
    }
}
</script>
@endpush
@endsection