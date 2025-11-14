<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Código *</label>
                                <input type="text" class="form-control" id="code" name="code" required>
                                <span class="invalid-feedback" id="code-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Nombre *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <span class="invalid-feedback" id="name-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_category_id">Categoría *</label>
                                <select class="form-control select2" id="product_category_id" name="product_category_id" required>
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="product_category_id-error"></span>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_price">Precio de compra *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S/</span>
                                            </div>
                                            <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" min="0" required>
                                        </div>
                                        <span class="invalid-feedback" id="purchase_price-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sale_price">Precio de venta *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S/</span>
                                            </div>
                                            <input type="number" class="form-control" id="sale_price" name="sale_price" step="0.01" min="0" required>
                                        </div>
                                        <span class="invalid-feedback" id="sale_price-error"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Stock inicial</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" value="0">
                                <span class="invalid-feedback" id="stock-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="min_stock">Stock mínimo</label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" min="0" value="0">
                                <span class="invalid-feedback" id="min_stock-error"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                <span class="invalid-feedback" id="description-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Imagen del producto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                    <label class="custom-file-label" for="image">Seleccionar archivo</label>
                                </div>
                                <span class="invalid-feedback" id="image-error"></span>
                                <div id="current-image" class="mt-2"></div>
                                <small class="form-text text-muted">Tamaño máximo: 2MB. Formatos: jpg, jpeg, png, gif</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="barcode">Código de barras</label>
                                <input type="text" class="form-control" id="barcode" name="barcode">
                                <span class="invalid-feedback" id="barcode-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="unit">Unidad de medida</label>
                                <select class="form-control" id="unit" name="unit">
                                    <option value="UNIDAD">UNIDAD</option>
                                    <option value="KILO">KILO</option>
                                    <option value="LITRO">LITRO</option>
                                    <option value="METRO">METRO</option>
                                    <option value="PAQUETE">PAQUETE</option>
                                    <option value="OTRO">OTRO</option>
                                </select>
                                <span class="invalid-feedback" id="unit-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Producto activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione una opción'
    });
    
    // Mostrar nombre del archivo seleccionado
    $("#image").on("change", function() {
        const fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").html(fileName || "Seleccionar archivo");
    });
    
    // Validación del formulario
    $("#productForm").validate({
        rules: {
            code: {
                required: true,
                minlength: 3,
                maxlength: 50
            },
            name: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            product_category_id: {
                required: true
            },
            purchase_price: {
                required: true,
                min: 0
            },
            sale_price: {
                required: true,
                min: 0
            },
            stock: {
                min: 0
            },
            min_stock: {
                min: 0
            },
            barcode: {
                maxlength: 50
            }
        },
        messages: {
            code: {
                required: "Por favor ingrese el código del producto",
                minlength: "El código debe tener al menos 3 caracteres",
                maxlength: "El código no puede tener más de 50 caracteres"
            },
            name: {
                required: "Por favor ingrese el nombre del producto",
                minlength: "El nombre debe tener al menos 3 caracteres",
                maxlength: "El nombre no puede tener más de 255 caracteres"
            },
            product_category_id: {
                required: "Por favor seleccione una categoría"
            },
            purchase_price: {
                required: "Por favor ingrese el precio de compra",
                min: "El precio no puede ser negativo"
            },
            sale_price: {
                required: "Por favor ingrese el precio de venta",
                min: "El precio no puede ser negativo"
            },
            stock: {
                min: "El stock no puede ser negativo"
            },
            min_stock: {
                min: "El stock mínimo no puede ser negativo"
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
