<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Nueva Categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="categoryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" required>
                        <span class="invalid-feedback" id="name-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                 id="description" name="description" rows="3"></textarea>
                        <span class="invalid-feedback" id="description-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_id">Categoría Padre</label>
                        <select class="form-control select2 @error('parent_id') is-invalid @enderror" 
                                id="parent_id" name="parent_id">
                            <option value="">Sin categoría padre</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" id="parent_id-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Imagen</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Seleccionar archivo</label>
                        </div>
                        <span class="invalid-feedback d-block" id="image-error"></span>
                        <small class="form-text text-muted">Tamaño máximo: 2MB. Formatos: jpg, jpeg, png, gif</small>
                        
                        <div id="current-image" class="mt-2">
                            <!-- Current image will be shown here when editing -->
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Categoría activa</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show selected file name
    $("#image").on("change", function() {
        const fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").html(fileName || "Seleccionar archivo");
    });
    
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione una opción',
        dropdownParent: $('#categoryModal')
    });
    
    // Form validation
    $("#categoryForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            description: {
                maxlength: 500
            }
        },
        messages: {
            name: {
                required: "Por favor ingrese el nombre de la categoría",
                minlength: "El nombre debe tener al menos 3 caracteres",
                maxlength: "El nombre no puede tener más de 255 caracteres"
            },
            description: {
                maxlength: "La descripción no puede tener más de 500 caracteres"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>
@endpush
