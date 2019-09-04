<?php

namespace App\Traits\Jobs;

use App\WebhookEndpoint;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait WebhookRequest
{
    /**
     * Request to Front API
     *
     * @param array           $webhookData
     * @param WebhookEndpoint $endpoint
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function request(array $webhookData, WebhookEndpoint $endpoint): array
    {
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);

        try {
            $payloadJson = json_encode($webhookData);
            $response = $client->request('POST', $endpoint->url, [
                'headers' => [
                    'X-Auth-Key' => $this->getSignature($payloadJson, $endpoint->secret_key)
                ],
                'body'    => $payloadJson
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
        } catch (RequestException $exception) {
            $statusCode = $exception->getCode();
            $body = $exception->getResponse()->getBody();
        }

        $contents = $body->read(500);

        if ($body->getSize() > 500) {
            $contents .= ' (truncated...)';
        }

        return [
            'status' => $statusCode,
            'body'   => $contents
        ];
    }

    /**
     * @param string $payloadJson
     * @param string $secretKey
     * @return string
     */
    private function getSignature(string $payloadJson, string $secretKey)
    {
        return base64_encode(hash_hmac('sha256', $payloadJson, $secretKey, true));
    }
}