<?php

namespace App\Repositories\Contracts;

interface ExtensionRepositoryInterface extends PortalRepositoryInterface
{
    public function licensed(array $data);

    public function extensions();

    public function themes();

    public function all(bool $isTheme =false);

    public function find(string $slug);

    public function install(string $slug, string $version);

    public function request(string $method, string $route, array $body = []);
}