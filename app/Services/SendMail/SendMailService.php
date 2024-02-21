<?php

namespace App\Services\SendMail;

use App\Interfaces\Services\SendMail\SendMailServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SendMailService implements SendMailServiceInterface
{
    private const BASE_URI = 'https://run.mocky.io';
    private const ENDPOINT = 'v3/54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6';

    public function __construct(private Client $client, private Log $log)
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
        ]);
    }

    public function isNotificationServiceStable(): bool
    {
        try {
            $response = $this->client->request('GET', self::ENDPOINT);

            $body = json_decode($response->getBody());
            return $body->message === true;
        } catch (GuzzleException $exception) {
            $this->log->critical("[It was not possible to authorize the transaction, please read the error message !]", [
                'message' => $exception->getMessage()
            ]);
            
            return false;
        }
    }
}
