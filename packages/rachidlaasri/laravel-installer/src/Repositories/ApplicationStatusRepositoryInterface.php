<?php

namespace RachidLaasri\LaravelInstaller\Repositories;

use Illuminate\Http\Request;
use Closure;

interface ApplicationStatusRepositoryInterface
{
    public function licenseType(): ?string;

    public function check(string $licenseKey, bool $installed = false): bool;

    public function portal();

    public function getVariable(string $key);

    public function generate(Request $request): void;

    public function setLicense(): void;

    public function next($request, Closure $next);

    public function webhook($request);
}