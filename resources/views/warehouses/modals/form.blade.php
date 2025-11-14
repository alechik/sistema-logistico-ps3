<div class="modal fade" id="warehouseModal" tabindex="-1" role="dialog" aria-labelledby="warehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warehouseModalLabel">Nuevo Almacén</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="warehouseForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" required>
                                <span class="invalid-feedback" id="name-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="code">Código *</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" required>
                                <span class="invalid-feedback" id="code-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="warehouse_type_id">Tipo de Almacén *</label>
                                <select class="form-control select2 @error('warehouse_type_id') is-invalid @enderror" 
                                        id="warehouse_type_id" name="warehouse_type_id" required>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach($warehouseTypes as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="warehouse_type_id-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="location">Ubicación</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location">
                                <span class="invalid-feedback" id="location-error"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Dirección</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3"></textarea>
                                <span class="invalid-feedback" id="address-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_phone">Teléfono de Contacto</label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                       id="contact_phone" name="contact_phone">
                                <span class="invalid-feedback" id="contact_phone-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_email">Email de Contacto</label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                       id="contact_email" name="contact_email">
                                <span class="invalid-feedback" id="contact_email-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" 
                                           name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Almacén activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notas</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                 id="notes" name="notes" rows="2"></textarea>
                        <span class="invalid-feedback" id="notes-error"></span>
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
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione una opción',
        dropdownParent: $('#warehouseModal')
    });
    
    // Form validation
    $("#warehouseForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            code: {
                required: true,
                minlength: 2,
                maxlength: 20
            },
            warehouse_type_id: {
                required: true
            },
            contact_email: {
                email: true
            }
        },
        messages: {
            name: {
                required: "Por favor ingrese el nombre del almacén",
                minlength: "El nombre debe tener al menos 3 caracteres",
                maxlength: "El nombre no puede tener más de 255 caracteres"
            },
            code: {
                required: "Por favor ingrese el código del almacén",
                minlength: "El código debe tener al menos 2 caracteres",
                maxlength: "El código no puede tener más de 20 caracteres"
            },
            warehouse_type_id: {
                required: "Por favor seleccione un tipo de almacén"
            },
            contact_email: {
                email: "Por favor ingrese un correo electrónico válido"
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
