@php
    $rowId = 'product_' . $index;
    $itemId = $item->id ?? null;
    $productId = $product->id;
    $productCode = $product->code;
    $productName = $product->name;
    $productUnit = $product->unit ?? 'u';
    $quantity = $item->quantity ?? 1;
    $price = $item->unit_price ?? $product->sale_price ?? 0;
    $discount = $item->discount ?? 0;
    $stock = $product->stock ?? 0;
    $subtotal = $quantity * $price * (1 - ($discount / 100));
@endphp

<tr class="sale-item" id="{{ $rowId }}" data-id="{{ $rowId }}" data-item-id="{{ $itemId }}" data-product-id="{{ $productId }}">
    <td>
        {{ $productName }}
        <input type="hidden" name="product_id[]" value="{{ $productId }}">
        <div class="text-muted">{{ $productCode }}</div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" class="form-control quantity" 
                   id="quantity_{{ $rowId }}" 
                   name="quantity[]" 
                   value="{{ $quantity }}" 
                   min="0.01" 
                   step="0.01" 
                   data-stock="{{ $stock }}"
                   required>
            <div class="input-group-append">
                <span class="input-group-text">{{ $productUnit }}</span>
            </div>
        </div>
        <small class="text-danger stock-error" style="display: none;">Stock insuficiente</small>
    </td>
    <td>
        <input type="number" class="form-control price" 
               id="price_{{ $rowId }}" 
               name="price[]" 
               value="{{ number_format($price, 2, '.', '') }}" 
               min="0" 
               step="0.01" 
               required>
    </td>
    <td>
        <div class="input-group">
            <input type="number" class="form-control discount" 
                   id="discount_{{ $rowId }}" 
                   name="discount[]" 
                   value="{{ $discount }}" 
                   min="0" 
                   max="100"
                   step="0.01">
            <div class="input-group-append">
                <span class="input-group-text">%</span>
            </div>
        </div>
    </td>
    <td class="text-right subtotal">S/ {{ number_format($subtotal, 2) }}</td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger remove-product">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
