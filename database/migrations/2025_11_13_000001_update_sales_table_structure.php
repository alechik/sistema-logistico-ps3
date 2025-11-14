<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if the sales table exists
        if (Schema::hasTable('sales')) {
            // Drop the check constraint if it exists
            try {
                DB::statement("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_status_check");
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
            
            // Add payment_method column if it exists (to be removed)
            if (Schema::hasColumn('sales', 'payment_method')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->dropColumn('payment_method');
                });
            }
            
            // Rename invoice_number to order_number if it exists
            if (Schema::hasColumn('sales', 'invoice_number')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->renameColumn('invoice_number', 'order_number');
                });
            }
            
            // Add notes if it doesn't exist
            if (!Schema::hasColumn('sales', 'notes')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->text('notes')->nullable()->after('status');
                });
            }
            
            // Update status column
            Schema::table('sales', function (Blueprint $table) {
                $table->string('status', 50)->default('pendiente')->change();
            });
            
            // Add check constraint
            DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_status_check CHECK (status IN ('pendiente', 'completada', 'cancelada'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales')) {
            // Drop the check constraint if it exists
            try {
                DB::statement("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_status_check");
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
            
            // Revert status column
            Schema::table('sales', function (Blueprint $table) {
                $table->string('status', 50)->default('pendiente')->change();
            });
            
            // Revert column name if needed
            if (Schema::hasColumn('sales', 'order_number')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->renameColumn('order_number', 'invoice_number');
                });
            }
            
            // Add back payment_method column
            if (!Schema::hasColumn('sales', 'payment_method')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->string('payment_method', 50)->nullable()->after('status');
                });
            }
        }
    }
};
