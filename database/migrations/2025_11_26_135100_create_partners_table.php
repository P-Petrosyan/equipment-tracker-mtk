<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('region');
            $table->string('region_hf')->nullable();
            $table->text('address')->nullable();
            $table->string('bank')->nullable();
            $table->string('region_r')->nullable();
            $table->string('tnoren')->nullable();
            $table->string('hashvapah')->nullable();
            $table->string('account_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
