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
            $table->text('frontend_code_before_head')->nullable();
            $table->text('frontend_code_before_body')->nullable();
            $table->text('dashboard_code_before_head')->nullable();
            $table->text('dashboard_code_before_body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('frontend_code_before_head');
            $table->dropColumn('frontend_code_before_body');
            $table->dropColumn('dashboard_code_before_head');
            $table->dropColumn('dashboard_code_before_body');
        });
    }
};
