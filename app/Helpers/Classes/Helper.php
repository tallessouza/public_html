<?php

namespace App\Helpers\Classes;

use App\Models\RateLimit;
use App\Models\Setting;
use App\Models\SettingTwo;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Helper
{
    public static function arrayMerge($merge = true, array $array = [], array ...$mergeArray): array
    {
        if ($merge) {
            return array_merge($array, ...$mergeArray);
        }

        return $array;
    }


    public static function routeWithoutTranslate($route = null)
    {
        if (is_null($route)) {
            return '/';
        }

        // URL'yi ayrıştır ve path bölümünü al
        $parsedUrl = parse_url($route);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

        // Path'i "/" ile böl
        $pathParts = explode('/', $path);

        // Belirli bir alt dizinden sonra gelen kısmı bul
        $start = array_search('dashboard', $pathParts);

        if ($start !== false) {
            // İstenen kısmı dizi olarak al
            $specificPathParts = array_slice($pathParts, $start);
            // Dizi elemanlarını birleştirerek string haline getir
            return '/' . implode('/', $specificPathParts);
        }

        return '/'; // Aranan kısım bulunamazsa boş döndür
    }

    public static function decodePaymentToken(string $token): array
    {
        $base64 = base64_decode($token);

        $data = explode(':', $base64);

        [$domain, $slug, $license] = $data;

        return compact('domain', 'slug', 'license');
    }

    public static function setEnv(array $data = []): void
    {
        $envPath = base_path('.env');

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $keyPattern = preg_quote($key, '/');
            $pattern = "/^{$keyPattern}=[^\r\n]*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}=" . '"' . "{$value}" . '"', $content);
            } else {
                $content .= "\n{$key}=" . '"' . "{$value}" . '"';
            }
        }

        file_put_contents($envPath, $content);
    }

    public static function dbConnectionStatus(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function sortingOpenAiSelected($data, $selected = null)
    {
        $data = $data->map(function ($item) use ($selected) {
            $item->selected = 0;
            if ($selected) {
                if (in_array($item->slug, $selected)) {
                    $item->selected = 1;
                }
            }

            return $item;
        });

        return $data->sortByDesc('selected');
    }


    public static function setAnthropicKey(): string
    {
        $settings = Setting::query()->first();

        $apiKeys = [];

        if ($settings?->getAttribute('user_api_option')) {
            $apiKeys = explode(',', auth()->user()?->getAttribute('anthropic_api_keys'));
        } else {
            $apiKeys = explode(',', setting('anthropic_api_secret'));
        }

        return Arr::random($apiKeys);
    }

    public static function setOpenAiKey(): string
    {
        $settings = Setting::query()->first();

        if ($settings?->getAttribute('user_api_option')) {
            $apiKeys = explode(',', auth()->user()?->getAttribute('api_keys'));
        } else {
            $apiKeys = explode(',', $settings?->getAttribute('openai_api_secret'));
        }

        config(['openai.api_key' => $apiKeys[array_rand($apiKeys)]]);

        return config('openai.api_key');
    }

    public static function setGeminiKey(): string
    {
        $settings = Setting::query()->first();
        if ($settings?->getAttribute('user_api_option')) {
            $apiKeys = explode(',', auth()->user()?->getAttribute('gemini_api_keys'));
        } else {
            $apiKeys = explode(',', setting('gemini_api_secret', ''));
        }
        config(['gemini.api_key' => $apiKeys[array_rand($apiKeys)]]);
        config(['gemini.request_timeout' => 120]);
        return config('gemini.api_key');
    }

    public static function strip_all_tags($text, $remove_breaks = false)
    {
        if (is_null($text)) {
            return '';
        }

        if (!is_scalar($text)) {
            /*
             * To maintain consistency with pre-PHP 8 error levels,
             * trigger_error() is used to trigger an E_USER_WARNING,
             * rather than _doing_it_wrong(), which triggers an E_USER_NOTICE.
             */
            trigger_error(
                sprintf(
                /* translators: 1: The function name, 2: The argument number, 3: The argument name, 4: The expected type, 5: The provided type. */
                    __('Warning: %1$s expects parameter %2$s (%3$s) to be a %4$s, %5$s given.'),
                    __FUNCTION__,
                    '#1',
                    '$text',
                    'string',
                    gettype($text)
                ),
                E_USER_WARNING
            );

            return '';
        }

        $text = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $text);
        $text = strip_tags($text);

        if ($remove_breaks) {
            $text = preg_replace('/[\r\n\t ]+/', ' ', $text);
        }

        return trim($text);
    }

    public static function multi_explode($delimiters, $string): array
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);

        $ready = str_replace([',,'], ',', $ready);

        $ready = str_replace('-', '', $ready);

        return explode($delimiters[0], $ready);
    }

    public static function settingTwo(string $key, $default = null)
    {
        $setting = SettingTwo::query()->first();

        return $setting?->getAttribute($key) ?? $default;
    }

    public static function setting(string $key, $default = null)
    {
        $setting = Setting::query()->first();

        return $setting?->getAttribute($key) ?? $default;
    }

    public static function appIsDemo(): bool
    {
        return config('app.status') == 'Demo';
    }

    public static function appIsNotDemo(): bool
    {
        return config('app.status') != 'Demo';
    }

    public static function checkImageDailyLimit()
    {
        $settings_two = SettingTwo::first();
        if ($settings_two->daily_limit_enabled) {
            if (Helper::appIsDemo()) {
                $msg = __('You have reached the maximum number of image generation allowed on the demo.');
            } else {
                $msg = __('You have reached the maximum number of image generation allowed.');
            }
            $ipAddress = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();
            $db_ip_address = RateLimit::where('ip_address', $ipAddress)->where('type', 'image')->first();
            if ($db_ip_address) {
                if (now()->diffInDays(Carbon::parse($db_ip_address->last_attempt_at)->format('Y-m-d')) > 0) {
                    $db_ip_address->attempts = 0;
                }
            } else {
                $db_ip_address = new RateLimit(['ip_address' => $ipAddress]);
            }

            if ($db_ip_address->attempts >= $settings_two->allowed_images_count) {
                $data = [
                    'errors' => [$msg],
                ];

                return response()->json($data, 429);
            } else {
                $db_ip_address->attempts++;
                $db_ip_address->last_attempt_at = now();
                $db_ip_address->save();
            }
        }

        return response()->json([], 200);
    }

    public static function checkRemainingImages($usr = null)
    {
        $user = $usr ?? auth()->user();
        if ($user->getAttribute('team')) {
            $teamManager = $user->teamManager;
            if ($teamManager) {
                if ($teamManager->remaining_images <= 0 and $teamManager->remaining_images != -1) {
                    $data = [
                        'errors' => ['You have no credits left. Please consider upgrading your plan.'],
                    ];

                    return response()->json($data, 429);
                }
            }
            $member = $user->teamMember;
            if ($member) {
                if (!$member->allow_unlimited_credits) {
                    if ($member->remaining_images <= 0 and $member->remaining_images != -1) {
                        $data = [
                            'errors' => ['You have no credits left. Please consider upgrading your plan.'],
                        ];

                        return response()->json($data, 429);
                    }
                }
            }
        } else {
            if ($user->remaining_images <= 0 and $user->remaining_images != -1) {
                $data = [
                    'errors' => ['You have no credits left. Please consider upgrading your plan.'],
                ];

                return response()->json($data, 429);
            }
        }

        return response()->json([], 200);
    }

    public static function sorting(array $data, $column, $direction)
    {
        if ($column) {
            $data = collect($data)->sortBy($column, SORT_REGULAR, $direction == 'asc');
        }

        return $data;
    }
}