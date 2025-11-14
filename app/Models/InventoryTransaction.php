<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InventoryTransaction extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_transactions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventory_id',
        'transaction_type_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'transaction_date',
        'notes',
        'reference',
        'user_id',
        'sale_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
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
        'transaction_date' => null,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transaction_date)) {
                $model->transaction_date = now();
            }
            
            if (empty($model->user_id) && auth()->check()) {
                $model->user_id = auth()->id();
            }
            
            // Calcular el costo total si no está establecido
            if (empty($model->total_cost) && isset($model->quantity) && isset($model->unit_cost)) {
                $model->total_cost = $model->quantity * $model->unit_cost;
            }
        });
    }

    /**
     * Get the inventory that owns the transaction.
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * Get the transaction type that owns the transaction.
     */
    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the sale that owns the transaction.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /**
     * Get the validation rules that apply to the transaction.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'inventory_id' => 'required|exists:inventories,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:100',
            'user_id' => 'required|exists:users,id',
            'sale_id' => 'nullable|exists:sales,id',
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
            'inventory_id.required' => 'El inventario es obligatorio.',
            'inventory_id.exists' => 'El inventario seleccionado no es válido.',
            'transaction_type_id.required' => 'El tipo de transacción es obligatorio.',
            'transaction_type_id.exists' => 'El tipo de transacción seleccionado no es válido.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.min' => 'La cantidad debe ser al menos 1.',
            'unit_cost.required' => 'El costo unitario es obligatorio.',
            'unit_cost.min' => 'El costo unitario no puede ser negativo.',
            'total_cost.required' => 'El costo total es obligatorio.',
            'total_cost.min' => 'El costo total no puede ser negativo.',
            'transaction_date.required' => 'La fecha de la transacción es obligatoria.',
            'transaction_date.date' => 'La fecha de la transacción debe ser una fecha válida.',
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no es válido.',
            'sale_id.exists' => 'La venta seleccionada no es válida.',
        ];
    }

    /**
     * Scope a query to only include transactions of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $effect
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEffect($query, $effect)
    {
        return $query->whereHas('transactionType', function ($q) use ($effect) {
            $q->where('effect', $effect);
        });
    }

    /**
     * Scope a query to only include entry transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntries($query)
    {
        return $this->byEffect('entrada');
    }

    /**
     * Scope a query to only include exit transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExits($query)
    {
        return $this->byEffect('salida');
    }

    /**
     * Scope a query to only include adjustment transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdjustments($query)
    {
        return $this->byEffect('ajuste');
    }

    /**
     * Check if the transaction is an entry.
     *
     * @return bool
     */
    public function isEntry(): bool
    {
        return $this->transactionType->effect === 'entrada';
    }

    /**
     * Check if the transaction is an exit.
     *
     * @return bool
     */
    public function isExit(): bool
    {
        return $this->transactionType->effect === 'salida';
    }

    /**
     * Check if the transaction is an adjustment.
     *
     * @return bool
     */
    public function isAdjustment(): bool
    {
        return $this->transactionType->effect === 'ajuste';
    }

    const TIPO_ENTRADA_COMPRA = 'entrada_compra';
    const TIPO_ENTRADA_AJUSTE = 'entrada_ajuste';
    const TIPO_ENTRADA_DEVOLUCION = 'entrada_devolucion';
    
    // Tipos de movimiento de salida
    const TIPO_SALIDA_VENTA = 'salida_venta';
    const TIPO_SALIDA_AJUSTE = 'salida_ajuste';
    const TIPO_SALIDA_VENCIMIENTO = 'salida_vencimiento';
    const TIPO_SALIDA_DESPERDICIO = 'salida_desperdicio';

    /**
     * Obtener el inventario asociado al movimiento
     */
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    /**
     * Obtener el usuario que realizó el movimiento
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Verificar si el movimiento es de entrada
     */
    public function esEntrada()
    {
        return in_array($this->tipo_movimiento, [
            self::TIPO_ENTRADA_COMPRA,
            self::TIPO_ENTRADA_AJUSTE,
            self::TIPO_ENTRADA_DEVOLUCION
        ]);
    }

    /**
     * Verificar si el movimiento es de salida
     */
    public function esSalida()
    {
        return in_array($this->tipo_movimiento, [
            self::TIPO_SALIDA_VENTA,
            self::TIPO_SALIDA_AJUSTE,
            self::TIPO_SALIDA_VENCIMIENTO,
            self::TIPO_SALIDA_DESPERDICIO
        ]);
    }

    /**
     * Obtener la descripción legible del tipo de movimiento
     */
    public function getTipoMovimientoDescripcionAttribute()
    {
        $tipos = [
            self::TIPO_ENTRADA_COMPRA => 'Entrada por Compra',
            self::TIPO_ENTRADA_AJUSTE => 'Entrada por Ajuste',
            self::TIPO_ENTRADA_DEVOLUCION => 'Entrada por Devolución',
            self::TIPO_SALIDA_VENTA => 'Salida por Venta',
            self::TIPO_SALIDA_AJUSTE => 'Salida por Ajuste',
            self::TIPO_SALIDA_VENCIMIENTO => 'Salida por Vencimiento',
            self::TIPO_SALIDA_DESPERDICIO => 'Salida por Desperdicio',
        ];

        return $tipos[$this->tipo_movimiento] ?? 'Desconocido';
    }

    /**
     * Obtener todos los tipos de movimiento disponibles
     */
    public static function getTiposMovimiento()
    {
        return [
            'entrada' => [
                self::TIPO_ENTRADA_COMPRA => 'Entrada por Compra',
                self::TIPO_ENTRADA_AJUSTE => 'Entrada por Ajuste',
                self::TIPO_ENTRADA_DEVOLUCION => 'Entrada por Devolución',
            ],
            'salida' => [
                self::TIPO_SALIDA_VENTA => 'Salida por Venta',
                self::TIPO_SALIDA_AJUSTE => 'Salida por Ajuste',
                self::TIPO_SALIDA_VENCIMIENTO => 'Salida por Vencimiento',
                self::TIPO_SALIDA_DESPERDICIO => 'Salida por Desperdicio',
            ]
        ];
    }

    /**
     * Scope para filtrar movimientos por rango de fechas
     */
    public function scopeRangoFechas($query, $fechaInicio, $fechaFin = null)
    {
        if (!$fechaFin) {
            $fechaFin = Carbon::now();
        }
        
        return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para filtrar movimientos por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        if ($tipo === 'entrada') {
            return $query->whereIn('tipo_movimiento', [
                self::TIPO_ENTRADA_COMPRA,
                self::TIPO_ENTRADA_AJUSTE,
                self::TIPO_ENTRADA_DEVOLUCION
            ]);
        } elseif ($tipo === 'salida') {
            return $query->whereIn('tipo_movimiento', [
                self::TIPO_SALIDA_VENTA,
                self::TIPO_SALIDA_AJUSTE,
                self::TIPO_SALIDA_VENCIMIENTO,
                self::TIPO_SALIDA_DESPERDICIO
            ]);
        }
        
        return $query;
    }
}
