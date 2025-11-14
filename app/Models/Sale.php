<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'invoice_number',
        'sale_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'notes',
        'user_id',
        'warehouse_id',
        'customer_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pendiente',
        'subtotal' => 0,
        'tax' => 0,
        'discount' => 0,
        'total' => 0,
    ];

    /**
     * Get the user that created the sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the customer that owns the sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the warehouse that owns the sale.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the sale items for the sale.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    /**
     * Get the inventory transactions for the sale.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'sale_id');
    }

    /**
     * Get the validation rules that apply to the sale.
     *
     * @param  int|null  $id
     * @return array
     */
    /**
     * Get the validation rules that apply to the sale.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'order_number' => 'required|string|max:50|unique:sales,order_number,' . $id,
            'sale_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:pendiente,completada,cancelada',
            'notes' => 'nullable|string',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function messages(): array
    {
        return [
            'order_number.required' => 'El número de orden es obligatorio.',
            'order_number.unique' => 'El número de orden ya está en uso.',
            'sale_date.required' => 'La fecha de venta es obligatoria.',
            'sale_date.date' => 'La fecha de venta debe ser una fecha válida.',
            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric' => 'El subtotal debe ser un número.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',
            'tax.required' => 'El impuesto es obligatorio.',
            'tax.numeric' => 'El impuesto debe ser un número.',
            'tax.min' => 'El impuesto no puede ser negativo.',
            'discount.required' => 'El descuento es obligatorio.',
            'discount.numeric' => 'El descuento debe ser un número.',
            'discount.min' => 'El descuento no puede ser negativo.',
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total no puede ser negativo.',
            'status.required' => 'El estado de la venta es obligatorio.',
            'status.in' => 'El estado de la venta no es válido. Los valores permitidos son: pendiente, completada, cancelada.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no es válido.',
            'customer_id.exists' => 'El cliente seleccionado no es válido.',
            'items.required' => 'La venta debe tener al menos un producto.',
            'items.array' => 'Los productos deben ser una lista.',
            'items.min' => 'La venta debe tener al menos un producto.',
            'items.*.product_id.required' => 'El ID del producto es obligatorio.',
            'items.*.product_id.exists' => 'Uno o más productos no son válidos.',
            'items.*.quantity.required' => 'La cantidad es obligatoria.',
            'items.*.quantity.numeric' => 'La cantidad debe ser un número.',
            'items.*.quantity.min' => 'La cantidad debe ser mayor a 0.',
            'items.*.unit_price.required' => 'El precio unitario es obligatorio.',
            'items.*.unit_price.numeric' => 'El precio unitario debe ser un número.',
            'items.*.unit_price.min' => 'El precio unitario no puede ser negativo.',
            'items.*.discount.required' => 'El descuento es obligatorio.',
            'items.*.discount.numeric' => 'El descuento debe ser un número.',
            'items.*.discount.min' => 'El descuento no puede ser negativo.',
            'items.*.discount.max' => 'El descuento no puede ser mayor a 100%.',
        ];
    }

    /**
     * Calculate the total of the sale.
     *
     * @return void
     */
    public function calculateTotal(): void
    {
        $this->total = $this->subtotal + $this->tax - $this->discount;
    }

    /**
     * Check if the sale is completed.
     *
     * @return bool
     */
    /**
     * Check if the sale is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completada';
    }

    /**
     * Check if the sale is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pendiente';
    }

    /**
     * Check if the sale is canceled.
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->status === 'cancelada';
    }

    /**
     * Check if the sale is refunded.
     *
     * @return bool
     */
    /**
     * Check if the sale is refunded.
     *
     * @return bool
     */
    public function isRefunded(): bool
    {
        // Si necesitas manejar reembolsos, agrega 'reembolsada' a las reglas de validación
        return false; // Temporalmente deshabilitado hasta que se implemente la lógica de reembolso
    }

    /**
     * Generate a unique invoice number.
     *
     * @return string
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'FAC-' . date('Ymd') . '-';
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
