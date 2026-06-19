@extends('layouts.chef-chantier')

@section('title', 'Calendrier - Chef de Chantier')

@section('page-title', 'Calendrier des étapes')
@section('page-subtitle', 'Visualisation mensuelle de vos missions')

@section('content')
<div class="container-fluid px-0">
    
    {{-- ONGLETS DE NAVIGATION --}}
    <div class="planning-tabs mb-4">
        <div class="d-flex gap-2">
            <a href="{{ route('chef-chantier.planning.index') }}" class="tab-btn">
                <i class="fas fa-list me-2"></i> VUE LISTE
            </a>
            <a href="{{ route('chef-chantier.planning.calendar') }}" class="tab-btn active">
                <i class="fas fa-calendar-alt me-2"></i> VUE CALENDRIER
            </a>
        </div>
    </div>
    
    {{-- STATISTIQUES RAPIDES --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-simple">
                <div class="stat-icon-sm bg-primary-soft">
                    <i class="fas fa-tasks text-primary"></i>
                </div>
                <div>
                    <h4 class="stat-number">{{ $stats['total'] ?? 0 }}</h4>
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
                    <h4 class="stat-number text-success">{{ $stats['terminees'] ?? 0 }}</h4>
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
                    <h4 class="stat-number text-warning">{{ $stats['en_cours'] ?? 0 }}</h4>
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
                    <h4 class="stat-number text-danger">{{ $stats['en_retard'] ?? 0 }}</h4>
                    <p class="stat-text">En retard</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- CALENDRIER --}}
    <div class="calendar-container">
        <div id="calendar"></div>
    </div>
    
    {{-- LÉGENDE --}}
    <div class="calendar-legend mt-4">
        <div class="legend-title"><i class="fas fa-palette me-2"></i> LÉGENDE :</div>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-color success"></span>
                <span>Terminé</span>
            </div>
            <div class="legend-item">
                <span class="legend-color primary"></span>
                <span>En cours</span>
            </div>
            <div class="legend-item">
                <span class="legend-color danger"></span>
                <span>En retard</span>
            </div>
            <div class="legend-item">
                <span class="legend-color secondary"></span>
                <span>Non commencé</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #3b82f6;
    --primary-soft: #dbeafe;
    --success: #10b981;
    --danger: #ef4444;
    --dark: #1e293b;
    --gray: #64748b;
    --gray-light: #f8fafc;
    --gray-border: #e2e8f0;
    --white: #ffffff;
    --shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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

.calendar-container {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 20px;
    padding: 1.5rem;
}

.fc-toolbar-title {
    font-size: 1rem !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
}

.fc-button-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    border-radius: 10px !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
}

.fc-day-today {
    background: var(--primary-soft) !important;
}

.fc-event {
    cursor: pointer;
    border-radius: 8px;
    font-size: 0.7rem;
    padding: 4px 6px;
    border: none;
    font-weight: 600;
}

.calendar-legend {
    background: var(--white);
    border: 1px solid var(--gray-border);
    border-radius: 16px;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.legend-title {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
}

.legend-items {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--gray);
}

.legend-color {
    width: 14px;
    height: 14px;
    border-radius: 4px;
    display: inline-block;
}

.legend-color.success { background: var(--success); }
.legend-color.primary { background: var(--primary); }
.legend-color.danger { background: var(--danger); }
.legend-color.secondary { background: #8b5cf6; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: {!! $eventsJson !!},
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        height: 'auto',
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine'
        }
    });
    calendar.render();
});
</script>
@endpush
@endsection