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
            $table->boolean('privacy_enable')->default(0);
            $table->boolean('privacy_enable_login')->default(0);
            $table->text('privacy_content')->nullable();
            $table->text('terms_content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('privacy_enable');
            $table->dropColumn('privacy_enable_login');
            $table->dropColumn('privacy_content');
            $table->dropColumn('terms_content');
        });
    }
};
