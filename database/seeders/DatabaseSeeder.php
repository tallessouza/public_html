<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Helpers\Classes\InstallationHelper;
use App\Models\OpenAIGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        # run installation
        InstallationHelper::runInstallation();


//        $path = resource_path('/dev_tools/currency.sql');
//        DB::unprepared(file_get_contents($path));
//
//        $path2 = resource_path('/dev_tools/openai_table.sql');
//        DB::unprepared(file_get_contents($path2));
//
//        $path3 = resource_path('/dev_tools/openai_chat_categories_table.sql');
//        DB::unprepared(file_get_contents($path3));
//
//        $path4 = resource_path('/dev_tools/openai_filters.sql');
//        DB::unprepared(file_get_contents($path4));
//
//        $path5 = resource_path('/dev_tools/frontend_tools.sql');
//        DB::unprepared(file_get_contents($path5));
//
//        $path6 = resource_path('/dev_tools/faq.sql');
//        DB::unprepared(file_get_contents($path6));
//
//        $path7 = resource_path('/dev_tools/frontend_future.sql');
//        DB::unprepared(file_get_contents($path7));
//
//        $path8 = resource_path('/dev_tools/howitworks.sql');
//        DB::unprepared(file_get_contents($path8));
//
//        $path9 = resource_path('/dev_tools/testimonials.sql');
//        DB::unprepared(file_get_contents($path9));
//
//        $path10 = resource_path('/dev_tools/frontend_who_is_for.sql');
//        DB::unprepared(file_get_contents($path10));
//
//        $path11 = resource_path('/dev_tools/frontend_generators.sql');
//        DB::unprepared(file_get_contents($path11));
//
//        $path12 = resource_path('/dev_tools/clients.sql');
//        DB::unprepared(file_get_contents($path12));
//
//        $path13 = resource_path('/dev_tools/health_check_result_history_items.sql');
//        DB::unprepared(file_get_contents($path13));
//
//        $path14 = resource_path('/dev_tools/email_templates.sql');
//        DB::unprepared(file_get_contents($path14));
//
//        $path15 = resource_path('/dev_tools/ads.sql');
//        DB::unprepared(file_get_contents($path15));
//
//        $path16 = resource_path('/dev_tools/ai_wizard.sql');
//        DB::unprepared(file_get_contents($path16));
//
//        $path17 = resource_path('/dev_tools/ai_vision.sql');
//        DB::unprepared(file_get_contents($path17));
//
//        $path18 = resource_path('/dev_tools/ai_vision2.sql');
//        DB::unprepared(file_get_contents($path18));
//
//        $path19 = resource_path('/dev_tools/ai_pdf.sql');
//        DB::unprepared(file_get_contents($path19));
//
//        $path20 = resource_path('/dev_tools/ai_pdf2.sql');
//        DB::unprepared(file_get_contents($path20));
//
//        $path21 = resource_path('/dev_tools/ai_chat_image.sql');
//        DB::unprepared(file_get_contents($path21));
//
//        $path22 = resource_path('/dev_tools/ai_chat_image2.sql');
//        DB::unprepared(file_get_contents($path22));
//
//        $path23 = resource_path('/dev_tools/ai_rewriter.sql');
//        DB::unprepared(file_get_contents($path23));
//
//        $path24 = resource_path('/dev_tools/team_email_templates.sql');
//        DB::unprepared(file_get_contents($path24));
//
//        $path25 = resource_path('/dev_tools/ai_webchat.sql');
//        DB::unprepared(file_get_contents($path25));
//
//        $path26 = resource_path('/dev_tools/ai_webchat2.sql');
//        DB::unprepared(file_get_contents($path26));
//
//        $path27 = resource_path('/dev_tools/ai_filechat.sql');
//        DB::unprepared(file_get_contents($path27));
//
//        $path28 = resource_path('/dev_tools/ai_filechat2.sql');
//        DB::unprepared(file_get_contents($path28));
//
//        $path29 = resource_path('/dev_tools/ai_video.sql');
//        DB::unprepared(file_get_contents($path29));
//
//        if (
//            Schema::hasTable('plans')
//            && Schema::hasColumn('plans', 'open_ai_items')
//            && Schema::hasTable('openai')
//        ) {
//
//            $openaiItems = \App\Models\OpenAIGenerator::query()->pluck('slug')->toArray();
//
//            $plans = \App\Models\PaymentPlans::query()->get();
//
//            foreach ($plans as $plan) {
//                $plan->open_ai_items = $openaiItems;
//                $plan->save();
//            }
//        }
//
//        if (
//            Schema::hasTable('openai')
//            && Schema::hasTable('settings')
//            && Schema::hasColumn('settings', 'free_open_ai_items')
//        ) {
//            $openaiItems = \App\Models\OpenAIGenerator::query()->pluck('slug')->toArray();
//            $setting = \App\Models\Setting::first();
//
//            $setting->update([
//                'free_open_ai_items' => $openaiItems ?: [],
//            ]);
//        }
//
//        if (Schema::hasTable('pages')) {
//            $customPages = \App\Models\Page::query()->where('is_custom', 1)->get();
//            if ($customPages->count() == 0) {
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/inner_pages.sql')));
//            }
//        }
//
//        if (Schema::hasTable('openai')) {
//            if (! OpenAIGenerator::where('slug', 'ai_voiceover')->exists()) {
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/ai_voiceover.sql')));
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/ai_filter_voiceover.sql')));
//            }
//        }
//
//        if (Schema::hasTable('openai')) {
//            if (! OpenAIGenerator::where('slug', 'ai_youtube')->exists()) {
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/ai_youtube.sql')));
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/ai_filter_youtube.sql')));
//            }
//        }
//
//        if (Schema::hasTable('openai')) {
//            if (! OpenAIGenerator::where('slug', 'ai_rss')->exists()) {
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/ai_rss.sql')));
//                DB::unprepared(file_get_contents(resource_path('/dev_tools/ai_filter_rss.sql')));
//            }
//        }

        $this->command->info('Currency table seeded!');
    }
}
