{{--
    ======================================================
    VUE : CRÉATION D'UNE PLANIFICATION
    ======================================================
    DESCRIPTION:
        - Formulaire pour créer une nouvelle planification
        - Saisie des informations générales du projet
        - Sélection de la devise (CDF, USD, EURO)
        - Calcul automatique de la durée
        - Assignation du chef de chantier
        - Information sur les 5 étapes obligatoires
    
    AUTEUR: ConstrucTrack
    DATE: 2025
    ======================================================
--}}

@extends('layouts.chef-projet')

@section('title', 'Nouvelle planification - Chef de Projet')

@section('page-title', 'Créer une planification')
@section('page-subtitle', 'Planifiez votre projet avant sa création')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                {{-- EN-TÊTE DU FORMULAIRE --}}
                <div class="form-card-header">
                    <div class="header-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="header-text">
                        <h5 class="mb-0">NOUVELLE PLANIFICATION</h5>
                        <p class="text-muted small mb-0 mt-1">Remplissez les informations suivantes pour planifier votre projet</p>
                    </div>
                    <div class="header-badge">
                        <span class="badge-planification">Étape 1/3</span>
                    </div>
                </div>
                
                {{-- FORMULAIRE DE PLANIFICATION --}}
                <form method="POST" action="{{ route('chef-projet.planification.store') }}" id="planificationForm">
                    @csrf
                    
                    <div class="form-card-body">
                        {{-- ========================================== --}}
                        {{-- SECTION 1: INFORMATIONS GÉNÉRALES --}}
                        {{-- ========================================== --}}
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i>
                            <span>INFORMATIONS GÉNÉRALES</span>
                            <div class="section-line"></div>
                        </div>
                        
                        <div class="row g-4">
                            {{-- Nom du projet --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">NOM DU PROJET <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" name="nom_projet" class="form-control-custom @error('nom_projet') is-invalid @enderror" 
                                           value="{{ old('nom_projet') }}" required placeholder="Ex: Construction Immeuble Kipushi">
                                </div>
                                @error('nom_projet')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Lieu du projet --}}
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
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- SECTION 2: RESPONSABLES & TYPE --}}
                        {{-- ========================================== --}}
                        <div class="section-title mt-4">
                            <i class="fas fa-users"></i>
                            <span>RESPONSABLES & TYPE</span>
                            <div class="section-line"></div>
                        </div>
                        
                        <div class="row g-4">
                            {{-- Responsable du projet (Chef de projet) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">RESPONSABLE DU PROJET <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-user-tie"></i>
                                    <input type="text" name="responsable" class="form-control-custom @error('responsable') is-invalid @enderror" 
                                           value="{{ old('responsable', Auth::user()->nom) }}" required placeholder="Nom du responsable">
                                </div>
                                <small class="form-hint">Le chef de projet qui supervise cette planification</small>
                                @error('responsable')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Type de projet (Bâtiment, Route, Pont, Autre) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">TYPE DE PROJET <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-building"></i>
                                    <select name="type_projet" class="form-control-custom @error('type_projet') is-invalid @enderror" required>
                                        <option value="batiment" {{ old('type_projet') == 'batiment' ? 'selected' : '' }}>🏢 Bâtiment</option>
                                        <option value="route" {{ old('type_projet') == 'route' ? 'selected' : '' }}>🛣️ Route</option>
                                        <option value="pont" {{ old('type_projet') == 'pont' ? 'selected' : '' }}>🌉 Pont</option>
                                        <option value="autre" {{ old('type_projet') == 'autre' ? 'selected' : '' }}>📐 Autre</option>
                                    </select>
                                </div>
                                @error('type_projet')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row g-4">
                            {{-- Priorité du projet (Haute, Moyenne, Basse) --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">PRIORITÉ</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-flag"></i>
                                    <select name="priorite" class="form-control-custom">
                                        <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>🔴 Haute</option>
                                        <option value="moyenne" {{ old('priorite') == 'moyenne' ? 'selected' : '' }}>🟡 Moyenne</option>
                                        <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>🟢 Basse</option>
                                    </select>
                                </div>
                                @error('priorite')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Chef de chantier assigné --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">CHEF DE CHANTIER <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-hard-hat"></i>
                                    <select name="id_chef_chantier" class="form-control-custom @error('id_chef_chantier') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner un chef de chantier --</option>
                                        @foreach($chefsChantier as $chef)
                                            <option value="{{ $chef->id }}" {{ old('id_chef_chantier') == $chef->id ? 'selected' : '' }}>
                                                👷 {{ $chef->nom }} ({{ $chef->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="form-hint">Le chef de chantier qui exécutera les travaux</small>
                                @error('id_chef_chantier')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- SECTION 3: CALENDRIER & BUDGET --}}
                        {{-- ========================================== --}}
                        <div class="section-title mt-4">
                            <i class="fas fa-calendar-alt"></i>
                            <span>CALENDRIER & BUDGET</span>
                            <div class="section-line"></div>
                        </div>
                        
                        <div class="row g-4">
                            {{-- Date de début prévue --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">DATE DE DÉBUT PRÉVUE <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" name="date_debut_prevue" id="date_debut" 
                                           class="form-control-custom @error('date_debut_prevue') is-invalid @enderror" 
                                           value="{{ old('date_debut_prevue') }}" required>
                                </div>
                                @error('date_debut_prevue')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Date de fin prévue --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">DATE DE FIN PRÉVUE <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-flag-checkered"></i>
                                    <input type="date" name="date_fin_prevue" id="date_fin" 
                                           class="form-control-custom @error('date_fin_prevue') is-invalid @enderror" 
                                           value="{{ old('date_fin_prevue') }}" required>
                                </div>
                                @error('date_fin_prevue')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Durée estimée (calcul automatique) --}}
                            <div class="col-md-6">
                                <div class="duration-box">
                                    <i class="fas fa-hourglass-half"></i>
                                    <span>DURÉE ESTIMÉE : </span>
                                    <strong id="duree_estimee">0 jour(s)</strong>
                                </div>
                            </div>
                            
                            {{-- SECTION BUDGET AVEC SÉLECTION DE DEVISE --}}
                            <div class="col-md-6">
                                <label class="form-label-custom">BUDGET PRÉVISIONNEL <span class="text-danger">*</span></label>
                                <div class="budget-group">
                                    <div class="input-group-custom budget-input">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <input type="number" name="budget_previsionnel" id="budget_previsionnel" 
                                               class="form-control-custom @error('budget_previsionnel') is-invalid @enderror" 
                                               value="{{ old('budget_previsionnel') }}" required 
                                               placeholder="Ex: 10000000" step="1000">
                                    </div>
                                    <select name="devise" id="devise" class="devise-select">
                                        <option value="CDF" {{ old('devise') == 'CDF' ? 'selected' : '' }}>🇨🇩 CDF (Franc Congolais)</option>
                                        <option value="USD" {{ old('devise') == 'USD' ? 'selected' : '' }}>🇺🇸 USD (Dollar Américain)</option>
                                        <option value="EUR" {{ old('devise') == 'EUR' ? 'selected' : '' }}>🇪🇺 EUR (Euro)</option>
                                    </select>
                                </div>
                                <small class="form-hint">Choisissez la devise et entrez le montant</small>
                                @error('budget_previsionnel')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- SECTION 4: DESCRIPTION --}}
                        {{-- ========================================== --}}
                        <div class="section-title mt-4">
                            <i class="fas fa-align-left"></i>
                            <span>DESCRIPTION</span>
                            <div class="section-line"></div>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label-custom">DESCRIPTION <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <i class="fas fa-align-left"></i>
                                    <textarea name="description" class="form-control-custom @error('description') is-invalid @enderror" 
                                              rows="5" required placeholder="Décrivez votre projet en détail...">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- ========================================== --}}
                        {{-- SECTION 5: INFORMATION IMPORTANTE --}}
                        {{-- ========================================== --}}
                        <div class="info-box mt-4">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>📋 INFORMATION IMPORTANTE</strong><br>
                                <small>Après la création de la planification, vous devrez ajouter les <strong>5 étapes obligatoires</strong> :</small>
                                <div class="etapes-list-small mt-2">
                                    <span class="etape-step">01</span> ANALYSE DES BESOINS →
                                    <span class="etape-step">02</span> ÉTUDE DU SITE →
                                    <span class="etape-step">03</span> ÉTUDE DE FAISABILITÉ →
                                    <span class="etape-step">04</span> CONCEPTION →
                                    <span class="etape-step">05</span> VALIDATION
                                </div>
                                <small class="mt-2 d-block">✅ Une fois les 5 étapes planifiées et assignées, vous pourrez <strong>transformer en projet réel</strong>.</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- BOUTONS D'ACTION --}}
                    <div class="form-card-footer">
                        <a href="{{ route('chef-projet.planification.index') }}" class="btn-cancel-custom">
                            <i class="fas fa-times me-2"></i> ANNULER
                        </a>
                        <button type="submit" class="btn-submit-custom">
                            <i class="fas fa-save me-2"></i> CRÉER LA PLANIFICATION
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ======================================================
   STYLES POUR LE FORMULAIRE DE PLANIFICATION
   ====================================================== */
.form-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.form-card-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc, #ffffff);
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.header-icon {
    width: 55px;
    height: 55px;
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 1.5rem;
    color: white;
}

.header-text {
    flex: 1;
}

.header-text h5 {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: #1e293b;
    text-transform: uppercase;
    margin: 0;
}

.header-text p {
    margin: 0.25rem 0 0;
    font-size: 0.75rem;
    color: #64748b;
}

.header-badge .badge-planification {
    background: #d1fae5;
    color: #059669;
    padding: 0.4rem 1rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.form-card-body {
    padding: 1.5rem;
}

.form-card-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Sections */
.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.section-title i {
    font-size: 1rem;
    color: #1a56db;
}

.section-title span {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: #1e293b;
    text-transform: uppercase;
}

.section-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, #e2e8f0, transparent);
}

/* Champs de formulaire */
.form-label-custom {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    color: #1e293b;
    text-transform: uppercase;
}

.input-group-custom {
    display: flex;
    align-items: center;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.3s;
    background: white;
}

.input-group-custom:focus-within {
    border-color: #1a56db;
    box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
}

.input-group-custom i {
    padding: 0 0 0 1rem;
    color: #94a3b8;
    width: 40px;
}

.form-control-custom {
    flex: 1;
    padding: 0.8rem 1rem;
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.9rem;
}

select.form-control-custom {
    cursor: pointer;
}

textarea.form-control-custom {
    resize: vertical;
    min-height: 100px;
}

/* Groupe budget avec devise */
.budget-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.budget-group .budget-input {
    flex: 2;
}

.devise-select {
    flex: 1;
    padding: 0.8rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.devise-select:focus {
    outline: none;
    border-color: #1a56db;
    box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
}

.invalid-feedback-custom {
    color: #dc2626;
    font-size: 0.7rem;
    margin-top: 0.3rem;
    display: block;
}

.form-hint {
    display: block;
    margin-top: 0.3rem;
    font-size: 0.65rem;
    color: #64748b;
}

/* Boîte durée */
.duration-box {
    background: #f1f5f9;
    border-radius: 12px;
    padding: 0.8rem 1rem;
    margin-top: 0.5rem;
}

.duration-box i {
    color: #1a56db;
    margin-right: 0.5rem;
}

.duration-box strong {
    color: #1a56db;
    font-size: 1rem;
}

/* Boîte info */
.info-box {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    gap: 1rem;
}

.info-box i {
    font-size: 1.5rem;
    color: #d97706;
}

.info-box strong {
    font-size: 0.75rem;
    font-weight: 800;
    color: #92400e;
    text-transform: uppercase;
}

.info-box small {
    font-size: 0.7rem;
    color: #92400e;
    line-height: 1.4;
}

.etapes-list-small {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.7rem;
    color: #92400e;
}

.etape-step {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: #d97706;
    color: white;
    border-radius: 50%;
    font-size: 0.65rem;
    font-weight: 800;
}

/* Boutons */
.btn-cancel-custom {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 0.7rem 1.5rem;
    border-radius: 40px;
    color: #64748b;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.3s;
}

.btn-cancel-custom:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

.btn-submit-custom {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    padding: 0.7rem 1.8rem;
    border-radius: 40px;
    color: white;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.btn-submit-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .form-card-header {
        flex-direction: column;
        text-align: center;
    }
    
    .form-card-footer {
        flex-direction: column;
    }
    
    .btn-cancel-custom, .btn-submit-custom {
        text-align: center;
    }
    
    .budget-group {
        flex-direction: column;
    }
    
    .budget-group .budget-input,
    .devise-select {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // ======================================================
    // SCRIPT: CALCUL AUTOMATIQUE DE LA DURÉE
    // ======================================================
    // Récupération des éléments du DOM
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const dureeSpan = document.getElementById('duree_estimee');
    
    /**
     * Calcule la durée entre date de début et date de fin
     * Met à jour l'affichage de la durée estimée
     */
    function calculerDuree() {
        if (dateDebut.value && dateFin.value) {
            const debut = new Date(dateDebut.value);
            const fin = new Date(dateFin.value);
            const diffTime = Math.abs(fin - debut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            dureeSpan.textContent = diffDays + ' jour(s)';
        }
    }
    
    // Écouteurs d'événements sur les dates
    dateDebut.addEventListener('change', calculerDuree);
    dateFin.addEventListener('change', calculerDuree);
</script>
@endpush
@endsection