<?php

namespace Baronet\Notify\Channels;

use Baronet\Notify\Classes\Helpers;
use Illuminate\Notifications\Notification;

class BrowserChannel
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('browser', $notification)) {
            return;
        }

        $data = $notification->getData();

        $this->message($to, $data['message'], $data['url'] ?? '');
    }

    protected function message($to, $message, $url)
    {
        try {
            \OneSignal::async()->sendNotificationToUser(
                $message,
                $to,
                $url,
                [
                    'web_url' => $url
                ],
                null,
                null
            );

            return [
                'status' => self::STATUS_SUCCESS,
                'response' => [
                    'message' => self::STATUS_SUCCESS,
                    'code' => 200,
                ]
            ];

            return true;
        } catch (\Exception $exception) {
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
    }
}
