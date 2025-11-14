<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'minimum_stock',
        'maximum_stock',
        'location',
        'last_restock_date',
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'double',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'minimum_stock' => 'double',
        'maximum_stock' => 'double',
        'last_restock_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the inventory.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the warehouse that owns the inventory.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the inventory transactions for the inventory.
     */

    /**
     * Get the validation rules that apply to the inventory.
     *
     * @param  int|null  $id
     * @return array
     */
    /**
     * Get the validation rules that apply to the inventory.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'maximum_stock' => 'required|numeric|min:0|gte:minimum_stock',
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
            'product_id.required' => 'El producto es obligatorio.',
            'product_id.exists' => 'El producto seleccionado no es válido.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no es válido.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'quantity.min' => 'La cantidad no puede ser negativa.',
            'minimum_stock.required' => 'El stock mínimo es obligatorio.',
            'minimum_stock.numeric' => 'El stock mínimo debe ser un número.',
            'minimum_stock.min' => 'El stock mínimo no puede ser negativo.',
            'maximum_stock.required' => 'El stock máximo es obligatorio.',
            'maximum_stock.numeric' => 'El stock máximo debe ser un número.',
            'maximum_stock.min' => 'El stock máximo no puede ser negativo.',
            'maximum_stock.gte' => 'El stock máximo debe ser mayor o igual al stock mínimo.',
        ];
    }

    /**
     * Check if the inventory is below the minimum stock level.
     *
     * @return bool
     */
    public function isBelowMinimumStock(): bool
    {
        return $this->quantity < $this->minimum_stock;
    }

    /**
     * Check if the inventory exceeds the maximum stock level.
     *
     * @return bool
     */
    public function exceedsMaximumStock(): bool
    {
        return $this->quantity > $this->maximum_stock;
    }
    
    /**
     * Accessor para compatibilidad con código que usa 'stock'
     */
    public function getStockAttribute()
    {
        return $this->quantity;
    }
    
    /**
     * Accessor para compatibilidad con código que usa 'min_stock'
     */
    public function getMinStockAttribute()
    {
        return $this->minimum_stock;
    }
    
    /**
     * Accessor para compatibilidad con código que usa 'max_stock'
     */
    public function getMaxStockAttribute()
    {
        return $this->maximum_stock;
    }
}
