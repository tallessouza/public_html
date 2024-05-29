<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_integrations', function (Blueprint $table) {
            $table->id();
            $table->integer('integration_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->json('credentials')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_integrations');
    }
};
