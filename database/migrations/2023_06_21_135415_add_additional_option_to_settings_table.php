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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('feature_ai_voiceover')->default(true)->nullable();
            $table->text('gcs_file')->nullable();
            $table->text('gcs_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('feature_ai_voiceover');
            $table->dropColumn('gcs_file');
            $table->dropColumn('gcs_name');
        });
    }
};
