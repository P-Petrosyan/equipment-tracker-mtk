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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->date('receive_date');
            $table->date('exit_date')->nullable();

            $table->foreignId('partner_id')->constrained('partners')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('partner_structure_id')->nullable()->constrained('partner_structures')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('equipment_part_group_id')->nullable()->constrained('equipment_part_groups')->cascadeOnUpdate()->nullOnDelete();
            $table->decimal('equipment_part_group_total_price', 15, 2)->nullable();

            $table->string('old_serial_number')->nullable();
            $table->string('new_serial_number')->nullable();
            $table->string('partner_representative')->nullable();
            $table->boolean('non_repairable')->default(false);
            $table->string('conclusion_number')->nullable();

            $table->text('defects_description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('work_order_status')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
