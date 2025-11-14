<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_types';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'permissions_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the users for the user type.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_type_id');
    }

    /**
     * Get the permissions for the user type.
     */
    public function permissions(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permissions_id');
    }

    /**
     * Get the validation rules that apply to the user type.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'name' => 'required|string|max:255|unique:user_types,name,' . $id . ',id',
            'permissions_id' => 'nullable|exists:permissions,id',
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
            'name.required' => 'El nombre del tipo de usuario es obligatorio.',
            'name.unique' => 'Este nombre de tipo de usuario ya está en uso.',
            'permissions_id.exists' => 'El permiso seleccionado no es válido.',
        ];
    }
}
