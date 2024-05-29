<?php

namespace App\Repositories;

use App\Helpers\Classes\Helper;
use App\Models\Extension;
use App\Repositories\Contracts\ExtensionRepositoryInterface;
use Illuminate\Support\Facades\Http;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepository;

class ExtensionRepository implements ExtensionRepositoryInterface
{
    public const API_URL = 'https://magicmarket.projecthub.ai/api/';

    public function licensed(array $data): array
    {
        return collect($data)->filter(fn ($extension) => $extension['licensed'])->toArray();
    }

    public function extensions()
    {
        return $this->all();
    }

    public function themes()
    {
        return $this->all(true);
    }

    public function all(bool $isTheme =false)
    {
        $response = $this->request('get','extension', [
            'is_theme' => $isTheme,
//            'is_beta' => true
        ]);

        if ($response->ok()) {

            $data = $response->json('data');

            if (count($data) === $this->dbExtensionCount($isTheme)) {
                return $this->mergedInstalled($data);
            }

            $this->updateExtensionsTable($data);

            return $this->mergedInstalled($data);
        }

        return [];
    }

    public function find(string $slug)
    {
        $response = $this->request('get', "extension/{$slug}");

        if ($response->ok()) {

            $data = $response->json('data');

            $extension = Extension::query()->firstWhere('slug', $slug);

            return array_merge($data, [
                'db_version' => $extension?->version,
                'installed' => (bool) $extension?->installed,
                'upgradable' => $extension?->version !== $data['version'],
            ]);
        }

        return [];
    }

    public function install(string $slug, string $version)
    {
        return $this->request('post', "extension/{$slug}/install/{$version}");
    }

    public function request(string $method, string $route, array $body = [], $fullUrl = null)
    {
        $fullUrl  = $fullUrl ?? self::API_URL . $route;

        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-domain' => request()->getHost(),
            'x-domain-key' => $this->domainKey(),
            'x-license-type' => $this->licenseType(),
            'x-app-key' => $this->appKey()
        ])->when($method === 'post', function ($http) use ($fullUrl, $body) {
            return $http->post($fullUrl, $body);
        }, function ($http) use ($fullUrl, $body) {
            return $http->get($fullUrl, $body);
        });
    }

    public function mergedInstalled(array $data): array
    {
        $extensions = Extension::query()->get();

        return collect($data)->map(function ($extension) use ($extensions) {
            $value = $extensions->firstWhere('slug', $extension['slug']);

            return array_merge($extension, [
                'db_version' => $value?->version,
                'installed' => (bool) $value?->installed,
                'upgradable' => $value?->version !== $extension['version'],
            ]);
        })->toArray();
    }

    private function updateExtensionsTable(array $data): void
    {
        foreach ($data as $extension) {
            Extension::query()->firstOrCreate([
                'slug' => $extension['slug'],
                'is_theme' => $extension['is_theme'],
            ], [
                'version' => $extension['version'],
            ]);
        }
    }

    private function dbExtensionCount(bool $isTheme =false): int
    {
        return Extension::query()
            ->where('is_theme', $isTheme)
            ->count();
    }

    public function appKey()
    {
        return md5(config('app.key'));
    }

    public function licenseType()
    {
        return app(ApplicationStatusRepository::class)->getVariable('liquid_license_type') ?: Helper::settingTwo('liquid_license_type');
    }

    public function domainKey()
    {
        return app(ApplicationStatusRepository::class)->getVariable('liquid_license_domain_key') ?: Helper::settingTwo('liquid_license_domain_key');
    }
}