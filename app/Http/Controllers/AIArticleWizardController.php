<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\ArticleWizard;
use App\Models\OpenAIGenerator;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Usage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;


class AIArticleWizardController extends Controller
{
    protected $client;
    protected $settings;
	protected $settings_two;

    const STABLEDIFFUSION = 'stablediffusion';
    const STORAGE_S3 = 's3';
    const STORAGE_LOCAL = 'public';
	const CLOUDFLARE_R2 = 'r2';

    public function __construct()
    {
		$this->middleware(function (Request $request, $next) {
			Helper::setOpenAiKey();
            return $next($request);
        });
		//Settings
		$this->settings = Setting::first();
		$this->settings_two = SettingTwo::first();
		ini_set('max_execution_time', 120000);
    }

    public function index()
    {
        $wizards = ArticleWizard::select('id', 'title', 'created_at', 'generated_count', 'current_step', 'id')
            ->orderBy('id', 'asc')
            ->get();

        return view('panel.user.article_wizard.list', compact('wizards'));
    }

    /**
     * Create new article and return article id | not rec
     */
    public function newArticle(Request $request)
    {
        abort_if(Helper::setting('feature_ai_article_wizard') == 0, 404);

        $user_id = Auth::id();

        $wizard = ArticleWizard::where('user_id', $user_id)->where('current_step', '!=', 4)->first();

        if (! $wizard) {

            $records = ArticleWizard::where('user_id', $user_id)->get();
            foreach ($records as $record) {
                $extraImages = json_decode($record->extra_images, true);
                if ($extraImages != null) {
                    foreach ($extraImages as $extraImage) {
                        if (json_decode($record->image) != $extraImage['path']) {
                            if (($extraImage['storage'] ?? '') == self::STORAGE_S3) {
                                Storage::disk(self::STORAGE_S3)->delete(basename($extraImage['path']));
                            } else {
                                if (file_exists(substr($extraImage['path'], 1))) {
                                    unlink(substr($extraImage['path'], 1));
                                }
                            }
                        }
                    }
                }
            }

            ArticleWizard::where('user_id', $user_id)->delete();

            $wizard = new ArticleWizard();
            $wizard->user_id = $user_id;
            $wizard->current_step = 0;
            $wizard->keywords = '';
            $wizard->extra_keywords = '';
            $wizard->topic_keywords = '';
            $wizard->title = '';
            $wizard->extra_titles = '';
            $wizard->topic_title = '';
            $wizard->outline = '';
            $wizard->extra_outlines = '';
            $wizard->topic_outline = '';
            $wizard->result = '';
            $wizard->image = '';
            $wizard->extra_images = '';
            $wizard->topic_image = '';
            $wizard->save();
        }

        $wizard = ArticleWizard::find($wizard->id);
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
		if ($this->settings_two->openai_default_stream_server == 'backend') {
			$apikeyPart1 = base64_encode(rand(1, 100));
			$apikeyPart2 = base64_encode(rand(1, 100));
			$apikeyPart3 = base64_encode(rand(1, 100));
		}else{
			$settings = Setting::first();
			// Fetch the Site Settings object with openai_api_secret
			if ($this->settings?->user_api_option) {
				$apiKeys = explode(',', auth()->user()?->api_keys);
			} else {
				$apiKeys = explode(',', $this->settings?->openai_api_secret);
			}
			$apiKey = $apiKeys[array_rand($apiKeys)];

			$len = strlen($apiKey);

			$parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
			$parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
			$parts[] = substr($apiKey, array_sum($l));
			$apikeyPart1 = base64_encode($parts[0]);
			$apikeyPart2 = base64_encode($parts[1]);
			$apikeyPart3 = base64_encode($parts[2]);
		}

        return view('panel.user.article_wizard.wizard', compact(
            'wizard',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl'
        ));
    }

    // | not rec
    public function clearArticle(Request $request)
    {
        $user_id = Auth::id();
        $records = ArticleWizard::where('user_id', $user_id)->get();
        foreach ($records as $record) {
            $extraImages = json_decode($record->extra_images, true);
            if ($extraImages != null) {
                foreach ($extraImages as $extraImage) {
                    if ($record->image != $extraImage['path']) {
                        if (($extraImage['storage'] ?? '') == self::STORAGE_S3) {
                            Storage::disk(self::STORAGE_S3)->delete(basename($extraImage['path']));
                        } else {
                            Storage::disk(self::STORAGE_LOCAL)->delete(substr($extraImage['path'], 1));
                        }
                    }
                }
            }
        }
        ArticleWizard::where('user_id', Auth::id())->delete();

        return response()->json('success');
    }

    /** # | not rec
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $wizard = ArticleWizard::find($id);

        return view('panel.user.article_wizard.wizard', compact('wizard'));
    }

    // | not rec
    public function editArticle(string $id)
    {
        $wizard = ArticleWizard::find($id);

        return view('panel.user.article_wizard.wizard', compact('wizard'));
    }

    /** # | not rec
     * Generate keywords from topic
     */
    public function userRemaining(Request $request)
    {
        $user = Auth::user();

        return response()->json(['words' => $user->remaining_words, 'images' => $user->remaining_images]);
    }

    // | not rec
    public function generateKeywords(Request $request)
    {
        $user = Auth::user();
        if ($user->remaining_words <= 0 and $user->remaining_words != -1) {
            $data = [
                'message' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }
        try {
            $completion = OpenAI::chat()->create([
                'model' => $this->settings->openai_default_model,
                'messages' => [[
                    'role' => 'user',
                    'content' => "Generate $request->count keywords(simple words or 2 words, not phrase, not person name) about '$request->topic'. Must resut as array json data. in '$request->language' language. Result format is [keyword1, keyword2, ..., keywordn].  Must not write ```json",
                ]],
            ]);
            $total_used_tokens = countWords($completion['choices'][0]['message']['content']);

            $user = Auth::user();

            userCreditDecreaseForWord($user, $total_used_tokens);

            return response()->json(['result' => $completion['choices'][0]['message']['content']])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateTitles(Request $request)
    {
        $user = Auth::user();
        if ($user->remaining_words <= 0 and $user->remaining_words != -1) {
            $data = [
                'message' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }
        try {
            $prompt = "Generate $request->count titles(Maximum title length is $request->length. Must not be 'title1', 'title2', 'title3', 'title4', 'title5') about Keywords: '".$request->keywords."'. in '$request->language' language. Resut must be array json data. This is result format: [title1, title2, ..., titlen]. Maximum title length is $request->length  Must not write ```json";
            if ($request->topic != '') {
                $prompt = "Generate $request->count titles(Maximum title length is $request->length., Must not be 'title1', 'title2', 'title3', 'title4', 'title5') about Topic: '".$request->topic."'. in '$request->language' language. Resut must be array json data. This is result format: [title1, title2, ..., titlen]. Maximum title length is $request->length  Must not write ```json";
            }
            $completion = OpenAI::chat()->create([
                'model' => $this->settings->openai_default_model,
                'messages' => [[
                    'role' => 'user',
                    'content' => $prompt,
                ]],
            ]);
            $total_used_tokens = countWords($completion['choices'][0]['message']['content']);
            $user = Auth::user();

            userCreditDecreaseForWord($user, $total_used_tokens);

            return response()->json(['result' => $completion['choices'][0]['message']['content']])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateOutlines(Request $request)
    {

        $user = Auth::user();
        if ($user->remaining_words <= 0 and $user->remaining_words != -1) {
            $data = [
                'message' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }

        try {
            $prompt = "The keywords of article are $request->keywords.  Generate different outlines( Each outline must has only $request->subcount subtitles(Without number for order, subtitles are not keywords)) $request->count times. The depth is 1. in '$request->language' language. Must not write any description. Result must be json data, Every subtitle is sentence or phrase string. This is result format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], [subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], ... ,[subtitle1(string), subtitle2(string), subtitle3(string), ..., subtitle-$request->subcount (string)].  Must not write ```json";
            if ($request->topic != '') {
                $prompt = "The subject of article is $request->topic. Generate different outlines( Each outline must has only $request->subcount subtitles(Without number for order, subtitles are not keywords)) $request->count times. The depth is 1".". in '$request->language' language. Must not write any description. Result must be json data, Every subtitle is sentence or phrase string. This is result format: [[subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], [subtitle1(string), subtitle2(string), subtitle3(string), ... , subtitle-$request->subcount(string)], ... ,[subtitle1(string), subtitle2(string), subtitle3(string), ..., subtitle-$request->subcount (string)]].  Must not write ```json";
            }
            $completion = OpenAI::chat()->create([
                'model' => $this->settings->openai_default_model,
                'messages' => [[
                    'role' => 'user',
                    'content' => $prompt,
                ]],
            ]);

            $total_used_tokens = countWords($completion['choices'][0]['message']['content']);

            userCreditDecreaseForWord($user, $total_used_tokens);

            return response()->json(['result' => $completion['choices'][0]['message']['content'], 'words' => $user->remaining_words, 'images' => $user->remaining_images])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateArticle(Request $request)
    {

        $user = Auth::user();
        if ($user->remaining_words <= 0 and $user->remaining_words != -1) {
            $data = [
                'message' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }

        try {
            $wizard = ArticleWizard::find($request->id);
            $title = $wizard->title;
            $keywords = $wizard->keywords;
            $outlines = json_decode($wizard->outline, true);

            $length = $request->length;

            session_start();
            header('Content-type: text/event-stream');
            header('Cache-Control: no-cache');
            // ob_end_flush();
            $result = OpenAI::chat()->createStreamed([
                'model' => $this->settings->openai_default_model,
                'messages' => [[
                    'role' => 'user',
                    'content' => "Write Article(Maximum  $length words). in $wizard-> language. Generate article (Must not contain title, Must Mark outline with <h3> tag) about $title with following outline ".implode(',', $outlines).'Must mark outline with <h3> tag.  Must not write ```json',
                ]],
                'stream' => true,
            ]);

            foreach ($result as $response) {
                echo "event: data\n";
                echo 'data: '.json_encode(['message' => $response->choices[0]->delta->content])."\n\n";
                flush();
            }

            echo "event: stop\n";
            echo "data: stopped\n\n";
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // | not rec
    public function generateImages(Request $request)
    {
        $user = Auth::user();
        if ($user->remaining_images <= 0 and $user->remaining_images != -1) {
            $data = [
                'message' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }
        try {
            $wizard = ArticleWizard::find($request->id);

            $size = $request->size;
            $prompt = $request->prompt;
            if ($prompt == '' || $prompt == null) {
                $prompt = $wizard->topic_keywords;
            }
            $count = $request->count;

            $paths = [];

			$this->getImagesFromThirdParty($prompt, $count, $size, $paths);

            return response()->json(['status' => 'success', 'path' => $paths]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                // Unauthorized error
                if (Auth::user()->type == 'admin') {
                    return response()->json([
                        'message' => __('It seems your Unsplash API key is missing or invalid. Please go to your settings and add a valid Unsplash API key.'),
                    ], 401);
                } else {
                    return response()->json([
                        'message' => __('It seems that Unsplash API not set yet or is missing or invalid. Please submit a ticket to support.'),
                    ], 401);
                }
            } else {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 500);
            }
        }
    }

	private function getImagesFromThirdParty($prompt, $count, $size , &$paths){
		switch (setting('default_aw_image_engine', 'unsplash')) {
			case 'unsplash':
				$this->getImagesFromUnsplash($prompt, $count, $size , $paths);
				break;
			case 'pexels':
				$this->getImagesFromPexels($prompt, $count, $size , $paths);
				break;
			case 'pixabay':
				$this->getImagesFromPixabay($prompt, $count, $size , $paths);
				break;
			case 'openai':
				$this->getImagesDalle($prompt, $count, $size , $paths);
				break;
			case 'sd':
				$this->getImagesFromStableDiffusion($prompt, $count, $size , $paths);
				break;
			default:
				$this->getImagesFromUnsplash($prompt, $count, $size , $paths);
				break;
		}
	}
	private function getImagesFromUnsplash($prompt, $count, $size, &$paths){
		$user = Auth::user();
		$settings = $this->settings_two;
		$image_storage = $this->settings_two->ai_image_storage;
		$client = new Client();
		$apiKey = $settings->unsplash_api_key;
		$url = "https://api.unsplash.com/search/photos?query=$prompt&count=$count&client_id=$apiKey&orientation=landscape";
		$response = $client->request('GET', $url, [
			'headers' => [
				'Accept' => 'application/json',
			],
		]);
		$statusCode = $response->getStatusCode();
		$content = $response->getBody();
		if ($statusCode == 200) {
			$images = json_decode($content)->results;

			foreach ($images as $index => $image) {
				$image_url = $image->urls->$size;
				$imageContent = file_get_contents($image_url);
				$nameOfImage = Str::random(12).'.png';

				Storage::disk('public')->put($nameOfImage, $imageContent);
				$path = 'uploads/'.$nameOfImage;

				if ($image_storage == self::STORAGE_S3) {
					try {
						$uploadedFile = new File($path);
						$aws_path = Storage::disk('s3')->put('', $uploadedFile);
						unlink($path);
						$path = Storage::disk('s3')->url($aws_path);
					} catch (\Exception $e) {
						return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
					}
				} else {
					$path = "/$path";
				}

				array_push($paths, $path);
				
				userCreditDecreaseForImage($user, 1);
				$count = $count - 1;
				if ($count == 0) {
					break;
				}
			}
		} else {
			return response()->json([
				'status' => 'error',
				'message' => __('Failed to download images.'),
			], 500);
		}
	}
	private function getImagesFromPexels($prompt, $count, $size, &$paths){
		$user = Auth::user();
		switch ($size) {
			case 'thumb':
				$size = 'tiny';
				break;
			case 'small':
				$size = 'small';
				break;
			case 'small_s3':
				$size = 'medium';
				break;
			case 'full':
				$size = 'large';
				break;
			case 'raw':
				$size = 'original';
				break;
			default:
				$size = 'medium';
				break;
		}
		$settings = $this->settings_two;
		$image_storage = $this->settings_two->ai_image_storage;
		$client = new Client();
		$apiKey = setting('pexels_api_key');
		$url = "https://api.pexels.com/v1/search?query=$prompt&per_page=$count";
		$response = $client->request('GET', $url, [
			'headers' => [
				'Authorization' => $apiKey,
			],
		]);
		$statusCode = $response->getStatusCode();
		$content = $response->getBody();
		if ($statusCode == 200) {
			$images = json_decode($content)->photos;
			foreach ($images as $index => $image) {
				

				$image_url = $image->src->$size;
				$imageContent = file_get_contents($image_url);
				$nameOfImage = Str::random(12).'.png';

				Storage::disk('public')->put($nameOfImage, $imageContent);
				$path = 'uploads/'.$nameOfImage;

				if ($image_storage == self::STORAGE_S3) {
					try {
						$uploadedFile = new File($path);
						$aws_path = Storage::disk('s3')->put('', $uploadedFile);
						unlink($path);
						$path = Storage::disk('s3')->url($aws_path);
					} catch (\Exception $e) {
						return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
					}
				} else {
					$path = "/$path";
				}

				array_push($paths, $path);
				userCreditDecreaseForImage($user, 1);
				$count = $count - 1;
				if ($count == 0) {
					break;
				}
			}
		} else {
			return response()->json([
				'status' => 'error',
				'message' => __('Failed to download images.'),
			], 500);
		}
	}
	private function getImagesFromPixabay($prompt, $count, $size, &$paths){
		$user = Auth::user();
		$settings = $this->settings_two;
		$image_storage = $this->settings_two->ai_image_storage;
		$client = new Client();
		$apiKey = setting('pixabay_api_key');
		$url = "https://pixabay.com/api/?key=$apiKey&q=$prompt&image_type=photo&per_page=$count";
		$response = $client->request('GET', $url, [
			'headers' => [
				'Accept' => 'application/json',
			],
		]);
		$statusCode = $response->getStatusCode();
		$content = $response->getBody();
		if ($statusCode == 200) {
			$images = json_decode($content)->hits;
			foreach ($images as $index => $image) {
				$image_url = $image->webformatURL;
				$imageContent = file_get_contents($image_url);
				$nameOfImage = Str::random(12).'.png';

				Storage::disk('public')->put($nameOfImage, $imageContent);
				$path = 'uploads/'.$nameOfImage;

				if ($image_storage == self::STORAGE_S3) {
					try {
						$uploadedFile = new File($path);
						$aws_path = Storage::disk('s3')->put('', $uploadedFile);
						unlink($path);
						$path = Storage::disk('s3')->url($aws_path);
					} catch (\Exception $e) {
						return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
					}
				} else {
					$path = "/$path";
				}

				array_push($paths, $path);
				userCreditDecreaseForImage($user, 1);
				$count = $count - 1;
				if ($count == 0) {
					break;
				}
			}
		} else {
			return response()->json([
				'status' => 'error',
				'message' => __('Failed to download images.'),
			], 500);
		}
	}
	private function getImagesDalle($prompt, $count, $size, &$paths){
		$user = Auth::user();
		$count = 1;
		$settings = $this->settings_two;
		$image_storage = $this->settings_two->ai_image_storage;
		$setting = $this->settings;
		
		if ($this->settings_two->dalle == 'dalle2') {
			$model = 'dall-e-2';
		} elseif ($this->settings_two->dalle == 'dalle3') {
			$model = 'dall-e-3';
		} else {
			$model = 'dall-e-2';
		}

		$lockKey = 'generate_image_lock';
		$user = Auth::user();
		// Attempt to acquire lock
		if (!Cache::lock($lockKey, 10)->get()) {
			// Failed to acquire lock, another process is already running
			return response()->json(['message' => 'Image generation in progress. Please try again later.'], 409);
		}
		try {
			// check daily limit
			$chkLmt = Helper::checkImageDailyLimit();
			if ($chkLmt->getStatusCode() === 429) {
				return $chkLmt;
			}
			// check remainings
			$chkImg = Helper::checkRemainingImages($user);
			if ($chkImg->getStatusCode() === 429) {
				return $chkImg;
			}
			  
			switch ($size) {
				case "thumb":
					$size = $model == "dall-e-3"? "1024x1024" : "256x256";
					break;
				case "small":
					$size = $model == "dall-e-3"? "1024x1792" : "512x512";
					break;
				case "medium":
					$size = $model == "dall-e-3"? "1792x1024" : "1024x1024";
					break;
				case "large":
					$size = $model == "dall-e-3"? "1792x1024" : "1024x1024";
					break;
				case "full":
					$size = $model == "dall-e-3"? "1792x1024" : "1024x1024";
					break;
				default:
					$size = $model == "dall-e-3"? "1024x1024" : "256x256";
					break;
			}
			for ($i = 0; $i < $count; $i++) {
				if ($prompt == null) {
					return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
				}
				$quality = 'standard';
				$response = OpenAI::images()->create([
					'model' => $model,
					'prompt' => $prompt,
					'size' => $size,
					'response_format' => 'b64_json',
					'quality' => 'standard',
					'n' => 1,
				]);
				$image_url = $response['data'][0]['b64_json'];
				$contents = base64_decode($image_url);

				$nameOfImage = Str::random(12).'.png';
				Storage::disk('public')->put($nameOfImage, $contents);
				$path = 'uploads/'.$nameOfImage;

				if ($image_storage == self::STORAGE_S3) {
					try {
						$uploadedFile = new File($path);
						$aws_path = Storage::disk('s3')->put('', $uploadedFile);
						unlink($path);
						$path = Storage::disk('s3')->url($aws_path);
					} catch (\Exception $e) {
						return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
					}
				} else if ($image_storage == self::CLOUDFLARE_R2) {
					Storage::disk('r2')->put($nameOfImage, $contents);
					unlink($path);
					$path = Storage::disk('r2')->url($nameOfImage);
				} else {
					$path = "/$path";
				}

				array_push($paths, $path);
				userCreditDecreaseForImage($user, 1);
			}

			Cache::lock($lockKey)->release();
		} finally {
			Cache::lock($lockKey)->forceRelease();
		}
	}
	private function getImagesFromStableDiffusion($prompt, $count, $size, &$paths){
		$user = Auth::user();
		$count = 1;
		$settings = $this->settings_two;
		$image_storage = $this->settings_two->ai_image_storage;
		$setting = $this->settings;


		switch ($size) {
			case "thumb":
				$size = "640x1536";
				break;
			case "small":
				$size = "768x1344";
				break;
			case "medium":
				$size = "832x1216";
				break;
			case "large":
				$size = "896x1152";
				break;
			case "full":
				$size = "1024x1024";
				break;
			case "raw":
				$size = "1152x896";
				break;
			default:
				$size = "1024x1024";
				break;
		}
		
		$stablediffusionKeys = explode(',', $settings->stable_diffusion_api_key);
		$stablediffusionKey = $stablediffusionKeys[array_rand($stablediffusionKeys)];
		for ($i = 0; $i < $count; $i++) {
			if ($prompt == null) {
				return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
			}
			if ($stablediffusionKey == '') {
				return response()->json(['status' => 'error', 'message' => 'You must provide a StableDiffusion API Key.']);
			}

			$width = intval(explode('x', $size)[0]);
			$height = intval(explode('x', $size)[1]);

			// Stablediffusion engine
			$engine = $this->settings_two->stablediffusion_default_model;

			$sd3Payload = [];
			if($engine == 'sd3' || $engine == 'sd3-turbo')
			{
				$client = new Client([
					'base_uri' => 'https://api.stability.ai/v2beta/stable-image/generate/',
					'headers' => [
						'content-type' => 'multipart/form-data',
						'Authorization' => 'Bearer '.$stablediffusionKey,
						'accept' => 'application/json'
					],
				]);
			} else {
				$engine = 'stable-diffusion-xl-beta-v2-2-2';
				$client = new Client([
					'base_uri' => 'https://api.stability.ai/v1/generation/',
					'headers' => [
						'content-type' => 'application/json',
						'Authorization' => 'Bearer '. $stablediffusionKey,
						'accept' => 'application/json'
					],
				]);
			}

			// Content Type
			$content_type = 'json';

			$payload = [
				'cfg_scale' => 7,
				'clip_guidance_preset' => 'NONE',
				'samples' => 1,
				'steps' => 50,
			];

			$stable_url = "text-to-image";
			// $payload['width'] = $width;
			// $payload['height'] = $height;
			$sd3Payload = [
				[
					'name' => 'prompt',
					"contents" => $prompt
				],
				[
					'name' => 'file',
					'contents' => 'no'
				],
				[
					'name' => 'output_format',
					'contents' => 'png'
				]
			];
			$prompt = [
				[
					'text' => $prompt,
					'weight' => 1,
				],
			];
			$payload['text_prompts'] = $prompt;
			try {
				if($engine == 'sd3' || $engine == 'sd3-turbo')
				{
					$response = $client->post("$engine", [
						"headers" => [
							"accept" => "application/json",
						],
						"multipart" => $sd3Payload
					]);
				} else {
					$response = $client->post("$engine/$stable_url", [
						$content_type => $payload,
					]);
				}

			} catch (RequestException $e) {
				dd($e);
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
			}

			$body = $response->getBody();


			if ($response->getStatusCode() == 200) {
				$nameOfImage = Str::random(12).'.png';

				if($engine == 'sd3' || $engine == 'sd3-turbo') {
					$contents = base64_decode(json_decode($body)->image);
				}else {
					$contents = base64_decode(json_decode($body)->artifacts[0]->base64);
				}

				Storage::disk('public')->put($nameOfImage, $contents);
				$path = 'uploads/'.$nameOfImage;
				if ($image_storage == self::STORAGE_S3) {
					try {
						$uploadedFile = new File($path);
						$aws_path = Storage::disk('s3')->put('', $uploadedFile);
						unlink($path);
						$path = Storage::disk('s3')->url($aws_path);
					} catch (\Exception $e) {
						return response()->json(['status' => 'error', 'message' => 'AWS Error - '.$e->getMessage()]);
					}
				} else if ($image_storage == self::CLOUDFLARE_R2) {
					Storage::disk('r2')->put($nameOfImage, $contents);
					unlink($path);
					$path = Storage::disk('r2')->url($nameOfImage);
				} else {
					$path = "/$path";
				}

				array_push($paths, $path);
				userCreditDecreaseForImage($user, 1);
			} else {
				$message = '';
				if ($body->status == 'error') {
					$message = $body->message;
				} else {
					$message = 'Failed, Try Again';
				}

				return response()->json(['status' => 'error', 'message' => $message]);
			}
		}	
	}

    // | not rec
    public function updateArticle(Request $request)
    {
        $user = Auth::user();
        try {
            $data = $request->getContent();
            $decodedData = json_decode($data);

            $wizard = ArticleWizard::find($decodedData->id);
            if ($decodedData->type == 'EXTRA_KEYWORDS') {
                $wizard->extra_keywords = $decodedData->extra_keywords;
                $wizard->topic_keywords = $decodedData->topic_keywords;
            }
            if ($decodedData->type == 'EXTRA_TITLES') {
                $wizard->extra_titles = $decodedData->extra_titles;
                $wizard->topic_title = $decodedData->topic_title;
            }
            if ($decodedData->type == 'EXTRA_OUTLINES') {
                $wizard->extra_outlines = $decodedData->extra_outlines;
                $wizard->topic_outline = $decodedData->topic_outline;
            }
            if ($decodedData->type == 'EXTRA_IMAGES') {
                $wizard->extra_images = $decodedData->extra_images;
                $wizard->topic_image = $decodedData->topic_image;
            }
            if ($decodedData->type == 'KEYWORDS') {
                $wizard->keywords = $decodedData->keywords;
                $wizard->current_step = 1;
            }
            if ($decodedData->type == 'TITLE') {
                $wizard->title = $decodedData->title;
                $wizard->current_step = 2;
            }
            if ($decodedData->type == 'OUTLINE') {
                $wizard->outline = $decodedData->outline;
                $wizard->current_step = 3;
            }
            if ($decodedData->type == 'STEP') {
                $wizard->current_step = $decodedData->step;
            }
            if ($decodedData->type == 'UPDATE_STEP') {
                $wizard->current_step = $decodedData->step;
                if ($decodedData->step <= 0) {
                    $wizard->title = '';
                    $wizard->extra_titles = '';
                }
                if ($decodedData->step <= 1) {
                    $wizard->outline = '';
                    $wizard->extra_outlines = '';
                }
                if ($decodedData->step <= 2) {
                    $wizard->image = '';
                    $wizard->extra_images = '';
                }
            }
            if ($decodedData->type == 'IMAGE') {
                $wizard->image = $decodedData->image;
                $wizard->language = $decodedData?->language ?? $this->settings->openai_default_language;
                $decodedData->creativity = $decodedData?->creativity ?? $this->settings->openai_default_creativity;
                $wizard->current_step = 4;
            }

            if ($decodedData->type == 'TOKENS') {
                $total_used_tokens = $decodedData->tokens;
                $user = Auth::user();

                userCreditDecreaseForWord($user, $total_used_tokens);
            }

            if ($decodedData->type == 'RESULT') {
                $wizard->result = $decodedData->result;

                $user = Auth::user();

                $post = OpenAIGenerator::where('slug', 'ai_article_wizard_generator')->first();

                $entry = new UserOpenai();
                $entry->title = $wizard->title;
                $entry->slug = str()->random(7).str($user->fullName())->slug().'-workbook';
                $entry->user_id = Auth::id();
                $entry->openai_id = $post->id;
                $entry->input = "Write Article in $wizard-> language. Generate article about $wizard->title with must following outline $request->outline.  Please write only article.";
                $entry->hash = str()->random(256);
                $entry->credits = countWords($decodedData->result);
                $entry->words = countWords($decodedData->result);
                $entry->output = $decodedData->result;
                $entry->storage = $this->settings_two->ai_image_storage;
                $entry->response = json_decode($wizard->image);

                if ($user->remaining_words != -1) {

                    userCreditDecreaseForWord($user, countWords($decodedData->result));

                }

                $entry->save();
            }

            $wizard->save();

            return response()->json(['result' => 'success', 'remain_words' => (string) $user->remaining_words, 'remain_images' => (string) $user->remaining_images]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}