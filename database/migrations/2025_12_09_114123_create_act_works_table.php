<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('act_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('act_id')->constrained('acts')->cascadeOnDelete();
            $table->foreignId('work_id')->constrained('works')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['act_id', 'work_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('act_works');
    }
};