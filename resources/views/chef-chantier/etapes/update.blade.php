{{-- resources/views/chef-chantier/etapes/update.blade.php --}}
@extends('layouts.app')

@section('title', 'Mettre à jour l\'avancement - Chef de Chantier')

@section('page-title', 'Mettre à jour l\'avancement')
@section('page-subtitle', $etape->nom_etape)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Bouton retour --}}
            <div class="mb-3">
                <a href="{{ route('chef-chantier.etapes.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> RETOUR À MES ÉTAPES
                </a>
            </div>
            
            {{-- Carte principale --}}
            <div class="update-card">
                <div class="update-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-0">{{ $etape->nom_etape }}</h4>
                            <p class="mb-0 mt-1"><i class="fas fa-building me-1"></i> {{ $etape->projet->nom }}</p>
                        </div>
                        <div class="update-status">
                            @if($etape->statut == 'termine')
                                <span class="badge-success"><i class="fas fa-check-circle"></i> TERMINÉE</span>
                            @elseif($etape->statut == 'en_cours')
                                <span class="badge-primary"><i class="fas fa-play-circle"></i> EN COURS</span>
                            @elseif($etape->statut == 'en_retard')
                                <span class="badge-danger"><i class="fas fa-exclamation-triangle"></i> EN RETARD</span>
                            @else
                                <span class="badge-secondary"><i class="fas fa-clock"></i> NON COMMENCÉE</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('chef-chantier.etapes.update', $etape->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="update-body">
                        
                        {{-- ========================================== --}}
                        {{-- SECTION PROGRESSION - SLIDER INTERACTIF --}}
                        {{-- ========================================== --}}
                        <div class="progress-section">
                            <div class="section-title">
                                <i class="fas fa-chart-line"></i> AVANCEMENT DE L'ÉTAPE
                            </div>
                            
                            <div class="progress-slider-container">
                                <div class="current-progress-display">
                                    <div class="progress-circle" id="progressCircle">
                                        <span class="progress-percentage" id="percentageDisplay">0</span>
                                        <span class="progress-symbol">%</span>
                                    </div>
                                </div>
                                
                                <div class="slider-wrapper">
                                    <input type="range" name="progression" id="progressionSlider" 
                                           class="progress-slider" min="0" max="100" step="5"
                                           value="{{ old('progression', $etape->progression) }}">
                                    <div class="slider-labels">
                                        <span>0%</span>
                                        <span>25%</span>
                                        <span>50%</span>
                                        <span>75%</span>
                                        <span>100%</span>
                                    </div>
                                </div>
                                
                                <div class="quick-buttons">
                                    <button type="button" class="quick-btn" data-value="0">0%</button>
                                    <button type="button" class="quick-btn" data-value="25">25%</button>
                                    <button type="button" class="quick-btn" data-value="50">50%</button>
                                    <button type="button" class="quick-btn" data-value="75">75%</button>
                                    <button type="button" class="quick-btn complete" data-value="100">100% ✓ TERMINER</button>
                                </div>
                            </div>
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- INFORMATIONS DE L'ÉTAPE --}}
                        {{-- ========================================== --}}
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i> INFORMATIONS
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Description</span>
                                    <span class="info-value">{{ $etape->description }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Dates prévues</span>
                                    <span class="info-value">{{ \Carbon\Carbon::parse($etape->date_debut_prevue)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($etape->date_fin_prevue)->format('d/m/Y') }}</span>
                                </div>
                                @if($etape->date_debut_reelle)
                                <div class="info-item">
                                    <span class="info-label">Début réel</span>
                                    <span class="info-value text-success">{{ \Carbon\Carbon::parse($etape->date_debut_reelle)->format('d/m/Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- COMMENTAIRES --}}
                        {{-- ========================================== --}}
                        <div class="comment-section">
                            <div class="section-title">
                                <i class="fas fa-comment"></i> COMMENTAIRES
                            </div>
                            <textarea name="commentaires_chantier" class="comment-textarea" 
                                      rows="3" placeholder="Décrivez l'avancement, les difficultés rencontrées, le travail effectué...">{{ old('commentaires_chantier', $etape->commentaires_chantier) }}</textarea>
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- SECTION RETARD (SI EN RETARD) --}}
                        {{-- ========================================== --}}
                        @if($estEnRetard && $etape->progression < 100)
                        <div class="retard-section">
                            <div class="retard-header">
                                <i class="fas fa-exclamation-triangle"></i> JUSTIFICATION DU RETARD
                            </div>
                            <textarea name="justification_retard" class="retard-textarea" 
                                      rows="3" placeholder="Expliquez les raisons du retard...">{{ old('justification_retard', $etape->justification_retard) }}</textarea>
                            @error('justification_retard')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        
                        {{-- ========================================== --}}
                        {{-- ALERTE PROGRESSION --}}
                        {{-- ========================================== --}}
                        <div class="progress-alert" id="progressAlert" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <span>Félicitations ! Cette étape est maintenant terminée.</span>
                        </div>
                    </div>
                    
                    <div class="update-footer">
                        <a href="{{ route('chef-chantier.etapes.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> ANNULER
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> ENREGISTRER LA MISE À JOUR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   MISE À JOUR ÉTAPE - DESIGN CARRÉ PROFESSIONNEL
   ============================================ */

.btn-back {
    display: inline-flex;
    align-items: center;
    color: #64748b;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.2s;
}

.btn-back:hover {
    color: #1a56db;
}

.update-card {
    background: white;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.update-header {
    padding: 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.update-header h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
}

.badge-success, .badge-primary, .badge-danger, .badge-secondary {
    padding: 0.3rem 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
}
.badge-success { background: #d1fae5; color: #059669; }
.badge-primary { background: #dbeafe; color: #1d4ed8; }
.badge-danger { background: #fee2e2; color: #dc2626; }
.badge-secondary { background: #f1f5f9; color: #64748b; }

.update-body {
    padding: 1.5rem;
}

/* Section titre */
.section-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #1e293b;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-title i {
    margin-right: 0.5rem;
    color: #1a56db;
}

/* ==========================================
   PROGRESS SLIDER
   ========================================== */
.progress-section {
    background: #f8fafc;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.progress-slider-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.current-progress-display {
    text-align: center;
}

.progress-circle {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 10px 25px rgba(26,86,219,0.3);
}

.progress-percentage {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
}

.progress-symbol {
    font-size: 0.8rem;
    opacity: 0.9;
}

.slider-wrapper {
    width: 100%;
    max-width: 500px;
}

.progress-slider {
    width: 100%;
    height: 8px;
    -webkit-appearance: none;
    background: #e2e8f0;
    border-radius: 0;
    outline: none;
}

.progress-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    background: #1a56db;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.slider-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.6rem;
    color: #64748b;
}

.quick-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
}

.quick-btn {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 0.4rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-btn:hover {
    background: #1a56db;
    color: white;
    border-color: #1a56db;
}

.quick-btn.complete {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.quick-btn.complete:hover {
    background: #059669;
}

/* ==========================================
   INFO SECTION
   ========================================== */
.info-section, .comment-section, .retard-section {
    margin-bottom: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.65rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.info-value {
    font-size: 0.85rem;
    color: #1e293b;
}

.text-success {
    color: #059669;
}

.comment-textarea, .retard-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
    resize: vertical;
    transition: all 0.2s;
}

.comment-textarea:focus, .retard-textarea:focus {
    outline: none;
    border-color: #1a56db;
    box-shadow: 0 0 0 2px rgba(26,86,219,0.1);
}

/* Retard section */
.retard-section {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    padding: 1rem;
}

.retard-header {
    font-size: 0.7rem;
    font-weight: 700;
    color: #dc2626;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.retard-textarea {
    background: white;
    border-color: #fecaca;
}

.error-message {
    color: #dc2626;
    font-size: 0.65rem;
    margin-top: 0.25rem;
}

/* Alerte progression */
.progress-alert {
    background: #d1fae5;
    border: 1px solid #a7f3d0;
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #059669;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Footer */
.update-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-cancel {
    background: #f1f5f9;
    color: #64748b;
    padding: 0.6rem 1.5rem;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-cancel:hover {
    background: #e2e8f0;
}

.btn-submit {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26,86,219,0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .update-footer {
        flex-direction: column;
    }
    
    .quick-buttons {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Slider et affichage du pourcentage
const slider = document.getElementById('progressionSlider');
const percentageDisplay = document.getElementById('percentageDisplay');
const progressAlert = document.getElementById('progressAlert');

// Initialiser l'affichage
function updateDisplay() {
    const value = slider.value;
    percentageDisplay.textContent = value;
    
    // Changer la couleur du cercle selon le pourcentage
    const circle = document.querySelector('.progress-circle');
    if (value >= 100) {
        circle.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        progressAlert.style.display = 'flex';
    } else if (value >= 50) {
        circle.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
        progressAlert.style.display = 'none';
    } else {
        circle.style.background = 'linear-gradient(135deg, #1a56db, #0e3a8a)';
        progressAlert.style.display = 'none';
    }
}

// Événement du slider
slider.addEventListener('input', updateDisplay);
updateDisplay();

// Boutons rapides
document.querySelectorAll('.quick-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const value = this.dataset.value;
        slider.value = value;
        updateDisplay();
    });
});

// Afficher une confirmation avant soumission si progression < 100
document.querySelector('form').addEventListener('submit', function(e) {
    const progression = slider.value;
    if (progression < 100 && progression > 0) {
        if (!confirm('⚠️ Vous n\'avez pas encore terminé cette étape (progression: ' + progression + '%).\nConfirmez-vous cette mise à jour ?')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
@endsection