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
        Schema::create('pivot_part_groups_parts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_part_group_id')
                ->constrained('equipment_part_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('part_id')
                ->constrained('parts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Line-specific info:
            $table->decimal('quantity', 10, 2)->default(1);      // Քան
            $table->decimal('unit_price', 15, 2)->nullable();    // override default_unit_price if needed
            $table->text('comment')->nullable();                 // extra Armenian text if you want

            $table->timestamps();

            // if you want at most ONE row per (group, part) and use quantity instead:
            $table->unique(['equipment_part_group_id', 'part_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_part_groups_parts');
    }
};
