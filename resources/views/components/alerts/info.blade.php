{{-- resources/views/components/alerts/info.blade.php --}}
@props(['message', 'dismissible' => true])

<div {{ $attributes->merge(['class' => 'alert-modern alert-info']) }} 
     role="alert"
     x-data="{ show: true }"
     x-show="show">
    
    <div class="alert-content">
        <div class="alert-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="alert-message">
            <strong>Information</strong>
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
.alert-info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}
</style>