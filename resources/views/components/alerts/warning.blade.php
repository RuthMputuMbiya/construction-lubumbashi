{{-- resources/views/components/alerts/warning.blade.php --}}
@props(['message', 'dismissible' => true])

<div {{ $attributes->merge(['class' => 'alert-modern alert-warning']) }} 
     role="alert"
     x-data="{ show: true }"
     x-show="show">
    
    <div class="alert-content">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-message">
            <strong>Attention !</strong>
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
.alert-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}
</style>