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
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default user types
        DB::table('user_types')->insert([
            ['name' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Employee', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
