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
        Schema::table('users', function (Blueprint $table) {
            $table->text('email_confirmation_code')->nullable();
            $table->boolean('email_confirmed')->default(0);
            $table->text('password_reset_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_confirmation_code');
            $table->dropColumn('email_confirmed');
            $table->dropColumn('password_reset_code');
        });
    }
};
