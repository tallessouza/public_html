<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;

class AIController extends Controller
{
    protected $client;

    protected $settings;

    const STABLEDIFFUSION = 'stablediffusion';

    const STORAGE_S3 = 's3';

    const STORAGE_LOCAL = 'public';

    public function __construct()
    {
        //Settings
        $this->settings = Setting::first();
        $this->settings_two = SettingTwo::first();
        // Fetch the Site Settings object with openai_api_secret
        if ($this->settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $this->settings?->openai_api_secret);
        }
        $apiKey = $apiKeys[array_rand($apiKeys)];
        config(['openai.api_key' => $apiKey]);
        set_time_limit(120);
    }

     /**
     * @OA\Post(
     *      path="/api/aiwriter/generate",
     *      operationId="buildOutputApi",
     *      tags={"AI Writer"},
     *      security={{ "passport": {} }},
     *      summary="Build AI-generated content please see the function there is custom inputs foreach template",
     *      description="Build AI-generated content based on user input.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="post_type",
     *                      type="string",
     *                      enum={"post_title_generator", "article_generator", "summarize_text", "product_description", "product_name", "testimonial_review", "problem_agitate_solution", "blog_section", "blog_post_ideas", "blog_intros", "blog_conclusion", "facebook_ads", "youtube_video_description", "youtube_video_title", "youtube_video_tag", "instagram_captions", "instagram_hashtag", "social_media_post_tweet", "social_media_post_business", "facebook_headlines", "google_ads_headlines", "google_ads_description", "content_rewrite", "paragraph_generator", "pros_cons", "meta_description", "faq_generator", "email_generator", "email_answer_generator", "newsletter_generator", "grammar_correction", "tldr_summarization", "ai_image_generator", "ai_code_generator"},
     *                      example="summarize_text"
     *                  ),
     *                  @OA\Property(
     *                      property="maximum_length",
     *                      description="Maximum length of the generated text",
     *                      type="integer",
     *                      example=200
     *                  ),
     *                  @OA\Property(
     *                      property="number_of_results",
     *                      description="Number of summary results to generate",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="creativity",
     *                      description="Creativity level for the generated content (0 to 1)",
     *                      type="number",
     *                      example=0.75
     *                  ),
     *                  @OA\Property(
     *                      property="tone_of_voice",
     *                      description="Tone of voice for the generated content",
     *                      type="string",
     *                      example="Professional"
     *                  ),
     *                  @OA\Property(
     *                      property="language",
     *                      description="Language code for the input text",
     *                      type="string",
     *                      example="en-US"
     *                  ),
     *                  @OA\Property(
     *                      property="text_to_summary",
     *                      description="Text to be summarized",
     *                      type="string",
     *                      example="dfg"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message_id", type="integer", description="ID of the generated content message"),
     *              @OA\Property(property="workbook", type="object", description="Details about the generated content workbook"),
     *              @OA\Property(property="creativity", type="number", description="Creativity factor (between 0 and 1)"),
     *              @OA\Property(property="maximum_length", type="integer", description="Maximum length of the generated content"),
     *              @OA\Property(property="number_of_results", type="integer", description="Number of desired results"),
     *              @OA\Property(property="inputPrompt", type="string", description="User input prompt"),
     *              @OA\Property(property="generated_content", type="string", description="Generated content"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="errors", type="string", description="Error message"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", description="Internal Server Error"),
     *          ),
     *      ),
     * )
     */
    public function buildOutput(Request $request)
    {
        $user = $request->user();

        if ($request->post_type != 'ai_image_generator' && $user->remaining_words <= 0 && $user->remaining_words != -1) {
            return response()->json(['errors' => 'You have no remaining words. Please upgrade your plan.'], 400);
        }

        $image_generator = $request->image_generator;
        $post_type = $request->post_type;

        //SETTINGS
        $number_of_results = $request->number_of_results;
        $maximum_length = $request->maximum_length;
        $creativity = $request->creativity;

        $language = $request->language;
        try {
            $language = explode('-', $language);
            if (count($language) > 1 && LaravelLocalization::getSupportedLocales()[$language[0]]['name']) {
                $ek = $language[1];
                $language = LaravelLocalization::getSupportedLocales()[$language[0]]['name'];
                $language .= " $ek";
            } else {
                $language = $request->language;
            }
        } catch (\Throwable $th) {
            $language = $request->language;
            Log::error($language);
        }

        $negative_prompt = $request->negative_prompt;
        $tone_of_voice = $request->tone_of_voice;

        //POST TITLE GENERATOR
        if ($post_type == 'post_title_generator') {
            $your_description = $request->your_description;
            $prompt = "Post title about $your_description in language $language .Generate $number_of_results post titles. Tone $tone_of_voice.";
        }

        //ARTICLE GENERATOR
        if ($post_type == 'article_generator') {
            $article_title = $request->article_title;
            $focus_keywords = $request->focus_keywords;
            $prompt = "Generate article about $article_title. Focus on $focus_keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice";
        }

        //SUMMARY GENERATOR SUMMARIZER SUMMARIZE TEXT
        if ($post_type == 'summarize_text') {
            $text_to_summary = $request->text_to_summary;
            $tone_of_voice = $request->tone_of_voice;

            $prompt = "Summarize the following text: $text_to_summary in $language using a tone of voice that is $tone_of_voice. The summary should be no longer than $maximum_length words and set the creativity to $creativity in terms of creativity. Generate $number_of_results different summaries.";
        }

        //PRODUCT DESCRIPTION
        if ($post_type == 'product_description') {
            $product_name = $request->product_name;
            $description = $request->description;

            $prompt = "Write product description for $product_name. The language is $language. Maximum length is $maximum_length. Creativity is $creativity between 0 to 1. see the following information as a starting point: $description. Generate $number_of_results different product descriptions. Tone $tone_of_voice.";
        }

        //PRODUCT NAME
        if ($post_type == 'product_name') {
            $seed_words = $request->seed_words;
            $product_description = $request->product_description;

            $prompt = "Generate product names that will appeal to customers who are interested in $seed_words. These products should be related to $product_description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different product names. Tone of voice must be $tone_of_voice";
        }

        //TESTIMONIAL REVIEW GENERATOR
        if ($post_type == 'testimonial_review') {
            $subject = $request->subject;
            $prompt = "Generate testimonial for $subject. Include details about how it helped you and what you like best about it. Be honest and specific, and feel free to get creative with your wording Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different testimonials. Tone of voice must be $tone_of_voice";
        }

        //PROBLEM AGITATE SOLUTION
        if ($post_type == 'problem_agitate_solution') {
            $description = $request->description;

            $prompt = "Write Problem-Agitate-Solution copy for the $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. problem-agitate-solution. Tone of voice must be $tone_of_voice Generate $number_of_results different Problem-Afitate-Solution.";
        }

        //BLOG SECTION
        if ($post_type == 'blog_section') {
            $description = $request->description;

            $prompt = " Write me blog section about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog sections. Tone of voice must be $tone_of_voice";
        }

        //BLOG POST IDEAS
        if ($post_type == 'blog_post_ideas') {
            $description = $request->description;

            $prompt = "Write blog post article ideas about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog post ideas. Tone of voice must be $tone_of_voice";
        }

        //BLOG INTROS
        if ($post_type == 'blog_intros') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post intro about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog intros. Tone of voice must be $tone_of_voice";
        }

        //BLOG CONCLUSION
        if ($post_type == 'blog_conclusion') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post conclusion about title: $title. And the description is $description.Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog conclusions. Tone of voice must be $tone_of_voice";
        }

        //FACEBOOK ADS
        if ($post_type == 'facebook_ads') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write facebook ads text about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads text. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO DESCRIPTION
        if ($post_type == 'youtube_video_description') {
            $title = $request->title;

            $prompt = "write youtube video description about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video descriptions. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO TITLE
        if ($post_type == 'youtube_video_title') {
            $description = $request->description;

            $prompt = "Craft captivating, attention-grabbing video titles about $description for YouTube rankings. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video titles. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO TAG
        if ($post_type == 'youtube_video_tag') {
            $title = $request->title;

            $prompt = "Generate tags and keywords about $title for youtube video. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video tags. Tone of voice must be $tone_of_voice";
        }

        //INSTAGRAM CAPTIONS
        if ($post_type == 'instagram_captions') {
            $title = $request->title;

            $prompt = "Write instagram post caption about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram captions. Tone of voice must be $tone_of_voice";
        }

        //INSTAGRAM HASHTAG
        if ($post_type == 'instagram_hashtag') {
            $keywords = $request->keywords;

            $prompt = "Write instagram hastags for $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram hashtags. Tone of voice must be $tone_of_voice";
        }

        //SOCIAL MEDIA POST TWEET
        if ($post_type == 'social_media_post_tweet') {
            $title = $request->title;

            $prompt = "Write in 1st person tweet about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tweets. Tone of voice must be $tone_of_voice";
        }

        //SOCIAL MEDIA POST BUSINESS
        if ($post_type == 'social_media_post_business') {
            $company_name = $request->company_name;
            $provide = $request->provide;
            $description = $request->description;

            $prompt = "Write in company social media post, company name: $company_name. About: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different social media posts. Tone of voice must be $tone_of_voice";
        }

        //FACEBOOK HEADLINES
        if ($post_type == 'facebook_headlines') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write Facebook ads title about title: $title. And description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads title. Tone of voice must be $tone_of_voice";
        }

        //GOOGLE ADS HEADLINES
        if ($post_type == 'google_ads_headlines') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write Google ads headline product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads headlines. Tone of voice must be $tone_of_voice";
        }

        //GOOGLE ADS DESCRIPTION
        if ($post_type == 'google_ads_description') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write google ads description product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads description. Tone of voice must be $tone_of_voice";
        }

        //CONTENT REWRITE
        if ($post_type == 'content_rewrite') {
            $contents = $request->contents;

            $prompt = "Rewrite content:  '$contents'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different rewrited content. Tone of voice must be $tone_of_voice";
        }

        //PARAGRAPH GENERATOR
        if ($post_type == 'paragraph_generator') {
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice";
        }

        //Pros & Cons
        if ($post_type == 'pros_cons') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Generate pros & cons about title:  '$title'. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different pros&cons. Tone of voice must be $tone_of_voice";
        }

        // META DESCRIPTION
        if ($post_type == 'meta_description') {
            $title = $request->title;
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate website meta description site name: $title. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different meta descriptions. Tone of voice must be $tone_of_voice";
        }

        // FAQ Generator (All datas)
        if ($post_type == 'faq_generator') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Answer like faq about subject: $title Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different faqs. Tone of voice must be $tone_of_voice";
        }

        // Email Generator
        if ($post_type == 'email_generator') {
            $subject = $request->subject;
            $description = $request->description;

            $prompt = "Write email about title: $subject, description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different emails. Tone of voice must be $tone_of_voice";
        }

        // Email Answer Generator
        if ($post_type == 'email_answer_generator') {
            $description = $request->description;

            $prompt = "answer this email content: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different email answers. Tone of voice must be $tone_of_voice";
        }

        // Newsletter Generator
        if ($post_type == 'newsletter_generator') {
            $description = $request->description;
            $subject = $request->subject;
            $title = $request->title;

            $prompt = "generate newsletter template about product_title: $title, reason: $subject description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different newsletter template. Tone of voice must be $tone_of_voice";
        }

        // Grammar Correction
        if ($post_type == 'grammar_correction') {
            $description = $request->description;

            $prompt = "Correct this to standard $language. Text is '$description'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different grammar correction. Tone of voice must be $tone_of_voice";
        }

        // TL;DR summarization
        if ($post_type == 'tldr_summarization') {
            $description = $request->description;

            $prompt = "$description. Tl;dr Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tl;dr. Tone of voice must be $tone_of_voice";
        }

        if ($post_type == 'ai_image_generator') {
            $imageParam = $request->all();
            // $description = $request->description;
            // $prompt = "$description";
            // $size = $request->size;
            // $style = $request->image_style;
            // $lighting = $request->image_lighting;
            // $mood = $request->image_mood;
            // $number_of_images = (int)$request->image_number_of_images;
        }

        if ($post_type == 'ai_code_generator') {
            $description = $request->description;
            $code_language = $request->code_language;
            $prompt = "Write a code about $description, in $code_language";
        }

        $post = OpenAIGenerator::where('slug', $post_type)->first();

        if ($post->custom_template == 1) {
            $custom_template = OpenAIGenerator::find($request->openai_id);
            $prompt = $custom_template->prompt;
            foreach (json_decode($custom_template->questions) as $question) {
                $question_name = '**'.$question->name.'**';
                $prompt = str_replace($question_name, $request[$question->name], $prompt);
            }

            $prompt .= " in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters";
        }

        if ($post->type == 'text') {
            return $this->textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user);
        }

        if ($post->type == 'code') {
            return $this->codeOutput($prompt, $post, $user);
        }

        if ($post->type == 'image') {
            return $this->imageOutput($imageParam, $post, $user);
        }

        if ($post->type == 'audio') {
            $file = $request->file('file');

            return $this->audioOutput($file, $post, $user);
        }
    }

    public function textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user)
    {
        $user = Auth::user();
        if ($user->remaining_words <= 0 and $user->remaining_words != -1) {
            $data = [
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }
        $entry = new UserOpenai();
        $entry->title = __('New Workbook');
        $entry->slug = str()->random(7).str($user->fullName())->slug().'-workbook';
        $entry->user_id = Auth::id();
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = null;
        $entry->output = null;
        $entry->hash = str()->random(256);
        $entry->credits = 0;
        $entry->words = 0;
        $entry->save();

        $message_id = $entry->id;
        $workbook = $entry;
        $inputPrompt = $prompt;

        return response()->json(compact('message_id', 'creativity', 'maximum_length', 'number_of_results', 'inputPrompt', 'workbook'));
    }

    public function codeOutput($prompt, $post, $user)
    {
        if ($this->settings->openai_default_model == 'text-davinci-003') {
            $response = FacadesOpenAI::completions()->create([
                'model' => $this->settings->openai_default_model,
                'prompt' => $prompt,
                'max_tokens' => (int) $this->settings->openai_max_output_length,
            ]);
        } else {
            $response = FacadesOpenAI::chat()->create([
                'model' => $this->settings->openai_default_model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
        }
        $total_used_tokens = $response->usage->totalTokens;

        $entry = new UserOpenai();
        $entry->title = __('New Workbook');
        $entry->slug = Str::random(7).Str::slug($user->fullName()).'-workbook';
        $entry->user_id = Auth::id();
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = json_encode($response->toArray());

        if ($this->settings->openai_default_model == 'text-davinci-003') {
            $entry->output = $response['choices'][0]['text'];
            $total_used_tokens = countWords($entry->output);
        } else {
            $entry->output = $response->choices[0]->message->content;
            $total_used_tokens = countWords($entry->output);
        }

        $entry->hash = Str::random(256);
        $entry->credits = $total_used_tokens;
        $entry->words = 0;
        $entry->save();

        $user = Auth::user();
        if ($user->remaining_words != -1) {
            $user->remaining_words -= $total_used_tokens;
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
        }
        $user->save();

        return response()->json(compact('post', 'entry'));
        //return $this->finalizeOutput($post, $entry);
    }

    public function imageOutput($param, $post, $user)
    {
        //save generated image datas
        $entries = [];

        if ($user->remaining_images <= 0 and $user->remaining_images != -1) {
            $data = [
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }

        $prompt = '';
        $image_generator = $param['image_generator'];
        $number_of_images = (int) $param['image_number_of_images'];
        $mood = $param['image_mood'];
        $size = $param['size'];

        if ($image_generator != self::STABLEDIFFUSION) {
            $description = $param['description'];
            $prompt = "$description";
            $style = $param['image_style'];
            $lighting = $param['image_lighting'];

            if ($style != null) {
                $prompt .= ' '.$style.' style.';
            }
            if ($lighting != null) {
                $prompt .= ' '.$lighting.' lighting.';
            }
            if ($mood != null) {
                $prompt .= ' '.$mood.' mood.';
            }
        } else {
            $stable_type = $param['type'];
            $prompt = $param['stable_description'];
            $negative_prompt = $param['negative_prompt'];
            $style_preset = $param['style_preset'];
            $sampler = $param['sampler'];
            $clip_guidance_preset = $param['clip_guidance_preset'];
            $image_resolution = $param['image_resolution'];
            $init_image = $param['image_src'] ?? null;
        }

        $image_storage = $this->settings_two->ai_image_storage;

        for ($i = 0; $i < $number_of_images; $i++) {
            if ($image_generator != self::STABLEDIFFUSION) {
                //send prompt to openai
                if ($prompt == null) {
                    return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
                }
                $response = FacadesOpenAI::images()->create([
                    'prompt' => $prompt,
                    'size' => $size,
                    'response_format' => 'b64_json',
                ]);
                $image_url = $response['data'][0]['b64_json'];
                $contents = base64_decode($image_url);
                $nameOfImage = Str::random(12).'-DALL-E-'.Str::slug($prompt).'.png';

                //save file on local storage or aws s3
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = 'uploads/'.$nameOfImage;
            } else {
                //send prompt to stablediffusion
                $settings = SettingTwo::first();
                $stablediffusionKeys = explode(',', $settings->stable_diffusion_api_key);
                $stablediffusionKey = $stablediffusionKeys[array_rand($stablediffusionKeys)];
                if ($prompt == null) {
                    return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
                }
                if ($stablediffusionKey == '') {
                    return response()->json(['status' => 'error', 'message' => 'You must provide a StableDiffusion API Key.']);
                }
                $width = intval(explode('x', $image_resolution)[0]);
                $height = intval(explode('x', $image_resolution)[1]);
                $client = new Client([
                    'base_uri' => 'https://api.stability.ai/v1/generation/',
                    'headers' => [
                        'content-type' => ($stable_type == 'upscale' || $stable_type == 'image-to-image') ? 'multipart/form-data' : 'application/json',
                        'Authorization' => 'Bearer '.$stablediffusionKey,
                    ],
                ]);

                // Stablediffusion engine
                $engine = $this->settings_two->stablediffusion_default_model;
                // Content Type
                $content_type = 'json';

                $payload = [
                    'cfg_scale' => 7,
                    'clip_guidance_preset' => $clip_guidance_preset ?? 'NONE',
                    'samples' => 1,
                    'steps' => 50,
                ];

                if ($sampler) {
                    $payload['sampler'] = $sampler;
                }

                if ($style_preset) {
                    $payload['style_preset'] = $style_preset;
                }

                switch ($stable_type) {
                    case 'multi-prompt':
                        $stable_url = 'text-to-image';
                        $payload['width'] = $width;
                        $payload['height'] = $height;
                        $arr = [];
                        foreach ($prompt as $p) {
                            $arr[] = [
                                'text' => $p.($mood == null ? '' : (' '.$mood.' mood.')),
                                'weight' => 1,
                            ];
                        }
                        $prompt = $arr;
                        break;
                    case 'upscale':
                        $stable_url = 'image-to-image/upscale';
                        $engine = 'esrgan-v1-x2plus';
                        $payload = [];
                        $payload['image'] = $init_image->get();
                        $prompt = [
                            [
                                'text' => $prompt.'-'.Str::random(16),
                                'weight' => 1,
                            ],
                        ];
                        $content_type = 'multipart';
                        break;
                    case 'image-to-image':
                        $stable_url = $stable_type;
                        $payload['init_image'] = $init_image->get();
                        $prompt = [
                            [
                                'text' => $prompt.($mood == null ? '' : (' '.$mood.' mood.')),
                                'weight' => 1,
                            ],
                        ];
                        $content_type = 'multipart';
                        break;
                    default:
                        $stable_url = $stable_type;
                        $payload['width'] = $width;
                        $payload['height'] = $height;
                        $prompt = [
                            [
                                'text' => $prompt.($mood == null ? '' : (' '.$mood.' mood.')),
                                'weight' => 1,
                            ],
                        ];
                        break;
                }

                if ($negative_prompt) {
                    $prompt[] = ['text' => $negative_prompt, 'weight' => -1];
                }

                if ($stable_type != 'upscale') {
                    $payload['text_prompts'] = $prompt;
                }

                if ($content_type == 'multipart') {
                    $multipart = [];
                    foreach ($payload as $key => $value) {
                        if (! is_array($value)) {
                            $multipart[] = ['name' => $key, 'contents' => $value];

                            continue;
                        }

                        foreach ($value as $multiKey => $multiValue) {
                            $multiName = $key.'['.$multiKey.']'.(is_array($multiValue) ? '['.key($multiValue).']' : '').'';
                            $multipart[] = ['name' => $multiName, 'contents' => (is_array($multiValue) ? reset($multiValue) : $multiValue)];
                        }
                    }
                    $payload = $multipart;
                }

                try {
                    $response = $client->post("$engine/$stable_url", [
                        $content_type => $payload,
                    ]);
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $statusCode = $response->getStatusCode();
                        // Custom handling for specific status codes here...

                        if ($statusCode == '404') {
                            // Handle a not found error
                        } elseif ($statusCode == '500') {
                            // Handle a server error
                        }

                        $errorMessage = $response->getBody()->getContents();

                        return response()->json(['status' => 'error', 'message' => json_decode($errorMessage)->message]);
                        // Log the error message or handle it as required
                    }

                    return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                } catch (Exception $e) {
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                }
                $body = $response->getBody();
                if ($response->getStatusCode() == 200) {
                    $nameOfImage = Str::random(12).'-'.Str::slug($prompt[0]['text']).'.png';

                    $contents = base64_decode(json_decode($body)->artifacts[0]->base64);
                } else {
                    $message = '';
                    if ($body->status == 'error') {
                        $message = $body->message;
                    } else {
                        $message = 'Failed, Try Again';
                    }

                    return response()->json(['status' => 'error', 'message' => $message]);
                }

                Storage::disk('public')->put($nameOfImage, $contents);
                $path = 'uploads/'.$nameOfImage;
            }

            if ($image_storage == self::STORAGE_S3) {
                try {
                    $uploadedFile = new File($path);
                    $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                    unlink($path);
                    $path = Storage::disk('s3')->url($aws_path);
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
                }
            }

            $entry = new UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7).Str::slug($user->fullName()).'-workbsook';
            $entry->user_id = Auth::id();
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            if ($image_generator == self::STABLEDIFFUSION) {
                $entry->input = $prompt[0]['text'];
            } else {
                $entry->input = $prompt;
            }
            // $entry->input = $prompt[0]['text'];
            $entry->response = $image_generator == 'stablediffusion' ? 'SD' : 'DE';
            $entry->output = $image_storage == self::STORAGE_S3 ? $path : '/'.$path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = $image_storage == self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::find($post->id);

                return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $image_storage]);
            }
        }

        return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $image_storage]);
    }

    public function audioOutput($file, $post, $user)
    {

        $path = 'upload/audio/';

        $file_name = Str::random(4).'-'.Str::slug($user->fullName()).'-audio.'.$file->getClientOriginalExtension();

        //Audio Extension Control
        $imageTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json($data, 419);
        }

        $file->move($path, $file_name);

        $response = FacadesOpenAI::audio()->transcribe([
            'file' => fopen($path.$file_name, 'r'),
            'model' => 'whisper-1',
            'response_format' => 'verbose_json',
        ]);

        $text = $response->text;

        $entry = new UserOpenai();
        $entry->title = __('New Workbook');
        $entry->slug = Str::random(7).Str::slug($user->fullName()).'-speech-to-text-workbook';
        $entry->user_id = Auth::id();
        $entry->openai_id = $post->id;
        $entry->input = $path.$file_name;
        $entry->response = json_encode($response->toArray());
        $entry->output = $text;
        $entry->hash = Str::random(256);
        $entry->credits = countWords($text);
        $entry->words = countWords($text);
        $entry->save();

        if ($user->remaining_words != -1 and $user->remaining_words - $entry->credits < -1) {
            $user->remaining_words = 0;
        }

        if ($user->remaining_words != -1 and $user->remaining_words - $entry->credits > 0) {
            $user->remaining_words -= $entry->credits;
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
        }

        $user->save();

        //Workbook add-on
        $workbook = $entry;

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);

        // $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();
        return response()->json(compact('userOpenai', 'openai'));
    }

    public function finalizeOutput($post, $entry) // not used. see line 546
    {
        //Workbook add-on
        $workbook = $entry;
        $html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html', 'html2'));
    }
}