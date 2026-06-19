{{-- resources/views/components/alerts/success.blade.php --}}
@props(['message', 'dismissible' => true])

<div {{ $attributes->merge(['class' => 'alert-modern alert-success']) }} 
     role="alert"
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 5000)">
    
    <div class="alert-content">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-message">
            <strong>Succès !</strong>
            <span>{{ $message }}</span>
        </div>
        @if($dismissible)
            <button type="button" class="alert-close" @click="show = false">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
    
    <div class="alert-progress"></div>
</div>

<style>
.alert-modern {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 350px;
    max-width: 450px;
    border-radius: 12px;
    overflow: hidden;
    animation: slideInRight 0.3s ease-out;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px 20px;
}

.alert-icon i {
    font-size: 1.5rem;
}

.alert-message {
    flex: 1;
}

.alert-message strong {
    display: block;
    font-size: 0.9rem;
    margin-bottom: 2px;
}

.alert-message span {
    font-size: 0.85rem;
    opacity: 0.95;
}

.alert-close {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    font-size: 1rem;
    opacity: 0.7;
    transition: opacity 0.3s;
}

.alert-close:hover {
    opacity: 1;
}

.alert-progress {
    height: 3px;
    background: rgba(255,255,255,0.3);
    animation: progress 5s linear forwards;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

@media (max-width: 768px) {
    .alert-modern {
        left: 20px;
        right: 20px;
        min-width: auto;
    }
}
</style>