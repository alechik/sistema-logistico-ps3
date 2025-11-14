<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_items';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'tax',
        'discount',
        'total',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
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
        'tax' => 0,
        'discount' => 0,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Calcular el subtotal si no está establecido
            if (empty($model->subtotal) && isset($model->quantity) && isset($model->unit_price)) {
                $model->subtotal = $model->quantity * $model->unit_price;
            }
            
            // Calcular el total si no está establecido
            if (empty($model->total) && isset($model->subtotal)) {
                $model->total = $model->subtotal + $model->tax - $model->discount;
            }
        });
        
        static::updating(function ($model) {
            // Recalcular el subtotal si cambia la cantidad o el precio
            if ($model->isDirty(['quantity', 'unit_price'])) {
                $model->subtotal = $model->quantity * $model->unit_price;
                $model->total = $model->subtotal + $model->tax - $model->discount;
            }
            
            // Recalcular el total si cambia el impuesto o el descuento
            if ($model->isDirty(['tax', 'discount'])) {
                $model->total = $model->subtotal + $model->tax - $model->discount;
            }
        });
    }

    /**
     * Get the sale that owns the sale item.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /**
     * Get the product that owns the sale item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the validation rules that apply to the sale item.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function messages(): array
    {
        return [
            'sale_id.required' => 'La venta es obligatoria.',
            'sale_id.exists' => 'La venta seleccionada no es válida.',
            'product_id.required' => 'El producto es obligatorio.',
            'product_id.exists' => 'El producto seleccionado no es válido.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'unit_price.required' => 'El precio unitario es obligatorio.',
            'unit_price.min' => 'El precio unitario no puede ser negativo.',
            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',
            'tax.required' => 'El impuesto es obligatorio.',
            'tax.min' => 'El impuesto no puede ser negativo.',
            'discount.required' => 'El descuento es obligatorio.',
            'discount.min' => 'El descuento no puede ser negativo.',
            'total.required' => 'El total es obligatorio.',
            'total.min' => 'El total no puede ser negativo.',
        ];
    }

    /**
     * Calculate the total of the sale item.
     *
     * @return void
     */
    public function calculateTotal(): void
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        $this->total = $this->subtotal + $this->tax - $this->discount;
    }
}
