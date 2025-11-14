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
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('description')->nullable();
            $table->enum('effect', ['entrada', 'salida', 'ajuste']);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Insert default transaction types
        DB::table('transaction_types')->insert([
            ['name' => 'Compra', 'description' => 'Entrada de inventario por compra', 'effect' => 'entrada', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Venta', 'description' => 'Salida de inventario por venta', 'effect' => 'salida', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ajuste de entrada', 'description' => 'Ajuste manual de inventario (entrada)', 'effect' => 'ajuste', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ajuste de salida', 'description' => 'Ajuste manual de inventario (salida)', 'effect' => 'ajuste', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Devolución de compra', 'description' => 'Devolución de inventario a proveedor', 'effect' => 'salida', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Devolución de venta', 'description' => 'Devolución de inventario de cliente', 'effect' => 'entrada', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
    }
};
