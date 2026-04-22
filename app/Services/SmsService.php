<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SmsService
{
    static function send(string $phone, string $message)
    {
        $prodUrl = 'https://apis.letexto.com';
        $token = env('LE_TEXTO_API_KEY');

        $data = [
            'from' => 'DOMA',
            'to' => $phone,
            'content' => $message,
            'dlrUrl' => 'https://doma.ci/api/dlr',
            'dlrMethod' => 'GET',
            'customData' => 'Aucun',
            'sendAt' => now()
        ];

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);

        try {
            $response = $client->post($prodUrl . '/v1/messages/send', [
                'json' => $data
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            echo 'Error: ' . $e->getMessage();
            if ($e->hasResponse()) {
                echo ' - ' . $e->getResponse()->getBody();
            }
        }

    }
}

