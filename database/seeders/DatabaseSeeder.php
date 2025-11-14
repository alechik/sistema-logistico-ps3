<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use App\Models\WarehouseType;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createUserTypes();
        $this->createWarehouseTypes();
        $this->createProductCategories();
        $this->createAdminUser();
    }

    /**
     * Create default user types.
     */
    private function createUserTypes(): void
    {
        $types = ['Administrator', 'Manager', 'Employee'];
        
        foreach ($types as $type) {
            UserType::firstOrCreate(['name' => $type]);
        }
        
        $this->command->info('User types seeded successfully!');
    }

    /**
     * Create default warehouse types.
     */
    private function createWarehouseTypes(): void
    {
        $types = [
            ['name' => 'Principal', 'description' => 'Almacén principal de la empresa'],
            ['name' => 'Secundario', 'description' => 'Almacén secundario o de respaldo'],
            ['name' => 'Temporal', 'description' => 'Almacén temporal para inventario en tránsito'],
            ['name' => 'Externo', 'description' => 'Almacén externo o de terceros'],
        ];
        
        foreach ($types as $type) {
            WarehouseType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
        
        $this->command->info('Warehouse types seeded successfully!');
    }

    /**
     * Create default product categories.
     */
    private function createProductCategories(): void
    {
        $categories = [
            ['name' => 'Electrónicos', 'description' => 'Productos electrónicos y dispositivos'],
            ['name' => 'Ropa', 'description' => 'Ropa y accesorios'],
            ['name' => 'Alimentos', 'description' => 'Productos alimenticios'],
            ['name' => 'Hogar', 'description' => 'Artículos para el hogar'],
            ['name' => 'Deportes', 'description' => 'Artículos deportivos'],
        ];
        
        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
        
        $this->command->info('Product categories seeded successfully!');
    }

    /**
     * Create admin user if not exists.
     */
    private function createAdminUser(): void
    {
        if (User::where('email', 'admin@example.com')->exists()) {
            $this->command->info('Admin user already exists.');
            return;
        }

        $adminType = UserType::where('name', 'Administrator')->first();
        
        if (!$adminType) {
            $this->command->error('Administrator user type not found!');
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type_id' => $adminType->id,
        ]);
        
        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
        $this->command->warn('IMPORTANT: Change the default password after first login!');
    }
}
