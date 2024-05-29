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
            $table->string('openai_default_model')->default('gpt-3.5-turbo');
            $table->string('openai_default_language')->default('en-US');
            $table->string('openai_default_tone_of_voice')->default('professional');
            $table->string('openai_default_creativity')->default('0.75');
            $table->string('openai_max_input_length')->default('300');
            $table->string('openai_max_output_length')->default('200');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('openai_default_model');
            $table->dropColumn('openai_default_language');
            $table->dropColumn('openai_default_tone_of_voice');
            $table->dropColumn('openai_default_creativity');
            $table->dropColumn('openai_max_input_length');
            $table->dropColumn('openai_max_output_length');
        });
    }
};
