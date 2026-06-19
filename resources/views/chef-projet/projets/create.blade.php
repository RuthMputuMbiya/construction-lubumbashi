{{-- resources/views/chef-projet/projets/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un projet - Chef de Projet')

@section('page-title', 'Créer un nouveau projet')
@section('page-subtitle', 'Remplissez les informations pour démarrer votre projet')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-card-header">
                    <div class="header-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="header-text">
                        <h5 class="mb-0">NOUVEAU PROJET</h5>
                        <p class="text-muted small mb-0 mt-1">Renseignez les détails de votre projet de construction</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('chef-projet.projets.store') }}">
                    @csrf
                    
                    <div class="form-card-body">
                        <div class="row g-4">
                            {{-- Nom du projet --}}
                            <div class="col-12">
                                <label class="form-label-custom">NOM DU PROJET <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" name="nom" class="form-control-custom @error('nom') is-invalid @enderror" 
                                           value="{{ old('nom') }}" required placeholder="Ex: Construction Immeuble Kipushi">
                                </div>
                                @error('nom')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Lieu --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">LIEU <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <input type="text" name="lieu" class="form-control-custom @error('lieu') is-invalid @enderror" 
                                           value="{{ old('lieu') }}" required placeholder="Ex: Lubumbashi, Quartier Golf">
                                </div>
                                @error('lieu')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Date début --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">DATE DE DÉBUT PRÉVUE <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" name="date_debut_prevue" class="form-control-custom @error('date_debut_prevue') is-invalid @enderror" 
                                           value="{{ old('date_debut_prevue') }}" required>
                                </div>
                                @error('date_debut_prevue')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Date fin --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">DATE DE FIN PRÉVUE <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-flag-checkered"></i>
                                    <input type="date" name="date_fin_prevue" class="form-control-custom @error('date_fin_prevue') is-invalid @enderror" 
                                           value="{{ old('date_fin_prevue') }}" required>
                                </div>
                                @error('date_fin_prevue')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- BUDGET EN FRANCS CONGOLAIS (FC) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">BUDGET (FC)</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <input type="number" name="budget_total_fc" class="form-control-custom" 
                                           value="{{ old('budget_total_fc') }}" placeholder="Ex: 10000000" step="1000">
                                </div>
                                <small class="form-hint">Montant en Francs Congolais (optionnel)</small>
                            </div>
                            
                            {{-- BUDGET EN DOLLARS USD --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">BUDGET (USD)</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-dollar-sign"></i>
                                    <input type="number" name="budget_total_usd" class="form-control-custom" 
                                           value="{{ old('budget_total_usd') }}" placeholder="Ex: 50000" step="100">
                                </div>
                                <small class="form-hint">Montant en Dollars Américains (optionnel)</small>
                            </div>
                            
                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label-custom">DESCRIPTION <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-align-left"></i>
                                    <textarea name="description" class="form-control-custom @error('description') is-invalid @enderror" 
                                              rows="5" required placeholder="Décrivez votre projet...">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- Information conseil --}}
                        <div class="info-box mt-4">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <strong>INFORMATION</strong><br>
                                <small>Le budget pourra être validé ultérieurement par le chef de projet. Vous pouvez saisir le montant en FC (Francs Congolais) et/ou en USD (Dollars Américains).</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-card-footer">
                        <a href="{{ route('chef-projet.projets.index') }}" class="btn-cancel-custom">
                            <i class="fas fa-times me-2"></i> ANNULER
                        </a>
                        <button type="submit" class="btn-submit-custom">
                            <i class="fas fa-save me-2"></i> CRÉER LE PROJET
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
   CRÉATION PROJET - DESIGN PROFESSIONNEL ATTRACTIF
   ============================================ */

:root {
    --gradient-blue: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-green: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --gradient-orange: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-cyan: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-purple: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 8px 30px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.container {
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

/* Carte principale */
.form-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

.form-card:hover {
    box-shadow: var(--shadow-md);
}

/* En-tête de la carte */
.form-card-header {
    padding: 1.75rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 55px;
    height: 55px;
    background: var(--gradient-blue);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25);
}

.header-icon i {
    font-size: 1.8rem;
    color: white;
}

.header-text h5 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    background: var(--gradient-blue);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.header-text p {
    margin: 0.25rem 0 0;
    font-size: 0.75rem;
    color: #64748b;
}

/* Corps du formulaire */
.form-card-body {
    padding: 2rem;
}

.form-card-footer {
    padding: 1.25rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Labels */
.form-label-custom {
    display: block;
    margin-bottom: 0.6rem;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: #1e293b;
    text-transform: uppercase;
}

.text-danger {
    color: #f5576c;
}

/* Groupes d'input */
.input-group-custom {
    display: flex;
    align-items: center;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 16px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.input-group-custom:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.input-group-custom i {
    padding: 0 0 0 1.25rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 1rem;
}

.form-control-custom {
    flex: 1;
    padding: 0.9rem 1.25rem;
    border: none;
    background: transparent;
    font-size: 0.9rem;
    outline: none;
    color: #1e293b;
}

.form-control-custom::placeholder {
    color: #94a3b8;
    font-size: 0.85rem;
}

.form-control-custom.is-invalid {
    border-color: #f5576c;
}

textarea.form-control-custom {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
}

/* Message d'erreur */
.invalid-feedback-custom {
    color: #f5576c;
    font-size: 0.65rem;
    font-weight: 600;
    margin-top: 0.4rem;
    display: block;
}

/* Hint texte */
.form-hint {
    display: block;
    margin-top: 0.4rem;
    font-size: 0.65rem;
    font-weight: 500;
    background: linear-gradient(135deg, #64748b, #94a3b8);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Boîte d'information */
.info-box {
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border: 1px solid #fde68a;
    border-radius: 20px;
    padding: 1.25rem;
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    transition: all 0.3s;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.15);
}

.info-box i {
    font-size: 1.5rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.info-box strong {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: #92400e;
    text-transform: uppercase;
}

.info-box small {
    font-size: 0.7rem;
    color: #92400e;
    line-height: 1.4;
}

/* Boutons */
.btn-cancel-custom {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    color: #64748b;
    padding: 0.75rem 1.8rem;
    border-radius: 50px;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel-custom:hover {
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
    transform: translateY(-2px);
    color: #475569;
}

.btn-submit-custom {
    background: var(--gradient-green);
    color: white;
    border: none;
    padding: 0.75rem 1.8rem;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}

.btn-submit-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
}

/* Effet de focus sur les champs */
input:focus, textarea:focus {
    outline: none;
}

/* Animation des champs */
.form-control-custom {
    transition: all 0.2s;
}

/* Responsive */
@media (max-width: 768px) {
    .form-card-header {
        padding: 1.25rem;
        flex-direction: column;
        text-align: center;
    }
    
    .form-card-body {
        padding: 1.25rem;
    }
    
    .form-card-footer {
        padding: 1rem 1.25rem;
        flex-direction: column;
    }
    
    .btn-cancel-custom, .btn-submit-custom {
        width: 100%;
        padding: 0.7rem;
    }
    
    .input-group-custom i {
        padding-left: 0.75rem;
    }
    
    .form-control-custom {
        padding: 0.7rem 1rem;
    }
    
    .info-box {
        padding: 1rem;
    }
}

/* Animation d'entrée des champs */
.input-group-custom {
    animation: slideInRight 0.4s ease-out;
    animation-fill-mode: both;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(-15px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Délais d'animation pour chaque champ */
.input-group-custom:nth-child(1) { animation-delay: 0.05s; }
.input-group-custom:nth-child(2) { animation-delay: 0.1s; }
.input-group-custom:nth-child(3) { animation-delay: 0.15s; }
.input-group-custom:nth-child(4) { animation-delay: 0.2s; }
.input-group-custom:nth-child(5) { animation-delay: 0.25s; }
.input-group-custom:nth-child(6) { animation-delay: 0.3s; }
.input-group-custom:nth-child(7) { animation-delay: 0.35s; }

/* Style pour le champ invalide */
.input-group-custom:has(.is-invalid) {
    border-color: #f5576c;
    background: rgba(245, 87, 108, 0.02);
}
</style>
@endpush
@endsection