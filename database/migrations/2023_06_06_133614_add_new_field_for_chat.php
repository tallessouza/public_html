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
        Schema::table('openai_chat_category', function (Blueprint $table) {
            $table->text('chat_completions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('openai_chat_category', function (Blueprint $table) {
            $table->dropColumn('chat_completions');
        });
    }
};
