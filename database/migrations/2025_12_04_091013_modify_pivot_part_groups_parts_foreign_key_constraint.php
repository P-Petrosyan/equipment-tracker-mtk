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
        Schema::table('pivot_part_groups_parts', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['part_id']);
            
            // Add the new foreign key constraint with cascade on delete
            $table->foreign('part_id')
                ->references('id')
                ->on('parts')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pivot_part_groups_parts', function (Blueprint $table) {
            // Drop the cascade constraint
            $table->dropForeign(['part_id']);
            
            // Restore the original restrict constraint
            $table->foreign('part_id')
                ->references('id')
                ->on('parts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};