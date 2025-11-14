@csrf

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="code">Código *</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" 
                   value="{{ old('code', $product->code ?? '') }}" required>
            @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="name">Nombre *</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                   value="{{ old('name', $product->name ?? '') }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="product_category_id">Categoría *</label>
            <select class="form-control select2 @error('product_category_id') is-invalid @enderror" 
                    id="product_category_id" name="product_category_id" required>
                <option value="">Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ (old('product_category_id', $product->product_category_id ?? '') == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('product_category_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="purchase_price">Precio de compra *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">S/</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control @error('purchase_price') is-invalid @enderror" 
                               id="purchase_price" name="purchase_price" 
                               value="{{ old('purchase_price', $product->purchase_price ?? '0.00') }}" required>
                    </div>
                    @error('purchase_price')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sale_price">Precio de venta *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">S/</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control @error('sale_price') is-invalid @enderror" 
                               id="sale_price" name="sale_price" 
                               value="{{ old('sale_price', $product->sale_price ?? '0.00') }}" required>
                    </div>
                    @error('sale_price')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                      name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="image">Imagen del producto</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                <label class="custom-file-label" for="image">Seleccionar archivo</label>
            </div>
            @error('image')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            
            @if(isset($product) && $product->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Imagen actual" 
                         class="img-thumbnail" style="max-height: 100px;">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                        <label class="form-check-label" for="remove_image">
                            Eliminar imagen actual
                        </label>
                    </div>
                </div>
            @endif
            <small class="form-text text-muted">Tamaño máximo: 2MB. Formatos: jpg, jpeg, png, gif</small>
        </div>
        
        <div class="form-group">
            <label for="barcode">Código de barras</label>
            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                   id="barcode" name="barcode" 
                   value="{{ old('barcode', $product->barcode ?? '') }}">
            @error('barcode')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="unit">Unidad de medida *</label>
            <select class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                @foreach(['UNIDAD', 'KILO', 'LITRO', 'METRO', 'PAQUETE', 'OTRO'] as $unit)
                    <option value="{{ $unit }}" 
                        {{ (old('unit', $product->unit ?? 'UNIDAD') == $unit) ? 'selected' : '' }}>
                        {{ $unit }}
                    </option>
                @endforeach
            </select>
            @error('unit')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                       value="1" {{ old('is_active', isset($product) ? $product->is_active : true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">Producto activo</label>
            </div>
        </div>
    </div>
</div>

<div class="form-group mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Guardar
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Cancelar
    </a>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Mostrar nombre del archivo seleccionado
    $("#image").on("change", function() {
        const fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").html(fileName || "Seleccionar archivo");
    });
    
    // Inicializar select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione una opción'
    });
});
</script>
@endpush
