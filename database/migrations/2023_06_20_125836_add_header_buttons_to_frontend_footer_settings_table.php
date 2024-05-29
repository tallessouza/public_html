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
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
        $table->string('sign_in')->default('Sign In');
        $table->string('join_hub')->default('Join Hub');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_footer_settings', function (Blueprint $table) {
            $table->dropColumn('sign_in');
            $table->dropColumn('join_hub');
        });
    }
};
