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
        Schema::create('equipment_part_groups', function (Blueprint $table) {
            $table->id();

            // which equipment this group belongs to
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // columns similar to your Access screenshot:
            $table->string('name');
            $table->decimal('total_price', 15, 2)->nullable();
            $table->decimal('total_price_alt', 15, 2)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            // optional: unique group name per equipment
            $table->unique(['equipment_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_part_groups');
    }
};
