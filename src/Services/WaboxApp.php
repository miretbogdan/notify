<?php

namespace Baronet\Notify\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Baronet\Notify\Classes\Helpers;

class WaboxApp
{
    const CHAT_ENDPOINT = 'https://www.waboxapp.com/api/send/chat';
    const IMAGE_ENDPOINT = 'https://www.waboxapp.com/api/send/image';
    const LINK_ENDPOINT = 'https://www.waboxapp.com/api/send/link';
    const MEDIA_ENDPOINT = 'https://www.waboxapp.com/api/send/media';
    const VERIFY_ENDPOINT = 'https://www.waboxapp.com/api/status/';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    const ERROR_CODES = [403];

    protected $client;
    protected $params = [];

    public function __construct(Client $client, $token, $uid)
    {
        $this->params = [
            'token' => $token,
            'uid' => $uid,
            'custom_uid' => 'msg-' . Str::random(15),
        ];

        $this->client = $client;
    }

    public function checkAccount()
    {
        return $this->postAsync(self::VERIFY_ENDPOINT . $this->params['uid'], [
            'token' => $this->params['token']
        ]);
    }

    public function sendText($to, $from, $message, $args = [])
    {
        $params = array_merge(
            $this->params,
            [
                'uid' => $from,
                'text' => $message,
                'to' => $to,
            ],
            $args
        );

        return $this->postAsync(self::CHAT_ENDPOINT, $params);
    }

    public function sendImage($to, $from, $url, $args = [])
    {
        $params = array_merge(
            $this->params,
            [
                'uid' => $from,
                'to' => $to,
                'url' => $url,
            ],
            $args
        );

        return $this->postAsync(self::IMAGE_ENDPOINT, $params);
    }

    public function sendLink($to, $from, $url, $args = [])
    {
        $params = array_merge(
            $this->params,
            [
                'uid' => $from,
                'to' => $to,
                'url' => $url,
            ],
            $args
        );

        return $this->postAsync(self::LINK_ENDPOINT, $params);
    }

    public function sendMedia($to, $from, $url, $args = [])
    {
        $params = array_merge(
            $this->params,
            [
                'uid' => $from,
                'to' => $to,
                'url' => $url,
            ],
            $args
        );


        return $this->postAsync(self::MEDIA_ENDPOINT, $params);
    }

    protected function postAsync($endpoint, $params)
    {
        $promise = $this->client->postAsync($endpoint, [
            'form_params' => $params
        ])->then(
            function ($response) {
                return [
                    'status' => self::STATUS_SUCCESS,
                    'response' => $response
                ];
            },
            function (\Exception $exception) {
                $code = $exception->getCode();

                if (in_array($code, self::ERROR_CODES)) {
                    $message = 'Authentication Error while trying to send Waboxapp notification. Account inactive due to funds, wrong api keys, violations or other. Code: ' . $code . ' . Check Waboxapp account: https://www.waboxapp.com';
                } else {
                    $message = $e->getMessage() . ' - ' . __METHOD__ . ' : ' . __LINE__;
                }

                Helpers::sendExceptionNotification($message, $exception);

                return [
                    'status' => self::STATUS_FAILED,
                    'response' => [
                        'message' => $message,
                        'code' => $code,
                    ]
                ];
            }
        );

        return $promise->wait();
    }
}
