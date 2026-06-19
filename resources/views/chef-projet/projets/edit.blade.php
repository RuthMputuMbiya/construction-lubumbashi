{{-- resources/views/chef-projet/projets/edit.blade.php --}}
@extends('layouts.chef-projet')

@section('title', 'Modifier le projet - Chef de Projet')

@section('page-title', 'Modifier le projet')
@section('page-subtitle', $projet->nom)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-card-header">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="header-text">
                        <h5 class="mb-0">MODIFIER LE PROJET</h5>
                        <p class="text-muted small mb-0 mt-1">Mettez à jour les informations de votre projet</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('chef-projet.projets.update', $projet->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-card-body">
                        <div class="row g-4">
                            {{-- Nom du projet --}}
                            <div class="col-12">
                                <label class="form-label-custom">NOM DU PROJET <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" name="nom" class="form-control-custom @error('nom') is-invalid @enderror" 
                                           value="{{ old('nom', $projet->nom) }}" required placeholder="Ex: Construction Immeuble Kipushi">
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
                                           value="{{ old('lieu', $projet->lieu) }}" required placeholder="Ex: Lubumbashi, Quartier Golf">
                                </div>
                                @error('lieu')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- BUDGET EN FRANCS CONGOLAIS (FC) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">BUDGET (FC) <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <input type="number" name="budget_total_fc" class="form-control-custom @error('budget_total_fc') is-invalid @enderror" 
                                           value="{{ old('budget_total_fc', $projet->budget_total_fc) }}" required step="1000" placeholder="Ex: 10000000">
                                </div>
                                @error('budget_total_fc')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Date de fin prévue --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">DATE DE FIN PRÉVUE <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" name="date_fin_prevue" class="form-control-custom @error('date_fin_prevue') is-invalid @enderror" 
                                           value="{{ old('date_fin_prevue', $projet->date_fin_prevue instanceof \Carbon\Carbon ? $projet->date_fin_prevue->format('Y-m-d') : $projet->date_fin_prevue) }}" required>
                                </div>
                                @error('date_fin_prevue')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- BUDGET EN DOLLARS USD --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">BUDGET (USD)</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-dollar-sign"></i>
                                    <input type="number" name="budget_total_usd" class="form-control-custom" 
                                           value="{{ old('budget_total_usd', $projet->budget_total_usd) }}" placeholder="Ex: 50000" step="100">
                                </div>
                                <small class="form-hint">Montant en Dollars Américains (optionnel)</small>
                                @error('budget_total_usd')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label-custom">DESCRIPTION <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-align-left"></i>
                                    <textarea name="description" class="form-control-custom @error('description') is-invalid @enderror" 
                                              rows="5" required placeholder="Décrivez votre projet...">{{ old('description', $projet->description) }}</textarea>
                                </div>
                                @error('description')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- Informations non modifiables --}}
                        <div class="info-box mt-4">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>INFORMATION</strong><br>
                                <small>Le code projet, la date de début, la date de création et le statut ne peuvent pas être modifiés.</small>
                            </div>
                        </div>
                        
                        {{-- Informations supplémentaires du projet --}}
                        <div class="project-meta mt-4">
                            <div class="meta-header">
                                <i class="fas fa-chart-simple"></i>
                                <span>INFORMATIONS SUPPLÉMENTAIRES</span>
                            </div>
                            <div class="meta-grid">
                                <div class="meta-item">
                                    <span class="meta-label">Code projet</span>
                                    <span class="meta-value">{{ $projet->code_projet ?? 'PRJ-'.str_pad($projet->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Date de début prévue</span>
                                    <span class="meta-value">{{ \Carbon\Carbon::parse($projet->date_debut_prevue)->format('d/m/Y') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Statut actuel</span>
                                    <span class="meta-value">
                                        @if($projet->statut == 'en_cours')
                                            <span class="badge-status success">En cours</span>
                                        @elseif($projet->statut == 'termine')
                                            <span class="badge-status success">Terminé</span>
                                        @elseif($projet->statut == 'annule')
                                            <span class="badge-status danger">Annulé</span>
                                        @else
                                            <span class="badge-status warning">En attente</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Créé le</span>
                                    <span class="meta-value">{{ $projet->created_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-card-footer">
                        <a href="{{ route('chef-projet.projets.show', $projet->id) }}" class="btn-cancel-custom">
                            <i class="fas fa-times me-2"></i> ANNULER
                        </a>
                        <button type="submit" class="btn-submit-custom">
                            <i class="fas fa-save me-2"></i> ENREGISTRER
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
   MODIFICATION PROJET - DESIGN PROFESSIONNEL ATTRACTIF
   ============================================ */

:root {
    --gradient-blue: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-green: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --gradient-orange: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-cyan: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-yellow: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
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
    background: var(--gradient-orange);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(240, 147, 251, 0.25);
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
    background: var(--gradient-orange);
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
    border-color: #f093fb;
    box-shadow: 0 0 0 4px rgba(240, 147, 251, 0.1);
}

.input-group-custom i {
    padding: 0 0 0 1.25rem;
    background: var(--gradient-orange);
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
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 20px;
    padding: 1.25rem;
    display: flex;
    gap: 1rem;
    transition: all 0.3s;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
}

.info-box i {
    font-size: 1.5rem;
    background: var(--gradient-blue);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.info-box strong {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: #1e40af;
    text-transform: uppercase;
}

.info-box small {
    font-size: 0.7rem;
    color: #1e40af;
    line-height: 1.4;
}

/* Métadonnées du projet */
.project-meta {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 20px;
    padding: 1.25rem;
    margin-top: 1rem;
    border: 1px solid #e2e8f0;
}

.meta-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.meta-header i {
    background: var(--gradient-cyan);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 1rem;
}

.meta-header span {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: #64748b;
    text-transform: uppercase;
}

.meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.meta-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meta-value {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
}

/* Badges de statut */
.badge-status {
    display: inline-block;
    padding: 0.2rem 0.7rem;
    border-radius: 40px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.badge-status.success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.badge-status.danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.badge-status.warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
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

/* Animation des champs */
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

/* Délais d'animation */
.input-group-custom:nth-child(1) { animation-delay: 0.05s; }
.input-group-custom:nth-child(2) { animation-delay: 0.1s; }
.input-group-custom:nth-child(3) { animation-delay: 0.15s; }
.input-group-custom:nth-child(4) { animation-delay: 0.2s; }
.input-group-custom:nth-child(5) { animation-delay: 0.25s; }
.input-group-custom:nth-child(6) { animation-delay: 0.3s; }

/* Style pour le champ invalide */
.input-group-custom:has(.is-invalid) {
    border-color: #f5576c;
    background: rgba(245, 87, 108, 0.02);
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
    
    .info-box, .project-meta {
        padding: 1rem;
    }
    
    .meta-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .meta-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>
@endpush
@endsection