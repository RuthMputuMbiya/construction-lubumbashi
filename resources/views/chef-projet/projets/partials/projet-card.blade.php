{{-- resources/views/chef-projet/projets/partials/projet-card.blade.php --}}
<div class="projet-card" data-status="{{ $projet->statut }}" data-nom="{{ strtolower($projet->nom) }}" data-id="{{ $projet->id }}">
    <div class="projet-header">
        <div class="projet-status">
            @if($projet->statut == 'en_cours')
                <span class="status-badge status-en-cours"><i class="fas fa-play-circle"></i> EN COURS</span>
            @elseif($projet->statut == 'termine')
                <span class="status-badge status-termine"><i class="fas fa-check-circle"></i> TERMINÉ</span>
            @elseif($projet->statut == 'annule')
                <span class="status-badge status-annule"><i class="fas fa-ban"></i> ANNULÉ</span>
            @else
                <span class="status-badge status-attente"><i class="fas fa-clock"></i> EN ATTENTE</span>
            @endif
        </div>
        <div class="projet-code">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</div>
    </div>
    
    <div class="projet-body">
        <h5 class="projet-title">{{ $projet->nom }}</h5>
        <div class="projet-location">
            <i class="fas fa-map-marker-alt"></i> {{ $projet->lieu }}
        </div>
        <p class="projet-description">{{ Str::limit($projet->description, 80) }}</p>
        
        <div class="projet-dates">
            <div class="date-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Début: {{ \Carbon\Carbon::parse($projet->date_debut_prevue)->format('d/m/Y') }}</span>
            </div>
            <div class="date-item">
                <i class="fas fa-flag-checkered"></i>
                <span>Fin: {{ \Carbon\Carbon::parse($projet->date_fin_prevue)->format('d/m/Y') }}</span>
            </div>
        </div>
        
        <div class="projet-budget">
            <span class="budget-label">BUDGET TOTAL</span>
            <span class="budget-value">{{ number_format($projet->budget_total, 0, ',', ' ') }} FC</span>
        </div>
        
        <div class="projet-progress">
            <div class="progress-header">
                <span>AVANCEMENT</span>
                <span class="progress-percent">{{ $projet->avancement }}%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-fill" style="width: {{ $projet->avancement }}%"></div>
            </div>
        </div>
    </div>
    
    <div class="projet-footer">
        <a href="{{ route('chef-projet.projets.show', $projet->id) }}" class="btn-view">
            <i class="fas fa-eye"></i> DÉTAILS
        </a>
        @if($projet->statut == 'en_attente')
            <a href="{{ route('chef-projet.projets.edit', $projet->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> MODIFIER
            </a>
        @endif
    </div>
</div>