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
            if (Schema::hasColumn('settings_two', 'stablediffusion_default_model')) {
                $table->string('stablediffusion_default_model')->default('stable-diffusion-xl-beta-v2-2-2')->change();
            } else {
                $table->string('stablediffusion_default_model')->default('stable-diffusion-xl-beta-v2-2-2');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            $table->string('stablediffusion_default_model')->default('stable-diffusion-xl-beta-v2-2-2')->change();
        });
    }
};
