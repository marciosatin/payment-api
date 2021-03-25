<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class NotificationService
{
    const NOTIFICATION_SUCCESS = 'Enviado';

    public static function send(User $payee)
    {
        $response = Http::get('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04');
        $status = $response->status();
        $body = $response->json();
        $sendNotification = (($status == Response::HTTP_OK) && (isset($body['message']) && $body['message'] == self::NOTIFICATION_SUCCESS));

        if (!$sendNotification) {
            //Add job queue
        }
    }

}