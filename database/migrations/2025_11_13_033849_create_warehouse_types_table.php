<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default warehouse types
        DB::table('warehouse_types')->insert([
            ['name' => 'Principal', 'description' => 'Almacén principal de la empresa', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secundario', 'description' => 'Almacén secundario o de respaldo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Temporal', 'description' => 'Almacén temporal para inventario en tránsito', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Externo', 'description' => 'Almacén externo o de terceros', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_types');
    }
};
