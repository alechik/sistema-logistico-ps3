<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'product_category_id',
        'purchase_price',
        'sale_price',
        'stock',
        'min_stock',
        'barcode',
        'unit',
        'image',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Get the URL to the product's image.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Scope a query to only include active products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include low stock products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock')
                    ->where('min_stock', '>', 0);
    }

    /**
     * Get the inventories for the product.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id');
    }

    /**
     * Get the sale items for the product.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }

    /**
     * Get the inventory transactions for the product.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'product_id');
    }

    /**
     * Get the validation rules that apply to the product.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $id . ',id',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
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
            'name.required' => 'El nombre del producto es obligatorio.',
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Este SKU ya está en uso.',
            'sku.max' => 'El SKU no debe exceder los 255 caracteres.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }

    /**
     * Get the current stock across all warehouses.
     *
     * @return int
     */
    public function getTotalStockAttribute(): int
    {
        return $this->inventories->sum('stock');
    }
}
