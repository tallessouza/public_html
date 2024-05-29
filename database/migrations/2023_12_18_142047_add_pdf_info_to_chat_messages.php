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
        Schema::table('user_openai_chat_messages', function (Blueprint $table) {
            $table->text('pdfName')->nullable();
            $table->text('pdfPath')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_openai_chat_messages', function (Blueprint $table) {
            $table->dropColumn('pdfName');
            $table->dropColumn('pdfPath');
        });
    }
};
