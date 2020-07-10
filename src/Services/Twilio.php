<?php

namespace Baronet\Notify\Services;

use Twilio\Rest\Client;
use Baronet\Notify\Classes\Helpers;
use Twilio\Exceptions\ConfigurationException;

class Twilio
{
    const CALLBACK_ENDPOINT = 'https://pinkclub.cc/api/notifications/callback';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    const ERROR_CODES = [10001, 20003, 20005, 21472, 30003];

    protected $sId;
    protected $token;
    protected $sandboxCode;
    protected $sandboxNumber;
    protected $number;
    protected $client;

    public function __construct($sId, $token, $number, $sandboxCode, $sandboxNumber)
    {
        $this->sId = $sId;
        $this->token = $token;
        $this->number = $number;
        $this->sandboxCode = $sandboxCode;
        $this->sandboxNumber = $sandboxNumber;

        try {
            $this->client = new Client($this->sId, $this->token);
        } catch (ConfigurationException $exception) {
            // TO-DO: notify
        }
    }

    public function inviteToSandbox()
    {
        return 'whatsapp://send?phone=' . $this->sandboxNumber . '&text=' . urlencode($this->sandboxCode);
    }

    public function lookupPhoneNumber($phoneNumber)
    {
        return $this->client->lookups->v1->phoneNumbers($phoneNumber)->fetch();
    }

    public function sendMessage($to, $message)
    {
        try {
            $response = $this->client->messages->create(
                $to,
                [
                    'from' => $this->number,
                    'body' => $message,
                    'statusCallback' => self::CALLBACK_ENDPOINT
                ]
            );

            return [
                'status' => self::STATUS_SUCCESS,
                'response' => $response
            ];
        } catch (\Exception $exception) {
            $code = $exception->getCode();

            if (in_array($code, self::ERROR_CODES)) {
                $message = 'Authentication Error while trying to send Twilio notification. Account inactive due to funds, wrong api keys, violations or other. Code: ' . $code . ' .Check twilio docs: https://www.twilio.com/docs/api/errors/' . $code;
            } else {
                $message = $exception->getMessage() . ' - ' . __METHOD__ . ' : ' . __LINE__;
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
    }
}
