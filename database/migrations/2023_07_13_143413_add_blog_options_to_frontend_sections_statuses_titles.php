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
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->boolean('blog_active')->default(0);
            $table->string('blog_title')->default('Latest News');
            $table->string('blog_subtitle')->default('Stay up-to-date');
            $table->integer('blog_posts_per_page')->default(3);
            $table->string('blog_button_text')->default('Show more');
            $table->string('blog_a_title')->default('Blog Posts');
            $table->string('blog_a_subtitle')->default('Latest News');
            $table->string('blog_a_description')->default('Welcome to our cozy corner of the internet, where you will find a delightful collection of our heartfelt and thought-provoking blog posts.');
            $table->integer('blog_a_posts_per_page')->default(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_sections_statuses_titles', function (Blueprint $table) {
            $table->dropColumn('blog_active');
            $table->dropColumn('blog_title');
            $table->dropColumn('blog_subtitle');
            $table->dropColumn('blog_posts_per_page');
            $table->dropColumn('blog_button_text');
            $table->dropColumn('blog_a_title');
            $table->dropColumn('blog_a_subtitle');
            $table->dropColumn('blog_a_description');
            $table->dropColumn('blog_a_posts_per_page');
        });
    }
};
