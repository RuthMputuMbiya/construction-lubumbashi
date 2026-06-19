{{-- resources/views/components/modals/upload-photo.blade.php --}}
@props(['id', 'action' => null, 'title' => 'Uploader une photo'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-upload">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Glissez-déposez une photo ici</p>
                            <p class="upload-hint">ou cliquez pour sélectionner</p>
                        </div>
                        <div class="upload-preview" id="uploadPreview" style="display: none;">
                            <img id="previewImage" alt="Aperçu">
                            <button type="button" class="remove-preview">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                    <input type="text" name="description" class="form-control mt-3" 
                           placeholder="Description de la photo (optionnel)">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-upload" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-submit-upload">Uploader</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const photoInput = document.getElementById('photoInput');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewImage = document.getElementById('previewImage');
    const uploadContent = document.querySelector('.upload-content');
    const removePreview = document.querySelector('.remove-preview');
    
    uploadArea.addEventListener('click', () => {
        photoInput.click();
    });
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleFile(file);
        }
    });
    
    photoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            handleFile(file);
        }
    });
    
    function handleFile(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            uploadContent.style.display = 'none';
            uploadPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
    
    if (removePreview) {
        removePreview.addEventListener('click', () => {
            uploadContent.style.display = 'block';
            uploadPreview.style.display = 'none';
            photoInput.value = '';
        });
    }
});
</script>

<style>
.modal-upload {
    border-radius: 20px;
    overflow: hidden;
}

.upload-area {
    border: 2px dashed #e5e7eb;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #f9fafb;
}

.upload-area:hover, .upload-area.dragover {
    border-color: #1a56db;
    background: #eff6ff;
}

.upload-content i {
    font-size: 3rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.upload-content p {
    margin: 0;
    color: #4b5563;
}

.upload-hint {
    font-size: 0.75rem;
    color: #9ca3af;
}

.upload-preview {
    position: relative;
}

.upload-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 10px;
}

.remove-preview {
    position: absolute;
    top: -10px;
    right: -10px;
    background: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    padding: 0;
}

.remove-preview i {
    font-size: 1.5rem;
    color: #ef4444;
}

.btn-cancel-upload {
    background: none;
    border: 1px solid #e5e7eb;
    padding: 8px 20px;
    border-radius: 50px;
    color: #6b7280;
}

.btn-submit-upload {
    background: linear-gradient(135deg, #1a56db, #0e3a8a);
    border: none;
    padding: 8px 25px;
    border-radius: 50px;
    color: white;
}
</style>