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
        // First, drop the check constraint if it exists
        DB::statement("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_status_check");
        
        // Then modify the column type
        DB::statement("ALTER TABLE sales ALTER COLUMN status TYPE VARCHAR(255) USING status::text");
        
        // Now add the new check constraint
        DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_status_check CHECK (status IN ('pendiente', 'completada', 'cancelada'))");
        
        // Set default value
        DB::statement("ALTER TABLE sales ALTER COLUMN status SET DEFAULT 'pendiente'");
        
        // Make the column not nullable
        DB::statement("ALTER TABLE sales ALTER COLUMN status SET NOT NULL");
        
        Schema::table('sales', function (Blueprint $table) {
            // Remove payment related columns
            if (Schema::hasColumn('sales', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            
            // Rename invoice_number to order_number for clarity
            if (Schema::hasColumn('sales', 'invoice_number')) {
                $table->renameColumn('invoice_number', 'order_number');
            }
            
            // Add notes if not exists
            if (!Schema::hasColumn('sales', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new check constraint
        DB::statement("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_status_check");
        
        // Revert to the original type
        DB::statement("ALTER TABLE sales ALTER COLUMN status TYPE VARCHAR(255) USING status::text");
        
        // Add back the original check constraint if needed
        // Note: You'll need to adjust this based on your original status values
        DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_status_check CHECK (status IN ('pendiente', 'completada', 'cancelada'))");
        
        Schema::table('sales', function (Blueprint $table) {
            // Add back payment method column if it doesn't exist
            if (!Schema::hasColumn('sales', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('status');
            }
            
            // Revert column name if it was changed
            if (Schema::hasColumn('sales', 'order_number')) {
                $table->renameColumn('order_number', 'invoice_number');
            }
        });
    }
};
