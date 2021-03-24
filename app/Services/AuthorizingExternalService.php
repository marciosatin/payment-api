<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class AuthorizingExternalService
{
    const AUTHORIZED = 'Autorizado';

    public static function isAuthorized()
    {
        $response = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
        $status = $response->status();
        $body = $response->json();
        if ($status == Response::HTTP_OK && isset($body['message']) && $body['message'] == self::AUTHORIZED) {
            return true;
        }
        return false;
    }

}