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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default product categories
        DB::table('product_categories')->insert([
            ['name' => 'Electrónicos', 'description' => 'Productos electrónicos y dispositivos', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ropa', 'description' => 'Ropa y accesorios', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alimentos', 'description' => 'Productos alimenticios', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hogar', 'description' => 'Artículos para el hogar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deportes', 'description' => 'Artículos deportivos', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
