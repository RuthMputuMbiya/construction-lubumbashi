{{-- resources/views/components/alerts/error.blade.php --}}
@props(['message', 'dismissible' => true])

<div {{ $attributes->merge(['class' => 'alert-modern alert-error']) }} 
     role="alert"
     x-data="{ show: true }"
     x-show="show">
    
    <div class="alert-content">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-message">
            <strong>Erreur !</strong>
            <span>{{ $message }}</span>
        </div>
        @if($dismissible)
            <button type="button" class="alert-close" @click="show = false">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div>

<style>
.alert-error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}
</style>