{{-- resources/views/components/modals/confirm-delete.blade.php --}}
@props(['id', 'title' => 'Confirmer la suppression', 'message' => 'Êtes-vous sûr de vouloir supprimer cet élément ?', 'action' => null])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-delete">
            <div class="modal-header border-0">
                <div class="modal-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h4 class="modal-title">{{ $title }}</h4>
                <p class="modal-message">{{ $message }}</p>
                <p class="modal-warning">
                    <i class="fas fa-exclamation-triangle"></i> Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <form method="POST" action="{{ $action }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">
                        <i class="fas fa-check"></i> Confirmer la suppression
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.modal-delete {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}

.modal-header {
    padding: 1.5rem 1.5rem 0;
    position: relative;
}

.modal-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.modal-icon i {
    font-size: 2rem;
    color: #dc2626;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.modal-message {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.modal-warning {
    background: #fef3c7;
    color: #d97706;
    padding: 10px;
    border-radius: 10px;
    font-size: 0.8rem;
    margin-top: 1rem;
}

.btn-cancel {
    background: none;
    border: 1px solid #e5e7eb;
    padding: 10px 25px;
    border-radius: 50px;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-confirm-delete {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
    padding: 10px 25px;
    border-radius: 50px;
    color: white;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-confirm-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(239,68,68,0.3);
}
</style>