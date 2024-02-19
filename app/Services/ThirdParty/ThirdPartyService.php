<?php

namespace App\Services\ThirdParty;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ThirdPartyService
{
    public function __construct(private Client $client)
    {
        $this->client = new Client([
            'base_uri' => 'https://run.mocky.io'
        ]);
    }

    public function authorizeTransaction(): bool
    {
        $endpoint = 'v3/5794d450-d2e2-4412-8131-73d0293ac1cc';
        try {
            $response = $this->client->request('GET', $endpoint);

            $body = json_decode($response->getBody());

            return $body->message === 'Autorizado';
        } catch (GuzzleException $exception) {
            return false;
        }
    }

    public function authorizeNotification(): bool
    {
        $endpoint = 'v3/54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6';
        try {
            $response = $this->client->request('GET', $endpoint);

            $body = json_decode($response->getBody());
            return $body->message === true;
        } catch (GuzzleException $exception) {
            return false;
        }
    }
}
