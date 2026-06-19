{{-- resources/views/chef-chantier/etapes/edit.blade.php --}}
{{-- METTRE À JOUR L'AVANCEMENT D'UNE ÉTAPE --}}

@extends('layouts.chef-chantier')

@section('title', 'Mettre à jour - Chef de Chantier')

@section('page-title', 'Mettre à jour l\'avancement')
@section('page-subtitle', $etape->nom_etape . ' - ' . $etape->projet->nom)

@section('content')
<div class="update-container">
    <div class="update-card">
        <div class="card-header-custom">
            <div class="header-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="header-info">
                <h4>MISE À JOUR DE L'ÉTAPE</h4>
                <p>Mettez à jour l'avancement de votre mission</p>
            </div>
        </div>
        
        <div class="card-body-custom">
            {{-- Informations de l'étape --}}
            <div class="info-section">
                <div class="info-group">
                    <label class="info-label">NOM DE L'ÉTAPE</label>
                    <div class="info-value">{{ $etape->nom_etape }}</div>
                </div>
                
                <div class="info-group">
                    <label class="info-label">DESCRIPTION</label>
                    <div class="info-value description">{{ $etape->description }}</div>
                </div>
                
                <div class="row-info">
                    <div class="info-group half">
                        <label class="info-label">DATE DE DÉBUT PRÉVUE</label>
                        <div class="info-value">
                            <i class="fas fa-calendar-alt"></i> 
                            {{ \Carbon\Carbon::parse($etape->date_debut_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </div>
                    </div>
                    <div class="info-group half">
                        <label class="info-label">DATE DE FIN PRÉVUE</label>
                        <div class="info-value">
                            <i class="fas fa-flag-checkered"></i> 
                            {{ \Carbon\Carbon::parse($etape->date_fin_prevue)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Formulaire de mise à jour --}}
            <form method="POST" action="{{ route('chef-chantier.etapes.update', $etape->id) }}" class="update-form">
                @csrf
                @method('PUT')
                
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-chart-line"></i>
                        <span>AVANCEMENT ACTUEL</span>
                    </div>
                    
                    {{-- Progression --}}
                    <div class="form-group">
                        <label class="form-label">Progression (%)</label>
                        <div class="progress-slider">
                            <input type="range" 
                                   name="progression" 
                                   class="slider" 
                                   min="0" 
                                   max="100" 
                                   step="5" 
                                   value="{{ $etape->progression }}"
                                   oninput="updateProgressValue(this.value)">
                            <div class="slider-value">
                                <span id="progressValue" class="progress-number">{{ $etape->progression }}</span>
                                <span class="progress-symbol">%</span>
                            </div>
                        </div>
                        <div class="progress-bar-preview">
                            <div class="preview-fill" style="width: {{ $etape->progression }}%"></div>
                        </div>
                    </div>
                    
                    {{-- Statut --}}
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select" required>
                            <option value="a_venir" {{ $etape->statut == 'a_venir' ? 'selected' : '' }}>
                                ⏳ À venir
                            </option>
                            <option value="en_cours" {{ $etape->statut == 'en_cours' ? 'selected' : '' }}>
                                ▶️ En cours
                            </option>
                            <option value="en_retard" {{ $etape->statut == 'en_retard' ? 'selected' : '' }}>
                                ⚠️ En retard
                            </option>
                            <option value="termine" {{ $etape->statut == 'termine' ? 'selected' : '' }}>
                                ✅ Terminée
                            </option>
                        </select>
                    </div>
                    
                    {{-- Commentaire (optionnel) --}}
                    <div class="form-group">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea name="commentaire" class="form-textarea" rows="4" 
                                  placeholder="Ajoutez un commentaire sur l'avancement, les difficultés rencontrées, etc."></textarea>
                    </div>
                </div>
                
                {{-- Boutons d'action --}}
                <div class="form-actions">
                    <a href="{{ route('chef-chantier.etapes.index') }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> ANNULER
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> ENREGISTRER
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.update-container {
    max-width: 800px;
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

.update-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s;
}

.update-card:hover {
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.1);
    border-color: #3b82f6;
}

.card-header-custom {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 55px;
    height: 55px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 1.5rem;
    color: white;
}

.header-info h4 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: white;
    text-transform: uppercase;
}

.header-info p {
    margin: 0.25rem 0 0;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
}

.card-body-custom {
    padding: 1.5rem;
}

.info-section {
    background: #f8fafc;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.info-group {
    margin-bottom: 1rem;
}

.info-group:last-child {
    margin-bottom: 0;
}

.info-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    margin-bottom: 0.3rem;
}

.info-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
}

.info-value.description {
    font-weight: 400;
    color: #475569;
    line-height: 1.5;
}

.row-info {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.info-group.half {
    flex: 1;
    margin-bottom: 0;
}

.info-value i {
    margin-right: 0.5rem;
    color: #2563eb;
}

/* Formulaire */
.form-section {
    margin-bottom: 1.5rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e2e8f0;
}

.section-title i {
    font-size: 1rem;
    color: #2563eb;
}

.section-title span {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #1e293b;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.progress-slider {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.slider {
    flex: 1;
    height: 6px;
    -webkit-appearance: none;
    background: #e2e8f0;
    border-radius: 10px;
    outline: none;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    background: #2563eb;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    transition: all 0.3s;
}

.slider::-webkit-slider-thumb:hover {
    transform: scale(1.2);
}

.slider-value {
    min-width: 70px;
    text-align: center;
}

.progress-number {
    font-size: 1.25rem;
    font-weight: 800;
    color: #2563eb;
}

.progress-symbol {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
}

.progress-bar-preview {
    margin-top: 0.5rem;
    background: #e2e8f0;
    height: 8px;
    border-radius: 10px;
    overflow: hidden;
}

.preview-fill {
    height: 100%;
    background: linear-gradient(90deg, #2563eb, #3b82f6);
    border-radius: 10px;
    transition: width 0.3s;
}

.form-select {
    width: 100%;
    padding: 0.6rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.85rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
}

.form-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-textarea {
    width: 100%;
    padding: 0.6rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.85rem;
    resize: vertical;
    font-family: inherit;
    transition: all 0.3s;
}

.form-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.btn-cancel, .btn-submit {
    padding: 0.6rem 1.5rem;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 40px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-cancel {
    background: #f1f5f9;
    color: #64748b;
}

.btn-cancel:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

.btn-submit {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

@media (max-width: 640px) {
    .card-body-custom {
        padding: 1rem;
    }
    
    .row-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-cancel, .btn-submit {
        width: 100%;
        justify-content: center;
    }
    
    .progress-slider {
        flex-direction: column;
        align-items: stretch;
    }
    
    .slider-value {
        text-align: left;
    }
}
</style>
@endpush

@push('scripts')
<script>
    function updateProgressValue(value) {
        document.getElementById('progressValue').innerText = value;
        const previewFill = document.querySelector('.preview-fill');
        if (previewFill) {
            previewFill.style.width = value + '%';
        }
    }
</script>
@endpush
@endsection