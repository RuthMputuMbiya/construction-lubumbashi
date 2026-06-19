{{-- resources/views/components/cards/project-card.blade.php --}}
@props(['project'])

<div class="project-card" data-aos="fade-up">
    <div class="project-card-header">
        <div>
            <span class="project-code">{{ $project->code_projet }}</span>
            <h4 class="project-title">{{ $project->nom }}</h4>
        </div>
        <span class="project-status status-{{ $project->statut }}">
            @if($project->statut == 'en_attente')
                <i class="fas fa-clock"></i> En attente
            @elseif($project->statut == 'en_cours')
                <i class="fas fa-play-circle"></i> En cours
            @elseif($project->statut == 'termine')
                <i class="fas fa-check-circle"></i> Terminé
            @else
                <i class="fas fa-ban"></i> Annulé
            @endif
        </span>
    </div>
    
    <div class="project-card-body">
        <div class="project-info">
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $project->lieu }}</span>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ \Carbon\Carbon::parse($project->date_debut_prevue)->format('d/m/Y') }} - 
                      {{ \Carbon\Carbon::parse($project->date_fin_prevue)->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <i class="fas fa-money-bill-wave"></i>
                <span>{{ number_format($project->budget_total, 0, ',', ' ') }} FC</span>
            </div>
        </div>
        
        <div class="project-progress">
            <div class="progress-header">
                <span>Avancement</span>
                <span class="progress-percentage">{{ $project->calculerAvancementGlobal() }}%</span>
            </div>
            <div class="progress-bar-custom">
                <div class="progress-fill" style="width: {{ $project->calculerAvancementGlobal() }}%"></div>
            </div>
        </div>
    </div>
    
    <div class="project-card-footer">
        <a href="{{ route('chef-projet.projets.show', $project->id) }}" class="btn-view">
            <i class="fas fa-eye"></i> Voir détails
        </a>
        <div class="project-actions">
            @if($project->statut == 'en_attente')
                <a href="{{ route('chef-projet.projets.edit', $project->id) }}" class="btn-icon">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn-icon" onclick="confirmCancel('{{ $project->id }}')">
                    <i class="fas fa-ban"></i>
                </button>
            @endif
        </div>
    </div>
</div>

<style>
.project-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.project-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.project-card-header {
    background: linear-gradient(135deg, #f9fafb, #ffffff);
    padding: 1.2rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #f3f4f6;
}

.project-code {
    font-size: 0.7rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.project-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    margin: 5px 0 0;
}

.project-status {
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-en_attente {
    background: #fef3c7;
    color: #d97706;
}

.status-en_cours {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-termine {
    background: #d1fae5;
    color: #059669;
}

.status-annule {
    background: #fee2e2;
    color: #dc2626;
}

.project-card-body {
    padding: 1.5rem;
}

.project-info {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.85rem;
    color: #4b5563;
}

.info-item i {
    width: 20px;
    color: #1a56db;
}

.project-progress {
    margin-top: 1rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
    color: #6b7280;
}

.progress-percentage {
    font-weight: 600;
    color: #1a56db;
}

.progress-bar-custom {
    background: #f3f4f6;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
}

.progress-fill {
    background: linear-gradient(90deg, #10b981, #1a56db);
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}

.project-card-footer {
    padding: 1rem 1.5rem;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f3f4f6;
}

.btn-view {
    background: none;
    border: none;
    color: #1a56db;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-view:hover {
    color: #0e3a8a;
    transform: translateX(5px);
}

.project-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    background: none;
    border: none;
    padding: 5px 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    color: #6b7280;
}

.btn-icon:hover {
    background: #e5e7eb;
    color: #1a56db;
}
</style>