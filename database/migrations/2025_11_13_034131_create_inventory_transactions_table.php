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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('transaction_type_id');
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->dateTime('transaction_date');
            $table->text('notes')->nullable();
            $table->string('reference', 100)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('inventory_id')
                  ->references('id')
                  ->on('inventories')
                  ->onDelete('cascade');
                  
            $table->foreign('transaction_type_id')
                  ->references('id')
                  ->on('transaction_types')
                  ->onDelete('restrict');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
                  
            $table->foreign('sale_id')
                  ->references('id')
                  ->on('sales')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
