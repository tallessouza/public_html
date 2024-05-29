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
        Schema::table('extensions', function (Blueprint $table) {
            if (Schema::hasColumn('extensions', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('extensions', 'review')) {
                $table->dropColumn('review');
            }

            if (Schema::hasColumn('extensions', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('extensions', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('extensions', 'badge')) {
                $table->dropColumn('badge');
            }

            if (Schema::hasColumn('extensions', 'zip_url')) {
                $table->dropColumn('zip_url');
            }

            if (Schema::hasColumn('extensions', 'price_id')) {
                $table->dropColumn('price_id');
            }

            if (Schema::hasColumn('extensions', 'image_url')) {
                $table->dropColumn('image_url');
            }

            if (Schema::hasColumn('extensions', 'detail')) {
                $table->dropColumn('detail');
            }

            if (Schema::hasColumn('extensions', 'licensed')) {
                $table->dropColumn( 'licensed');
            }

            if (Schema::hasColumn('extensions', 'theme_type')) {
                $table->dropColumn('theme_type');
            }

            if (Schema::hasColumn('extensions', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('extensions', 'fake_price')) {
                $table->dropColumn( 'fake_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            //
        });
    }
};
