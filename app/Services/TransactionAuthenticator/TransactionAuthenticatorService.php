<?php

namespace App\Services\TransactionAuthenticator;

use App\Interfaces\Services\TransactionAuthenticator\TransactionAuthenticatorServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class TransactionAuthenticatorService implements TransactionAuthenticatorServiceInterface
{
    private const BASE_URI = 'https://run.mocky.io';
    private const ENDPOINT = 'v3/5794d450-d2e2-4412-8131-73d0293ac1cc';

    public function __construct(private Client $client)
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
        ]);
    }

    public function authorizeTransaction(): bool
    {
        try {
            $response = $this->client->request('GET', self::ENDPOINT);

            $body = json_decode($response->getBody());

            return $body->message === 'Autorizado';
        } catch (GuzzleException $exception) {
            Log::critical("[It was not possible to authorize the transaction, please read the error message !]", [
                'message' => $exception->getMessage()
            ]);
            
            return false;
        }
    }
}
