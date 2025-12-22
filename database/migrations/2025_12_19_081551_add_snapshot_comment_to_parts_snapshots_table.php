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
        Schema::table('parts_snapshots', function (Blueprint $table) {
            $table->text('snapshot_comment')->nullable()->after('snapshot_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parts_snapshots', function (Blueprint $table) {
            $table->dropColumn('snapshot_comment');
        });
    }
};