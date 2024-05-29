<?php

namespace App\Services;


class GatewaySelector 
{

    public static function selectGateway($gateway, $error = 'Something went wrong..')
    {
        $service = config("services.{$gateway}.class");

        if ($service) {
            return resolve($service);
        }

        throw new \Exception($error);
    }

}