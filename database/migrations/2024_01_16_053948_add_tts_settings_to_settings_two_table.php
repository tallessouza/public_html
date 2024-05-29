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
        Schema::table('settings_two', function (Blueprint $table) {
            $table->string('elevenlabs_api_key')->nullable();
            $table->boolean('feature_tts_google')->default(true);
            $table->boolean('feature_tts_openai')->default(true);
            $table->boolean('feature_tts_elevenlabs')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            $table->dropColumn('elevenlabs_api_key');
            $table->dropColumn('feature_tts_google');
            $table->dropColumn('feature_tts_openai');
            $table->dropColumn('feature_tts_elevenlabs');
        });
    }
};
