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
            $table->string('logo_collapsed')->default('magicAI-logo-Collapsed.png');
            $table->string('logo_collapsed_path')->default('assets/img/logo/magicAI-logo-Collapsed.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('logo_collapsed');
            $table->dropColumn('logo_collapsed_path');
        });
    }
};
