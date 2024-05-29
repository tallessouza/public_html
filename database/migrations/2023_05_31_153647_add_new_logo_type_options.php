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
            $table->string('logo_dark')->default('magicAI-logo-dark.svg');
            $table->text('logo_dashboard')->nullable();
            $table->text('logo_dashboard_dark')->nullable();
            $table->string('logo_collapsed_dark')->default('magicAI-logo-collapsed-dark.svg');
            $table->text('logo_2x')->nullable();
            $table->text('logo_dark_2x')->nullable();
            $table->text('logo_dashboard_2x')->nullable();
            $table->text('logo_dashboard_dark_2x')->nullable();
            $table->text('logo_collapsed_2x')->nullable();
            $table->text('logo_collapsed_dark_2x')->nullable();
            $table->string('logo_dark_path')->default('assets/img/logo/magicAI-logo-dark.svg');
            $table->text('logo_dashboard_path')->nullable();
            $table->text('logo_dashboard_dark_path')->nullable();
            $table->string('logo_collapsed_dark_path')->default('assets/img/logo/magicAI-logo-collapsed-dark.svg');
            $table->text('logo_2x_path')->nullable();
            $table->text('logo_dark_2x_path')->nullable();
            $table->text('logo_dashboard_2x_path')->nullable();
            $table->text('logo_dashboard_dark_2x_path')->nullable();
            $table->text('logo_collapsed_2x_path')->nullable();
            $table->text('logo_collapsed_dark_2x_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('logo_dark');
            $table->dropColumn('logo_dashboard');
            $table->dropColumn('logo_dashboard_dark');
            $table->dropColumn('logo_collapsed_dark');
            $table->dropColumn('logo_2x');
            $table->dropColumn('logo_dark_2x');
            $table->dropColumn('logo_dashboard_2x');
            $table->dropColumn('logo_dashboard_dark_2x');
            $table->dropColumn('logo_collapsed_2x');
            $table->dropColumn('logo_collapsed_dark_2x');
            $table->dropColumn('logo_dark_path');
            $table->dropColumn('logo_dashboard_path');
            $table->dropColumn('logo_dashboard_dark_path');
            $table->dropColumn('logo_collapsed_dark_path');
            $table->dropColumn('logo_2x_path');
            $table->dropColumn('logo_dark_2x_path');
            $table->dropColumn('logo_dashboard_2x_path');
            $table->dropColumn('logo_dashboard_dark_2x_path');
            $table->dropColumn('logo_collapsed_2x_path');
            $table->dropColumn('logo_collapsed_dark_2x_path');
        });
    }
};
