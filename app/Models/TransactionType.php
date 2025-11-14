<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_types';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'effect',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * Get the inventory transactions for the transaction type.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'transaction_type_id');
    }

    /**
     * Get the validation rules that apply to the transaction type.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:50|unique:transaction_types,name,' . $id . ',id',
            'description' => 'nullable|string',
            'effect' => 'required|in:entrada,salida,ajuste',
            'active' => 'boolean',
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
            'name.required' => 'El nombre del tipo de transacción es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'name.unique' => 'Ya existe un tipo de transacción con este nombre.',
            'effect.required' => 'El efecto de la transacción es obligatorio.',
            'effect.in' => 'El efecto de la transacción no es válido.',
        ];
    }

    /**
     * Scope a query to only include active transaction types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Check if the transaction type is an entry type.
     *
     * @return bool
     */
    public function isEntry(): bool
    {
        return $this->effect === 'entrada';
    }

    /**
     * Check if the transaction type is an exit type.
     *
     * @return bool
     */
    public function isExit(): bool
    {
        return $this->effect === 'salida';
    }

    /**
     * Check if the transaction type is an adjustment type.
     *
     * @return bool
     */
    public function isAdjustment(): bool
    {
        return $this->effect === 'ajuste';
    }
}
