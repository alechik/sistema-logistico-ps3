<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module',
        'access',
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
     * Get the user types that have this permission.
     */
    public function userTypes(): HasMany
    {
        return $this->hasMany(UserType::class, 'permissions_id');
    }

    /**
     * Get the validation rules that apply to the permission.
     *
     * @param  int|null  $id
     * @return array
     */
    public static function rules($id = null): array
    {
        return [
            'module' => 'required|string|max:100',
            'access' => 'required|string|max:100',
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
            'module.required' => 'El módulo es obligatorio.',
            'module.max' => 'El módulo no debe exceder los 100 caracteres.',
            'access.required' => 'El tipo de acceso es obligatorio.',
            'access.max' => 'El tipo de acceso no debe exceder los 100 caracteres.',
        ];
    }
}
