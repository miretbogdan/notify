<?php

namespace Baronet\Notify\Services;

use GuzzleHttp\Client;
use Baronet\Notify\Classes\Helpers;

class Pushed
{
    const OAUTH_ENDPOINT = 'https://api.pushed.co/1/oauth/access_token';
    const OAUTH_VERIFY_ENDPOINT = 'https://api.pushed.co/1/oauth/verify';
    const PUSH_ENDPOINT = 'https://api.pushed.co/1/push';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $params = [];
    protected $client;

    public function __construct(Client $client, $key, $secret, $alias)
    {
        $this->client = $client;

        $this->params = [
            'key' => $key,
            'secret' => $secret,
            'alias' => $alias,
        ];
    }

    public function getAccessToken($code)
    {
        return $this->client->post(self::OAUTH_ENDPOINT, [
            'form_params' => [
                'code' => $code
            ]
        ]);
    }

    public function sendMessage($to, $message, $url)
    {
        $promise = $this->client->postAsync(self::PUSH_ENDPOINT, [
            'form_params' => [
                'access_token' => $to,
                'app_key' => $this->params['key'],
                'app_secret' => $this->params['secret'],
                'content' => $message,
                'content_extra' => $url,
                'content_type' => 'url',
                'target_alias' => $this->params['alias'],
                'target_type' => 'user',
            ]
        ])->then(
            function ($response) {
                return [
                    'status' => self::STATUS_SUCCESS,
                    'response' => $response
                ];
            },
            function (\Exception $exception) {
                $message = 'Cannot send notification - ' . __METHOD__;

                Helpers::sendExceptionNotification($message, $exception);

                return [
                    'status' => self::STATUS_FAILED,
                    'response' => [
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                    ]
                ];
            }
        );

        return $promise->wait();
    }

    public function verifyAccessToken($token)
    {
        return $this->client->post(self::OAUTH_VERIFY_ENDPOINT, [
            'form_params' => [
                'access_token' => $token
            ]
        ]);
    }
}
