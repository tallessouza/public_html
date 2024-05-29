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
        Schema::table('user_support', function (Blueprint $table) {
            $table->string('ticket_id');
            $table->string('priority')->default('Low');
            $table->string('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_support', function (Blueprint $table) {
            $table->dropColumn('ticket_id');
            $table->dropColumn('priority');
            $table->dropColumn('category');
        });
    }
};
