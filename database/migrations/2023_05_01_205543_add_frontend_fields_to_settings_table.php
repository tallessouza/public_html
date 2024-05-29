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
            $table->boolean('frontend_pricing_section')->default(1);
            $table->boolean('frontend_custom_templates_section')->default(1);
            $table->boolean('frontend_business_partners_section')->default(1);
            $table->string('frontend_additional_url')->nullable();
            $table->string('frontend_custom_js')->nullable();
            $table->string('frontend_custom_css')->nullable();
            $table->string('frontend_footer_facebook')->nullable();
            $table->string('frontend_footer_twitter')->nullable();
            $table->string('frontend_footer_instagram')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('frontend_pricing_section');
            $table->dropColumn('frontend_custom_templates_section');
            $table->dropColumn('frontend_business_partners_section');
            $table->dropColumn('frontend_additional_url');
            $table->dropColumn('frontend_custom_js');
            $table->dropColumn('frontend_custom_css');
            $table->dropColumn('frontend_footer_facebook');
            $table->dropColumn('frontend_footer_twitter');
            $table->dropColumn('frontend_footer_instagram');
        });
    }
};
