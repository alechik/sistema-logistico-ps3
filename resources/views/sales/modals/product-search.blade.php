<div class="modal fade" id="product-search-modal" tabindex="-1" role="dialog" aria-labelledby="productSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productSearchModalLabel">Buscar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="product-search" 
                                   placeholder="Buscar por código o nombre...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="search-product">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="show-only-available" checked>
                            <label class="form-check-label" for="show-only-available">Mostrar solo productos con stock</label>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="products-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let productCounter = 0;
    let addedProducts = [];
    
    // Search products
    $('#search-product').click(function() {
        searchProducts();
    });
    
    // Search on Enter key
    $('#product-search').keypress(function(e) {
        if (e.which === 13) {
            searchProducts();
            return false;
        }
    });
    
    function searchProducts() {
        const searchTerm = $('#product-search').val();
        const warehouseId = $('#warehouse_id').val();
        const showOnlyAvailable = $('#show-only-available').is(':checked');
        
        if (!warehouseId) {
            showError('Por favor seleccione un almacén primero');
            return;
        }
        
        $.get('{{ route("products.search") }}', {
            q: searchTerm,
            warehouse_id: warehouseId,
            available_only: showOnlyAvailable ? 1 : 0
        }, function(response) {
            let html = '';
            
            if (response.length === 0) {
                html = `
                <tr>
                    <td colspan="5" class="text-center">No se encontraron productos</td>
                </tr>`;
            } else {
                response.forEach(function(product) {
                    const isAdded = addedProducts.includes(product.id);
                    const disabled = isAdded ? 'disabled' : '';
                    const stockClass = product.stock <= 0 ? 'text-danger' : '';
                    
                    html += `
                    <tr>
                        <td>${product.code}</td>
                        <td>${product.name}</td>
                        <td class="text-right">S/ ${parseFloat(product.sale_price).toFixed(2)}</td>
                        <td class="text-right ${stockClass}">${parseFloat(product.stock).toFixed(2)} ${product.unit || ''}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary add-product-btn" 
                                    data-id="${product.id}" 
                                    data-code="${product.code}" 
                                    data-name="${product.name}" 
                                    data-price="${product.sale_price}" 
                                    data-stock="${product.stock}"
                                    data-unit="${product.unit || ''}"
                                    ${disabled}>
                                <i class="fas ${isAdded ? 'fa-check' : 'fa-plus'}"></i> ${isAdded ? 'Agregado' : 'Agregar'}
                            </button>
                        </td>
                    </tr>`;
                });
            }
            
            $('#products-table tbody').html(html);
        });
    }
    
    // Add product to sale
    $(document).on('click', '.add-product-btn', function() {
        const productId = $(this).data('id');
        const productCode = $(this).data('code');
        const productName = $(this).data('name');
        const productPrice = parseFloat($(this).data('price'));
        const productStock = parseFloat($(this).data('stock'));
        const productUnit = $(this).data('unit');
        
        // Add to added products array
        if (!addedProducts.includes(productId)) {
            addedProducts.push(productId);
            
            // Generate unique ID for the product row
            const rowId = 'product_' + productCounter++;
            
            // Add row to sale items table
            const row = `
            <tr class="sale-item" id="${rowId}" data-id="${rowId}" data-product-id="${productId}">
                <td>
                    ${productName}
                    <input type="hidden" name="product_id[]" value="${productId}">
                    <div class="text-muted">${productCode}</div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" class="form-control quantity" 
                               id="quantity_${rowId}" 
                               name="quantity[]" 
                               value="1" 
                               min="0.01" 
                               step="0.01" 
                               data-stock="${productStock}"
                               required>
                        <div class="input-group-append">
                            <span class="input-group-text">${productUnit || 'u'}</span>
                        </div>
                    </div>
                    <small class="text-danger stock-error" style="display: none;">Stock insuficiente</small>
                </td>
                <td>
                    <input type="number" class="form-control price" 
                           id="price_${rowId}" 
                           name="price[]" 
                           value="${productPrice.toFixed(2)}" 
                           min="0" 
                           step="0.01" 
                           required>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" class="form-control discount" 
                               id="discount_${rowId}" 
                               name="discount[]" 
                               value="0" 
                               min="0" 
                               step="0.01">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
                <td class="text-right subtotal">S/ ${productPrice.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-product">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            
            $('#sale-items').append(row);
            
            // Update the add button in the search modal
            $(this).html('<i class="fas fa-check"></i> Agregado').prop('disabled', true);
            
            // Recalculate totals
            calculateTotals();
        }
    });
    
    // Remove product from sale
    $(document).on('click', '.remove-product', function() {
        const row = $(this).closest('tr');
        const productId = row.data('product-id');
        
        // Remove from added products array
        addedProducts = addedProducts.filter(id => id !== productId);
        
        // Remove the row
        row.remove();
        
        // Update the add button in the search modal
        $(`.add-product-btn[data-id="${productId}"]`)
            .html('<i class="fas fa-plus"></i> Agregar')
            .prop('disabled', false);
        
        // Recalculate totals
        calculateTotals();
    });
    
    // Quantity change handler
    $(document).on('input', '.quantity', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat($(this).val()) || 0;
        const stock = parseFloat($(this).data('stock'));
        
        if (quantity > stock) {
            row.find('.stock-error').show();
            $(this).addClass('is-invalid');
        } else {
            row.find('.stock-error').hide();
            $(this).removeClass('is-invalid');
        }
        
        calculateRowTotal(row);
        calculateTotals();
    });
    
    // Price change handler
    $(document).on('input', '.price', function() {
        const row = $(this).closest('tr');
        calculateRowTotal(row);
        calculateTotals();
    });
    
    // Discount change handler
    $(document).on('input', '.discount', function() {
        const row = $(this).closest('tr');
        calculateRowTotal(row);
        calculateTotals();
    });
    
    // Calculate row subtotal
    function calculateRowTotal(row) {
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const price = parseFloat(row.find('.price').val()) || 0;
        const discount = parseFloat(row.find('.discount').val()) || 0;
        
        const subtotal = quantity * price;
        const discountAmount = subtotal * (discount / 100);
        const total = subtotal - discountAmount;
        
        row.find('.subtotal').text('S/ ' + total.toFixed(2));
    }
    
    // Calculate all totals
    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        
        $('.sale-item').each(function() {
            const quantity = parseFloat($(this).find('.quantity').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            const discount = parseFloat($(this).find('.discount').val()) || 0;
            
            const rowSubtotal = quantity * price;
            const rowDiscount = rowSubtotal * (discount / 100);
            
            subtotal += rowSubtotal;
            totalDiscount += rowDiscount;
        });
        
        const tax = subtotal * 0.18; // 18% IGV
        const total = subtotal + tax - totalDiscount;
        
        // Update summary
        $('#subtotal').text('S/ ' + subtotal.toFixed(2));
        $('#tax').text('S/ ' + tax.toFixed(2));
        $('#discount').text('S/ ' + totalDiscount.toFixed(2));
        $('#total').text('S/ ' + total.toFixed(2));
        
        // Update hidden inputs
        $('#subtotal-input').val(subtotal.toFixed(2));
        $('#tax-input').val(tax.toFixed(2));
        $('#discount-input').val(totalDiscount.toFixed(2));
        $('#total-input').val(total.toFixed(2));
        
        // Update change
        calculateChange();
    }
    
    // Close modal when clicking outside
    $('.modal').on('click', function(e) {
        if ($(e.target).is('.modal')) {
            $(this).modal('hide');
        }
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
