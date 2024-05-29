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
        Schema::table('openai', function (Blueprint $table) {
            $table->text('prompt')->nullable();
            $table->boolean('custom_template')->default(0);
            $table->boolean('tone_of_voice')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('openai', function (Blueprint $table) {
            $table->dropColumn('prompt');
            $table->dropColumn('custom_template');
            $table->dropColumn('tone_of_voice');
        });
    }
};
