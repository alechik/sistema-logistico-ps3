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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('sku', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->string('unit_of_measure', 50);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->string('location', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('category_id')
                  ->references('id')
                  ->on('product_categories')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
