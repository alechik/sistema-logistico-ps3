<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'warehouses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'email',
        'cellphone',
        'status',
        'manager_id',
        'warehouse_type_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => true,
    ];

    /**
     * Get the warehouse type that owns the warehouse.
     */
    public function warehouseType(): BelongsTo
    {
        return $this->belongsTo(WarehouseType::class, 'warehouse_type_id');
    }

    /**
     * Get the manager that owns the warehouse.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the inventory records for the warehouse.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'warehouse_id');
    }

    /**
     * Get the sales for the warehouse.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'warehouse_id');
    }

    /**
     * Get the source transactions for the warehouse.
     */
    public function sourceTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'source_warehouse_id');
    }

    /**
     * Get the target transactions for the warehouse.
     */
    public function targetTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'target_warehouse_id');
    }

    /**
     * Get the validation rules that apply to the warehouse.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:warehouses,email,' . $id . ',id',
            'cellphone' => 'nullable|string|max:50',
            'status' => 'boolean',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
            'manager_id' => 'nullable|exists:users,id',
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
            'name.required' => 'El nombre del almacén es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está en uso por otro almacén.',
            'warehouse_type_id.required' => 'El tipo de almacén es obligatorio.',
            'warehouse_type_id.exists' => 'El tipo de almacén seleccionado no es válido.',
            'manager_id.exists' => 'El responsable seleccionado no es válido.',
        ];
    }
}
