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
        Schema::create('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->id();

            $table->boolean('features_active')->default(1);
            $table->string('features_title')->default('The future of AI.');
            $table->text('features_description')->nullable();


            $table->boolean('generators_active')->default(1);

            $table->boolean('who_is_for_active')->default(1);


            $table->boolean('custom_templates_active')->default(1);
            $table->string('custom_templates_subtitle_one')->default('Custom');
            $table->string('custom_templates_subtitle_two')->default('Prompts');
            $table->string('custom_templates_title')->default('Custom Templates.');
            $table->text('custom_templates_description')->nullable();


            $table->boolean('tools_active')->default(1);
            $table->string('tools_title')->default('Magic Tools.');
            $table->text('tools_description')->nullable();

            $table->boolean('how_it_works_active')->default(1);
            $table->string('how_it_works_title')->default('So, how does it work?');


            $table->boolean('testimonials_active')->default(1);
            $table->string('testimonials_title')->default('Trusted by millions.');
            $table->string('testimonials_subtitle_one')->default('Testimonials');
            $table->string('testimonials_subtitle_two')->default('Trustpilot');


            $table->boolean('pricing_active')->default(1);
            $table->string('pricing_title')->default('Flexible Pricing.');
            $table->text('pricing_description')->nullable();
            $table->string('pricing_save_percent')->default('Save 30%');

            $table->boolean('faq_active')->default(1);
            $table->string('faq_title')->default('Have a question?');
            $table->string('faq_subtitle')->default('Our support team will get assistance from AI-powered suggestions, making it quicker than ever to handle support requests.');
            $table->string('faq_text_one')->default('FAQ');
            $table->string('faq_text_two')->default('Help Center');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_sections_statuses_titles');
    }
};
