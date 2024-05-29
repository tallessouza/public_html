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
            $table->string('chatbot_status')->nullable()->default('disabled');
            $table->integer('chatbot_template')->nullable();
            $table->string('chatbot_position')->nullable()->default('bottom-left');
            $table->tinyInteger('chatbot_login_require')->default(true);
            $table->integer('chatbot_rate_limit')->nullable()->default(10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            $table->dropColumn('chatbot_status');
            $table->dropColumn('chatbot_template');
            $table->dropColumn('chatbot_position');
            $table->dropColumn('chatbot_login_require');
            $table->dropColumn('chatbot_rate_limit');
        });
    }
};
