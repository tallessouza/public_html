<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\OpenAIGenerator;
use App\Models\PrivacyTerms;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function general()
    {
        return view('panel.admin.settings.general', [
            'chatSetting' => Extension::query()
                ->where('slug', 'chat-setting')
                ->where('installed', true)
                ->exists(),
        ]);
    }

    public function generalSave(Request $request)
    {

        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings = Setting::first();
            $settings_two = SettingTwo::first();

            $metaTitleLocal = $request->metaTitleLocal;
            $metaDescLocal = $request->metaDescLocal;

            if ($metaTitleLocal == $settings_two->languages_default) {
                $settings->meta_title = $request->meta_title;
            } else {
                $meta_title = PrivacyTerms::where('type', 'meta_title')->where('lang', $metaTitleLocal)->first();
                if ($meta_title) {
                    $meta_title->content = $request->meta_title;
                    $meta_title->save();
                } else {
                    $new_meta_title = new PrivacyTerms();
                    $new_meta_title->type = 'meta_title';
                    $new_meta_title->lang = $metaTitleLocal;
                    $new_meta_title->content = $request->meta_title;
                    $new_meta_title->save();
                }
            }

            if ($metaDescLocal == $settings_two->languages_default) {
                $settings->meta_description = $request->meta_description;
            } else {
                $meta_description = PrivacyTerms::where('type', 'meta_desc')->where('lang', $metaDescLocal)->first();
                if ($meta_description) {
                    $meta_description->content = $request->meta_description;
                    $meta_description->save();
                } else {
                    $new_meta_description = new PrivacyTerms();
                    $new_meta_description->type = 'meta_desc';
                    $new_meta_description->lang = $metaDescLocal;
                    $new_meta_description->content = $request->meta_description;
                    $new_meta_description->save();
                }
            }

            if ($request->has('chat_setting_for_customer') || $request->has('default_ai_engine') ) {

                setting([
                    'chat_setting_for_customer' => $request->chat_setting_for_customer,
                    'default_ai_engine' => $request->default_ai_engine,
                ])->save();
            }

            if ($request->has('default_aw_image_engine')) {
                setting([
                    'default_aw_image_engine' => $request->default_aw_image_engine,
                ])->save();
            }

            if ($request->has('photo_studio')) {
                setting([
                    'photo_studio' => $request->photo_studio,
                ])->save();
            }

            $settings->site_name = $request->site_name;
            $settings->site_url = $request->site_url;
            $settings->site_email = $request->site_email;
            $settings->default_country = $request->default_country;
            $settings->default_currency = $request->default_currency;
            $settings->register_active = $request->register_active;
            $settings->google_analytics_code = $request->google_analytics_code;
            $settings->meta_keywords = $request->meta_keywords;
            $settings->dashboard_code_before_head = $request->dashboard_code_before_head;
            $settings->dashboard_code_before_body = $request->dashboard_code_before_body;
            $settings->feature_ai_writer = $request->feature_ai_writer;
            $settings->feature_ai_rewriter = $request->feature_ai_rewriter;
            $settings->feature_ai_chat_image = $request->feature_ai_chat_image;
            $settings->feature_ai_image = $request->feature_ai_image;
            $settings_two->feature_ai_video = $request->feature_ai_video;
            $settings->feature_ai_chat = $request->feature_ai_chat;
            $settings->feature_ai_code = $request->feature_ai_code;
            $settings->feature_ai_speech_to_text = $request->feature_ai_speech_to_text;
            $settings->feature_ai_voiceover = $request->feature_ai_voiceover;
            $settings->feature_affilates = $request->feature_affilates;
            $settings->user_api_option = $request->user_api_option;
            $settings->feature_ai_article_wizard = $request->feature_ai_article_wizard;
            $settings->feature_ai_vision = $request->feature_ai_vision;
            $settings->feature_ai_pdf = $request->feature_ai_pdf;
            $settings->feature_ai_youtube = $request->feature_ai_youtube;
            $settings->feature_ai_rss = $request->feature_ai_rss;
            $settings->feature_ai_voice_clone = (bool) $request->feature_ai_voice_clone;
            $settings->team_functionality = $request->team_functionality;
            $settings->feature_ai_advanced_editor = $request->feature_ai_advanced_editor;
            $settings->login_without_confirmation = $request->login_without_confirmation;
            $settings->facebook_active = $request->facebook_active ?? 0;
            $settings->google_active = $request->google_active ?? 0;
            $settings->github_active = $request->github_active ?? 0;
            $settings->free_plan = $request->free_plan ?? '0,0';
            $settings->mobile_payment_active = $request->mobile_payment_active ?? 0;
            $settings->save();

            setting(['user_prompt_library' => $request->user_prompt_library])->save();
            setting(['user_ai_image_prompt_library' => $request->user_ai_image_prompt_library])->save();

            $this->toggleOpenaiTemplateStatus($settings);

            $settings_two->daily_limit_enabled = $request->limit;
            $settings_two->allowed_images_count = $request->daily_limit_count;
            $settings_two->openai_default_stream_server = $request->openai_default_stream_server;
            $settings_two->daily_voice_limit_enabled = $request->voice_limit;
            $settings_two->allowed_voice_count = $request->daily_voice_limit_count;

            $settings_two->save();

            $logo_types = [
                'logo' => '',
                'logo_dark' => 'dark',
                'logo_sticky' => 'sticky',
                'logo_dashboard' => 'dashboard',
                'logo_dashboard_dark' => 'dashboard-dark',
                'logo_collapsed' => 'collapsed',
                'logo_collapsed_dark' => 'collapsed-dark',
                // retina
                'logo_2x' => '2x',
                'logo_dark_2x' => 'dark-2x',
                'logo_sticky_2x' => 'sticky-2x',
                'logo_dashboard_2x' => 'dashboard-2x',
                'logo_dashboard_dark_2x' => 'dashboard-dark-2x',
                'logo_collapsed_2x' => 'collapsed-2x',
                'logo_collapsed_dark_2x' => 'collapsed-dark-2x',
            ];

            foreach ($logo_types as $logo => $logo_prefix) {

                if ($request->hasFile($logo)) {
                    $path = 'upload/images/logo/';
                    $image = $request->file($logo);
                    $image_name = Str::random(4).'-'.$logo_prefix.'-'.Str::slug($settings->site_name).'-logo.'.$image->getClientOriginalExtension();

                    //Resim uzantı kontrolü
                    $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                    if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                        $data = [
                            'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                        ];

                        return response()->json($data, 419);
                    }

                    $image->move($path, $image_name);

                    $settings->{$logo.'_path'} = $path.$image_name;
                    $settings->{$logo} = $image_name;
                    $settings->save();
                }

            }

            if ($request->hasFile('favicon')) {
                $path = 'upload/images/favicon/';
                $image = $request->file('favicon');
                $image_name = Str::random(4).'-'.Str::slug($settings->site_name).'-favicon.'.$image->getClientOriginalExtension();

                //Resim uzantı kontrolü
                $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                    $data = [
                        'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                    ];

                    return response()->json($data, 419);
                }

                $image->move($path, $image_name);

                $settings->favicon_path = $path.$image_name;
                $settings->favicon = $image_name;
                $settings->save();
            }
        }
    }

    public function toggleOpenaiTemplateStatus($settings)
    {
        $templates = [
            'ai_article_wizard_generator' => $settings->feature_ai_article_wizard,
            'ai_writer' => $settings->feature_ai_writer,
            'ai_rewriter' => $settings->feature_ai_rewriter,
            'ai_chat_image' => $settings->feature_ai_chat_image,
            'ai_image_generator' => $settings->feature_ai_image,
            'ai_code_generator' => $settings->feature_ai_code,
            'ai_speech_to_text' => $settings->feature_ai_speech_to_text,
            'ai_voiceover' => $settings->feature_ai_voiceover,
            'ai_vision' => $settings->feature_ai_vision,
            'ai_pdf' => $settings->feature_ai_pdf,
            'ai_youtube' => $settings->feature_ai_youtube,
            'ai_rss' => $settings->feature_ai_rss,
        ];

        foreach ($templates as $key => $status) {
            OpenAIGenerator::query()->where('slug', $key)->update(['active' => $status]);
        }
    }

    public function anthropic(Request $request)
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.anthropic');
    }

    public function anthropicTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $token = setting('anthropic_api_secret');

        $explodeToken = explode(',', $token);

        $randomToken = Arr::random($explodeToken);

        $request = Http::withHeaders([
            'x-api-key' => $randomToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => setting('anthropic_default_model'),
            'max_tokens' => (int) setting('anthropic_max_output_length'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello, how are you?',
                ],
            ],
        ]);

        if ($request->ok()) {
            echo ' <br>'.$randomToken.' - SUCCESS <br>';
        } else {
            echo $request->json('error.message').' - '.$randomToken.' -FAILED <br>';
        }
    }

    public function anthropicSave(Request $request)
    {
        $data = $request->validate([
            'anthropic_api_secret' => 'required|string',
            'anthropic_default_model' => 'required|string',
            'anthropic_max_input_length' => 'required|string',
            'anthropic_max_output_length' => 'required|string',
        ]);

        setting($data)->save();

        return response()->json([], 200);
    }

    public function gemini(Request $request)
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.gemini');
    }

    public function geminiTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $newhistory = [
            [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => 'who are u.',
                    ],
                ],
            ],
        ];

        $randomToken = Helper::setGeminiKey();
        $client = app(\App\Services\Ai\Gemini::class);
        $response = $client
            ->setHistory($newhistory)
            ->generateContent();
        if ($response->ok()) {
            echo ' <br>'.$randomToken.' - SUCCESS <br>';
        } else {
            echo $response->json('error.message').' -FAILED <br>';
        }
    }

    public function geminiSave(Request $request)
    {
        $data = $request->validate([
            'gemini_api_secret' => 'required|string',
            'gemini_default_model' => 'required|string',
            'gemini_max_input_length' => 'required|string',
            'gemini_max_output_length' => 'required|string',
        ]);
        setting($data)->save();

        return response()->json([], 200);
    }

    public function openai()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.openai');
    }

    public function stablediffusion()
    {
        return view('panel.admin.settings.stablediffusion');
    }

    public function unsplashapi(Request $request)
    {
        return view('panel.admin.settings.unsplashapi');
    }

    public function unsplashapiTest()
    {
        $client = new Client();
        $settings = SettingTwo::first();
        if ($settings->unsplash_api_key == '') {
            echo 'You must provide Unsplash API Key.';

            return;
        }

        $apiKey = $settings->unsplash_api_key;

        $client = new Client();

        try {
            $response = $client->get("https://api.unsplash.com/search/photos?query=Google&count=1&client_id=$apiKey");
            echo ' <br>'.$apiKey.' - SUCCESS <br>';
        } catch (\Exception $e) {
            echo $e->getMessage().' - '.$apiKey.' -FAILED <br>';
        }
    }

    public function unsplashapiSave(Request $request)
    {
        $settings = SettingTwo::first();
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings->unsplash_api_key = $request->unsplash_api_key;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function pexelsapi(Request $request)
    {
        return view('panel.admin.settings.pexels');
    }

    public function pexelsapiTest()
    {
        $client = new Client();
        $api = setting('pexels_api_key');
        if ($api == '') {
            echo 'You must provide Pexels API Key.';

            return;
        }

        $apiKey = $api;

        $client = new Client();

        try {
            $response = $client->get('https://api.pexels.com/v1/search?query=Google&per_page=1', [
                'headers' => [
                    'Authorization' => $apiKey,
                ],
            ]);
            echo ' <br>'.$apiKey.' - SUCCESS <br>';
        } catch (\Exception $e) {
            echo $e->getMessage().' - '.$apiKey.' -FAILED <br>';
        }
    }

    public function pexelsapiSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting(['pexels_api_key' => $request->pexels_api_key])->save();
        }

        return response()->json([], 200);
    }

    public function pixabayapi(Request $request)
    {
        return view('panel.admin.settings.pixabay');
    }

    public function pixabayapiTest()
    {
        $client = new Client();
        $api = setting('pixabay_api_key');
        if ($api == '') {
            echo 'You must provide Pixabay API Key.';

            return;
        }

        $apiKey = $api;
        $client = new Client();
        try {
            $response = $client->get("https://pixabay.com/api/?key=$apiKey&q=Google");
            echo ' <br>'.$apiKey.' - SUCCESS <br>';
        } catch (\Exception $e) {
            echo $e->getMessage().' - '.$apiKey.' -FAILED <br>';
        }
    }

    public function pixabayapiSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting(['pixabay_api_key' => $request->pixabay_api_key])->save();
        }

        return response()->json([], 200);
    }

    public function serperapi(Request $request)
    {
        return view('panel.admin.settings.serperapi');
    }

    public function serperapiSave(Request $request)
    {
        $settings = SettingTwo::first();
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings->serper_api_key = $request->serper_api_key;
            $settings->save();

            if ($request->hasAny(['serper_seo_aw_sq', 'serper_seo_aw_keyword', 'serper_seo_blog_title_desc', 'serper_seo_site_meta'])) {
                setting([
                    'serper_seo_aw_sq' => $request->serper_seo_aw_sq,
                    'serper_seo_aw_keyword' => $request->serper_seo_aw_keyword,
                    'serper_seo_blog_title_desc' => $request->serper_seo_blog_title_desc,
                    'serper_seo_site_meta' => $request->serper_seo_site_meta,
                ])->save();
            }
        }

        return response()->json([], 200);
    }

    public function clipdrop(Request $request)
    {
        return view('panel.admin.settings.clipdrop');
    }

    public function clipdropSave(Request $request)
    {
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            setting([
                'clipdrop_api_key' => $request->clipdrop_api_key
            ])->save();
        }

        return response()->json([], 200);
    }

    public function serperapiTest()
    {
        try {
            $settings = SettingTwo::first();
            if ($settings->serper_api_key == '') {
                echo 'You must provide Serper API Key.';

                return;
            }
            $client = new Client();
            $response = $client->post('https://google.serper.dev/search', [
                'headers' => [
                    'X-API-KEY' => $settings->serper_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'q' => 'Coffee',
                ],
            ]);
            $responseData = json_decode($response->getBody(), true);
            echo ' <br>'.$settings->serper_api_key.' - SUCCESS <br><hr> Example about "Coffee": <br>'.$responseData['organic'][0]['snippet'].'<br>';
        } catch (\Exception $e) {
            echo $e->getMessage().' - '.$settings->serper_api_key.' -FAILED <br>';
        }
    }

    public function openaiTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $client = new Client();
        $settings = Setting::first();
        if ($settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $settings?->openai_api_secret);
        }
        foreach ($apiKeys as $apiKey) {

            $client = new Client([
                'base_uri' => 'https://api.openai.com/v1/',
                'headers' => [
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            try {

                $response = $client->post('chat/completions', [
                    'json' => [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [['role' => 'user', 'content' => 'Say this is a test!']],
                        'temperature' => 0.7,
                    ],
                ]);

                echo ' <br>'.$apiKey.' - SUCCESS <br>';
            } catch (\Exception $e) {
                // API çağrısı başarısız oldu veya hata aldınız.
                echo $e->getMessage().' - '.$apiKey.' -FAILED <br>';
            }
        }
    }

    public function stablediffusionTest()
    {
        $client = new Client();
        $settings = SettingTwo::first();
        if ($settings->stable_diffusion_api_key == '') {
            echo 'You must provide Stable Difussion API key.';

            return;
        }

        $apiKeys = explode(',', $settings->stable_diffusion_api_key);

        foreach ($apiKeys as $apiKey) {

            $client = new Client([
                'base_uri' => 'https://stablediffusionapi.com',
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $prompt = 'Man on the mountain';

            try {
                // print_r($client); exit;
                $response = $client->post('/api/v3/text2img', [
                    'json' => [
                        'key' => $apiKey,
                        'prompt' => $prompt,
                        'negative_prompt' => null,
                        'width' => 512,
                        'height' => 512,
                        'samples' => 1,
                        'num_inference_steps' => '20',
                        'seed' => null,
                        'guidance_scale' => 7.5,
                        'safety_checker' => 'yes',
                        'multi_lingual' => 'no',
                        'panorama' => 'no',
                        'self_attention' => 'no',
                        'upscale' => 'no',
                        'embeddings_model' => null,
                        'webhook' => null,
                        'track_id' => null,
                    ],
                ]);
                echo ' <br>'.$apiKey.' - SUCCESS <br>';
            } catch (\Exception $e) {
                // API çağrısı başarısız oldu veya hata aldınız.
                echo $e->getMessage().' - '.$apiKey.' -FAILED <br>';
            }
        }
    }

    public function openaiSave(Request $request)
    {
        $settings = Setting::first();
        $settings_two = SettingTwo::first();
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings->openai_api_secret = $request->openai_api_secret;
            $settings->openai_default_model = $request->openai_default_model;
            $settings->openai_default_language = $request->openai_default_language;
            $settings->openai_default_tone_of_voice = $request->openai_default_tone_of_voice;
            $settings->openai_default_creativity = $request->openai_default_creativity;
            $settings->openai_max_input_length = $request->openai_max_input_length;
            $settings->openai_max_output_length = $request->openai_max_output_length;
            $settings_two->dalle = $request->dalle_default_model;
            $settings_two->openai_default_stream_server = $request->openai_default_stream_server;
            $settings->save();
            $settings_two->save();
            setting([
                'hide_creativity_option' => $request->hide_creativity_option,
                'hide_tone_of_voice_option' => $request->hide_tone_of_voice_option,
                'hide_output_length_option' => $request->hide_output_length_option,
            ])->save();
        }

        return response()->json([], 200);
    }

    public function affiliateStatusSave($id, Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $user = User::find($id);
            if ($user) {
                $user->affiliate_status = $request->input('affiliate_status');
                $user->save();
            }
        }

        return response()->json([], 200);
    }

    public function stablediffusionSave(Request $request)
    {
        $settings = SettingTwo::first();
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings->stable_diffusion_api_key = $request->stable_diffusion_api_key;
            $settings->stablediffusion_default_language = $request->stablediffusion_default_language;
            $settings->stablediffusion_default_model = $request->stablediffusion_default_model;
            $settings->save();

            setting(['stable_hidden' => $request->stable_hidden])->save();
        }

        return response()->json([], 200);
    }

    // thumbnail
    public function thumbnail()
    {
        return view('panel.admin.settings.thumbnail');
    }

    // thumbnailSave
    public function thumbnailSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting(['image_thumbnail' => $request->image_thumbnail])->save();
        }

        return response()->json([], 200);
    }

    // thumbnailPurge
    public function thumbnailPurge()
    {
        if (Helper::appIsNotDemo()) {
            PurgeThumbImages();
        }

        return response()->json([], 200);
    }

    public function tts()
    {
        return view('panel.admin.settings.tts');
    }

    public function ttsSave(Request $request)
    {
        $settings = Setting::first();
        $settings_two = SettingTwo::first();
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings->gcs_file = $request->gcs_file;
            $settings->gcs_name = $request->gcs_name;

            $settings_two->elevenlabs_api_key = $request->elevenlabs_api_key;

            $settings_two->feature_tts_google = $request->feature_tts_google;
            $settings_two->feature_tts_openai = $request->feature_tts_openai;
            $settings_two->feature_tts_elevenlabs = $request->feature_tts_elevenlabs;

            if ($request->hasAny(['feature_tts_azure', 'azure_api_key', 'azure_region'])) {
                setting([
                    'feature_tts_azure' => $request->get('feature_tts_azure'),
                    'azure_api_key' => $request->get('azure_api_key'),
                    'azure_region' => $request->get('azure_region'),
                ])->save();
            }

            $settings->save();
            $settings_two->save();
        }

        return response()->json([], 200);
    }

    public function invoice()
    {
        return view('panel.admin.settings.invoice');
    }

    public function invoiceSave(Request $request)
    {
        $settings = Setting::first();
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings->invoice_currency = $request->invoice_currency;
            $settings->invoice_name = $request->invoice_name;
            $settings->invoice_website = $request->invoice_website;
            $settings->invoice_address = $request->invoice_address;
            $settings->invoice_city = $request->invoice_city;
            $settings->invoice_state = $request->invoice_state;
            $settings->invoice_country = $request->invoice_country;
            $settings->invoice_phone = $request->invoice_phone;
            $settings->invoice_postal = $request->invoice_postal;
            $settings->invoice_vat = $request->invoice_vat;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function payment()
    {
        return view('panel.admin.settings.stripe');
    }

    public function paymentSave(Request $request)
    {
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings = Setting::first();
            $settings->default_currency = $request->default_currency;
            $settings->stripe_active = 1;
            $settings->stripe_key = $request->stripe_key;
            $settings->stripe_secret = $request->stripe_secret;
            $settings->stripe_base_url = $request->stripe_base_url;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function affiliate()
    {
        $users = User::query()->paginate(10);

        return view('panel.admin.settings.affiliate', compact('users'));
    }

    public function affiliateSave(Request $request)
    {
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings = Setting::first();
            $settings->affiliate_minimum_withdrawal = $request->affiliate_minimum_withdrawal;
            $settings->affiliate_commission_percentage = $request->affiliate_commission_percentage;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function smtp()
    {
        return view('panel.admin.settings.smtp');
    }

    public function smtpSave(Request $request)
    {
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings = Setting::first();
            $settings->smtp_host = $request->smtp_host;
            $settings->smtp_port = $request->smtp_port;
            $settings->smtp_username = $request->smtp_username;
            $settings->smtp_password = $request->smtp_password;
            $settings->smtp_email = $request->smtp_email;
            $settings->smtp_sender_name = $request->smtp_sender_name;
            $settings->smtp_encryption = $request->smtp_encryption;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function smtpTest(Request $request)
    {
        $toEmail = $request->test_email;
        $toName = 'Test Email';

        try {
            Mail::raw('Test email content', function ($message) use ($toEmail, $toName) {
                $message->to($toEmail, $toName)
                    ->subject('Test Email');
            });

            return 'Test email sent!';

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function gdpr()
    {
        return view('panel.admin.settings.gdpr');
    }

    public function gdprSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $settings = Setting::first();
            $settings->gdpr_status = $request->gdpr_status;
            $settings->gdpr_button = $request->gdpr_button;
            $settings->gdpr_content = $request->gdpr_content;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function privacy()
    {
        return view('panel.admin.settings.privacy');
    }

    public function privacySave(Request $request)
    {
        if (Helper::appIsNotDemo()) {

            $settings_two = SettingTwo::first();
            $settings = Setting::first();

            $termsLocal = $request->termsLocal;
            $privacyLocal = $request->privacyLocal;

            if ($termsLocal == $settings_two->languages_default) {
                $settings->terms_content = $request->terms_content;
            } else {
                $terms = PrivacyTerms::where('type', 'terms')->where('lang', $termsLocal)->first();
                if ($terms) {
                    $terms->content = $request->terms_content;
                    $terms->save();
                } else {
                    $newTerms = new PrivacyTerms();
                    $newTerms->type = 'terms';
                    $newTerms->lang = $termsLocal;
                    $newTerms->content = $request->terms_content;
                    $newTerms->save();
                }
            }

            if ($privacyLocal == $settings_two->languages_default) {
                $settings->privacy_content = $request->privacy_content;
            } else {
                $privacy = PrivacyTerms::where('type', 'privacy')->where('lang', $privacyLocal)->first();
                if ($privacy) {
                    $privacy->content = $request->privacy_content;
                    $privacy->save();
                } else {
                    $newPrivacy = new PrivacyTerms();
                    $newPrivacy->type = 'privacy';
                    $newPrivacy->lang = $privacyLocal;
                    $newPrivacy->content = $request->privacy_content;
                    $newPrivacy->save();
                }
            }

            $settings->privacy_enable = $request->privacy_enable;
            $settings->privacy_enable_login = $request->privacy_enable_login;
            $settings->save();

        }

        return response()->json([], 200);
    }

    public function getPrivacyTermsContent(Request $request)
    {
        $type = $request->input('type');
        $language = $request->input('lang');
        $settings_two = SettingTwo::first();

        if ($settings_two->languages_default == $language) {

            $settings = Setting::first();
            $content = [
                'type' => $type,
                'lang' => $language,
                'content' => $type == 'terms' ? $settings->terms_content : $settings->privacy_content,
            ];

        } else {
            $privacy_terms = PrivacyTerms::where('type', $type)->where('lang', $language)->first();
            $content = [
                'type' => $privacy_terms?->type ?? $type,
                'lang' => $privacy_terms?->lang,
                'content' => $privacy_terms?->content,
            ];
        }

        return response()->json($content);
    }

    public function getMetaContent(Request $request)
    {
        $type = $request->input('type');
        $language = $request->input('lang');
        $settings_two = SettingTwo::first();

        if ($settings_two->languages_default == $language) {

            $settings = Setting::first();
            $content = [
                'type' => $type,
                'lang' => $language,
                'content' => $type == 'meta_title' ? $settings->meta_title : $settings->meta_description,
            ];

        } else {
            $meta = PrivacyTerms::where('type', $type)->where('lang', $language)->first();
            $content = [
                'type' => $meta?->type ?? $type,
                'lang' => $meta?->lang,
                'content' => $meta?->content,
            ];
        }

        return response()->json($content);
    }

    public function storage()
    {
        return view('panel.admin.settings.storage', [
            'cloudflare_r2' => Extension::query()->where('slug', 'cloudflare-r2')->exists(),
        ]);
    }

    public function storagesave(Request $request)
    {
        // TODO SETTINGS
        if (Helper::appIsNotDemo()) {
            $settings_two = SettingTwo::first();
            $settings_two->ai_image_storage = $request->ai_image_storage;
            $settings_two->save();
        }

        return response()->json([], 200);
    }
}