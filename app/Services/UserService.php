<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;
use App\Models\WarehouseType;

class UserService
{
    public static function createSuperUserIfNotExists()
    {
        if (User::count() === 0) {
            // Create admin user type if it doesn't exist
            $adminType = UserType::firstOrCreate(
                ['name' => 'Administrator'],
                ['name' => 'Administrator']
            );

            // Create default warehouse types
            $warehouseTypes = [
                'Principal', 'Secundario', 'Temporal', 'Externo'
            ];
            
            foreach ($warehouseTypes as $type) {
                WarehouseType::firstOrCreate(['name' => $type]);
            }

            // Create super admin user
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'), // Change this in production!
                'user_type_id' => $adminType->id,
            ]);
        }
    }
}
